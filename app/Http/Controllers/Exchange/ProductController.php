<?php

namespace App\Http\Controllers\Exchange;

use App\Events\ChatSent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Chatting;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\User;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class ProductController extends Controller
{

    private $productRepository;
    private $userRepository;
    private $categoryProductRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryProductRepository $categoryProductRepository,
        UserRepository $userRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->categoryProductRepository = $categoryProductRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     */

    public function search(Request $request)
    {
        $categories = $this->categoryProductRepository->index();

        $location = $request->input('location');
        $keyword = $request->input('q');

        $products = $this->productRepository->productSearch($location, $keyword);
        // Tìm kiếm theo tên sản phẩm
        return view('exchange.product.search', compact('products', 'categories'));
    }

    public function filter(Request $request)
    {
        $categories = $this->categoryProductRepository->index();

        $filters = $request->only(['location', 'category', 'min_price', 'max_price', 'q', 'condition']);
        $products = $this->productRepository->getFilteredProducts($filters);

        return view('exchange.product.search', compact('products', 'categories'));
    }

    public function postProduct()
    {
        $categories = $this->categoryProductRepository->index();
        return view('exchange.manager-post.post-product', compact( 'categories'));
    }

    public function storePostProduct(Request $request)
    {
        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|in:new,used',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string',
            'images' => 'required|array|min:1|max:6',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tạo dữ liệu sản phẩm
        $input['slug'] = Str::slug($request->name . '-' . uniqid());
        $input['status'] = \App\Enums\Product::STATUS_POST_PENDING;
        $input['user_id'] = Auth::id();
        $input['start_date'] = now();
        $input['expires_at'] = now()->addDays(30);
        $input['name'] = $request->name;
        $input['category'] = $request->category;
        $input['description'] = $request->description;
        $input['condition'] = $request->condition;
        $input['location'] = $request->location;
        $input['price'] = $request->price;
        $input['is_sold'] = \App\Enums\Product::PRODUCT_IN_STOCK;
        // Lưu ảnh đầu tiên làm ảnh đại diện (cover)
        $imagePaths = [];

        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('/images/upload/product/'), $imageName);
            $imagePaths[] = '/images/upload/product/' . $imageName;
        }

        $input['images'] = json_encode($imagePaths);
        // Tạo sản phẩm
        $product = $this->productRepository->store($input);

        // (Tùy chọn) Nếu bạn muốn lưu thêm nhiều ảnh phụ vào bảng khác thì xử lý tại đây (hiện tại bạn không cần)

        return redirect()->route('exchange.managerPosts')->with('success', 'Post news created success!');
    }

    public function managerPosts(Request $request)
    {
        $user = Auth::user();
        $categories = $this->categoryProductRepository->index();
        $getNewsByStatus = $request->get('status');
        $keyword = $request->input('q');

        // Nếu có từ khóa, thì ưu tiên hiển thị kết quả tìm kiếm
        if ($keyword) {
            $productPosts = $this->productRepository->productPosts($getNewsByStatus, $user->id, $keyword);

        } else {
            // Không có từ khóa thì load mặc định
            $productPosts = $this->productRepository->productPosts($getNewsByStatus, $user->id);
        }

        $countProductByStatus = $this->productRepository->countProduct($user->id);

        return view('exchange.manager-post.index', compact('categories', 'productPosts', 'countProductByStatus'));
    }

    public function editPostProduct($slug)
    {
        $categories = $this->categoryProductRepository->index();
        $product = $this->productRepository->productDetail($slug);
        if(!$product) {
            return redirect()->route('exchange.home')->with('success', 'Product not found!');
        }
        return view('exchange.manager-post.edit-product', compact('product', 'categories'));
    }

    public function updatePostProduct(Request $request, $slug)
    {
        $input = $request->except(['_token']);
        $dataProduct = $this->productRepository->productDetail($slug);
        if (!$dataProduct) {
            return redirect()->route('exchange.home')->with('error', 'Product not found!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|in:new,used',
            'price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $location = $request->location ?? $dataProduct->location;

        $data = [
            'name' => $input['name'],
            'price' => $input['price'],
            'category' => $input['category'],
            'condition' => $input['condition'],
            'description' => $input['description'],
            'location' => $location,
            'start_date' => $dataProduct->start_date,
            'expires_at' => $dataProduct->expires_at,
            'is_sold' => $dataProduct->is_sold,
        ];

        // Danh sách ảnh cũ (mảng)
        $oldImages = json_decode($dataProduct->images ?? '[]', true);

        // Danh sách index ảnh cũ bị xóa
        $deletedIndexes = array_filter(explode(',', $request->input('delete-image-btn', '')), 'is_numeric');
        // Xóa ảnh bị xóa khỏi mảng và ổ đĩa
        foreach ($deletedIndexes as $index) {
            if (isset($oldImages[$index])) {
                $imagePath = public_path($oldImages[$index]);
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
                unset($oldImages[$index]);
            }
        }

        // Re-index lại mảng
        $oldImages = array_values($oldImages);

        // Thêm ảnh mới
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('/images/upload/product/'), $imageName);
                $oldImages[] = '/images/upload/product/' . $imageName;
            }
        }

        // Cập nhật lại mảng ảnh
        $data['images'] = json_encode($oldImages);

        // Cập nhật sản phẩm
        $this->productRepository->updateBySlug($data, $slug);

        return redirect()->route('exchange.managerPosts')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->productRepository->destroy($id);

        return redirect()->route('exchange.managerPosts')->with('success', 'Product updated successfully');
    }

    public function hideProduct(Request $request)
    {
        $product = $this->productRepository->show($request->product_id);
        $product->status = \App\Enums\Product::STATUS_POST_HIDDEN;
        $product->save();

        return redirect()->route('exchange.managerPosts')->with('success', __('Your product has been hidden.'));
    }

    public function restoreProduct(Request $request)
    {
        $product = $this->productRepository->show($request->product_id);
        $product->status = \App\Enums\Product::STATUS_POST_ACCEPT;
        $product->save();

        return redirect()->back()->with('success', __('Your product is now visible again.'));
    }

    //order
    public function markAsSold(Request $request, $id)
    {
        $product = $this->productRepository->show($id);

        if(!$product) {
            return redirect()->route('exchange.home')->with('success', 'Product not found!');
        }

        $order = Order::create([
            'product_id' => $product->id,
            'seller_id' => auth()->id(),
            'buyer_id' => $request->buyer_id,
            'status' => \App\Enums\Product::STATUS_POST_CONFIRMED,
            'confirmed_at' => now(),
        ]);


        // Ẩn sản phẩm
        Product::where('id', $request->product_id)->update([
            'is_sold' => \App\Enums\Product::PRODUCT_SOLD,
            'status' => \App\Enums\Product::STATUS_POST_HIDDEN,
        ]);
        return back()->with('success', 'Sản phẩm đã được đánh dấu là đã bán!');
    }


}
