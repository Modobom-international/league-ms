<?php

namespace App\Http\Controllers;

use App\Events\ChatSent;
use App\Http\Requests\UserRequest;
use App\Models\CategoryProduct;
use App\Models\Chatting;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class ExchangeController extends Controller
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
    public function index(Request $request)
    {
        $keyword = $request->input('q');

        // Nếu có từ khóa, thì ưu tiên hiển thị kết quả tìm kiếm
        if ($keyword) {
            $products = $this->productRepository->productSearch($keyword);
        } else {
            // Không có từ khóa thì load mặc định
            $products = $this->productRepository->homeExchange();
        }
        $newProductIds = $products->pluck('id')->toArray();
        $excludeIds = array_merge($newProductIds);
        $recommended = $this->productRepository->recommend($excludeIds);
        $categories = $this->categoryProductRepository->index();
        return view('exchange.index', compact('products', 'categories','recommended'));
    }

    public function aboutUs()
    {
        $categories = $this->categoryProductRepository->index();
        return view('exchange.about-us', compact('categories'));
    }

    public function privacyPolicy()
    {
        $categories = $this->categoryProductRepository->index();
        return view('exchange.privacy-policy', compact('categories'));
    }

    public function rule()
    {
        $categories = $this->categoryProductRepository->index();
        return view('exchange.rule', compact('categories'));
    }

    public function productDetail($slug)
    {
        $categories = $this->categoryProductRepository->index();
        $product = $this->productRepository->productDetail($slug);

        if(!$product) {
            return redirect()->route('exchange.home')->with('success', 'Product not found!');
        }

        $relatedProducts = Product::where('category', $product->category)->where('slug', '!=', $slug)->limit(6)->get();

        return view('exchange.product.show', compact('product', 'relatedProducts', 'categories'));
    }
    /**
     * Show the form for creating a new resource.
     */

    //category
    public function categoryDetail($slug, Request $request)
    {
        $categories = $this->categoryProductRepository->index();
        $category = $this->categoryProductRepository->productCategory($slug);

        // Tập hợp filter từ request
        $filters = [
            'location' => $request->input('location'),
            'condition' => $request->input('condition'),
            'sort' => $request->input('sort'),
            'keyword' => $request->input('q'), // nếu có ô tìm kiếm
        ];

        $products = $this->productRepository->productFilter($category, $filters);


        return view('exchange.product.category-product', compact('products', 'categories', 'category'));
    }

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
        $input['status'] = \App\Enums\Product::STATUS_POST_NEWS;
        $input['user_id'] = Auth::id();
        $input['start_date'] = now();
        $input['expires_at'] = now()->addDays(30);
        $input['name'] = $request->name;
        $input['category'] = $request->category;
        $input['description'] = $request->description;
        $input['condition'] = $request->condition;
        $input['location'] = $request->location;
        $input['price'] = $request->price;
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
            $productNews = $this->productRepository->productNews($getNewsByStatus, $user->id, $keyword);

        } else {
            // Không có từ khóa thì load mặc định
            $productNews = $this->productRepository->productNews($getNewsByStatus, $user->id);

        }

        $countProductByStatus = $this->productRepository->countProduct($user->id);

        return view('exchange.manager-post.index', compact('categories', 'productNews', 'countProductByStatus'));
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

    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);

        $products = Product::where('status', 'accepted')
            ->orderBy('created_at', 'desc')
            ->paginate(6, ['*'], 'page', $page);

        // Render HTML từ view (file paginate.product-list.blade.php)
        $view = view('exchange.paginate.product-list', compact('products'))->render();

        return response()->json([
            'products' => $view, // Trả về HTML thay vì JSON
            'next_page' => $products->nextPageUrl()
        ]);
    }

    public function profile()
    {
        $idUser = Auth::user()->id;
        $dataUser = $this->userRepository->showInfo($idUser);
        $categories = $this->categoryProductRepository->index();
        return view('exchange.profile.update-info', compact('categories', 'dataUser'));
    }

    public function update(Request $request, $userIdHash)
    {
        if (empty($userIdHash)) {
            abort(404);
        }

        $input = $request->except(['_token']);
        $this->userRepository->update($input, $userIdHash);
        return back()->with('success', __('Information has been updated successfully!'));
    }

    public function changePassword()
    {
        $idUser = Auth::user()->id;
        $dataUser = $this->userRepository->showInfo($idUser);
        $categories = $this->categoryProductRepository->index();
        return view('exchange.profile.change-password', compact('categories', 'dataUser'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with("error", __("Old passwords do not match!"));
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with("success", __("Password successfully changed!"));
    }

    public function listChat()
    {
        $user = Auth::user();

        $conversations = Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with(['buyer', 'seller', 'messages' => function ($q) {
                $q->latest()->limit(1); // chỉ lấy tin nhắn gần nhất cho preview
            }])
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('chattings')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1);
            })
            ->get();
        $categories = $this->categoryProductRepository->index();
        return view('exchange.chat.list-chat', compact('conversations', 'categories'));
    }

    public function chatWithSeller($productId)
    {
        $product = Product::findOrFail($productId);
        $user = Auth::user();

        // Tìm conversation đã tồn tại (có thể là buyer hoặc seller)
        $conversation = Conversation::where('product_id', $product->id)
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->first();
        // Nếu chưa có thì tạo mới (chỉ nếu user không phải là chủ sản phẩm)
        if (!$conversation) {
            $conversation = Conversation::create([
                'product_id' => $product->id,
                'buyer_id' => $user->id,
                'seller_id' => $product->user_id,
            ]);
        }

        // Lấy danh sách tất cả cuộc trò chuyện liên quan đến user
        $conversations = Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with([
                'buyer',
                'seller',
                'product',
                'messages' => fn($q) => $q->latest()->limit(1)
            ])
            ->get()
            ->sortByDesc(function ($conv) {
                return optional($conv->messages->first())->created_at ?? $conv->created_at;
            })
            ->values(); // reset lại index

        $messages = $conversation->messages()->orderBy('created_at')->get();
        $categories = $this->categoryProductRepository->index();

        return view('exchange.chat.show', compact(
            'categories',
            'conversation',
            'product',
            'conversations',
            'messages',
            'user'
        ));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // Kiểm tra nếu user không liên quan thì chặn
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            abort(403);
        }

        $allConversations = Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with([
                'buyer',
                'seller',
                'product',
                'messages' => fn($q) => $q->latest()->limit(1)
            ])
            ->get();

// Đưa current conversation lên đầu
        $conversations = collect([$conversation])
            ->merge(
                $allConversations->filter(fn($conv) => $conv->id !== $conversation->id)
                    ->sortByDesc(fn($conv) => optional($conv->messages->first())->created_at ?? $conv->created_at)
            )
            ->values(); // reset index

        $messages = $conversation->messages()->orderBy('created_at')->get();
        $categories = app(CategoryProductRepository::class)->index();

        return view('exchange.chat.show', compact(
            'categories',
            'conversation',
            'conversations',
            'messages',
            'user'
        ));
    }

    public function send(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $user = Auth::user();

        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|image|max:2048', // chỉ cho phép ảnh < 2MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('/images/upload/product/'), $imageName);
            $filePath = '/images/upload/product/' . $imageName;
        }

        $message = Chatting::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'file' => $filePath,
        ]);

        // ✅ Ghi log kiểm tra
        Log::info('Broadcasting ChatSent event', [
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
            'user_id' => $user->id,
        ]);
        broadcast(new ChatSent($conversation, $message))->toOthers();

        return response()->json(['message' => $message]);
    }

}
