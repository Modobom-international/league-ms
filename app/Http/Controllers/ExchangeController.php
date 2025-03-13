<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExchangeController extends Controller
{

    private $productRepository;
    private $categoryProductRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryProductRepository $categoryProductRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->categoryProductRepository = $categoryProductRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->productRepository->homeExchange();
        $categories = $this->categoryProductRepository->index();
        return view('exchange.index', compact('products', 'categories'));
    }

    public function productDetail($slug)
    {
        $categories = $this->categoryProductRepository->index();
        $product =  $this->productRepository->productDetail($slug);

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
    public function categoryDetail($slug)
    {
        $categories = $this->categoryProductRepository->index();
        $categoryProduct =  $this->categoryProductRepository->productCategory($slug);

        return view('exchange.product.category-product', compact('categoryProduct', 'categories'));
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

    public function createProductNews()
    {
        $categories = $this->categoryProductRepository->index();
        return view('exchange.manager-news.post-product', compact( 'categories'));
    }

    public function storeProductNews(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|in:new,used',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string',
            'images' => 'required|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_images'   => 'required|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        // Tạo sản phẩm mới
        $input = $request->except(['_token']);
        $input = $request->except(['_token']);
        $input['slug'] = Str::slug($request->name);
        $input['status'] = \App\Enums\Product::STATUS_POST_NEWS;
        $input['user_id'] = Auth::user()->id;
        $input['start_date'] = now();
        $input['expires_at'] = now()->addDays(30);
        // Xử lý ảnh chính (image)
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('/images/upload/product/'), $imageName);
            $input['images'] = '/images/upload/product/' . $imageName; // Lưu đường dẫn

        }
        $product = $this->productRepository->create($input);
        // Xử lý ảnh phụ (images)
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('/images/upload/product/'), $imageName);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => '/images/upload/product/' . $imageName
                ]);
            }
        }

        return redirect()->route('exchange.managerNews')->with('success', 'Post news created success!');
    }

    public function managerNews(Request $request)
    {
        $user = Auth::user();
        $categories = $this->categoryProductRepository->index();
        $getNewsByStatus = $request->get('status');
        $productNews = $this->productRepository->productNews($getNewsByStatus, $user->id);
        $countProductByStatus = $this->productRepository->countProduct($user->id);

        return view('exchange.manager-news.index', compact('categories', 'productNews', 'countProductByStatus'));
    }

    public function editProductNews($slug)
    {
        $categories = $this->categoryProductRepository->index();
        $product = $this->productRepository->productDetail($slug);
        if(!$product) {
            return redirect()->route('exchange.home')->with('success', 'Product not found!');
        }
        return view('exchange.manager-news.edit-product', compact('product', 'categories'));
    }

    public function updateProductNews(Request $request, $slug)
    {
        $input = $request->except(['_token']);
        $dataProduct = $this->productRepository->productDetail($slug);
        if(!$dataProduct) {
            return redirect()->route('exchange.home')->with('success', 'Product not found!');
        }

         $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|in:new,used',
            'price' => 'required|numeric|min:0',
            'images' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
             'product_images'   => 'array',
             'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

         if(!($request->location)) {
            $location = $dataProduct->location;
         }

        // Cập nhật thông tin sản phẩm
        $data = [
            'name' => $input['name'],
            'price' => $input['price'],
            'category' => $input['category'],
            'condition' => $input['condition'],
            'description' => $input['description'],
            'location' => $location,
            'start_date' => $dataProduct->start_date,
            'expires_at' => $dataProduct->expires_at
        ];

        // Cập nhật ảnh chính
        if ($request->hasFile('images')) {
            $image = $request->file('images');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('/images/upload/product/'), $imageName);
            $input['images'] = '/images/upload/product/' . $imageName; // Lưu đường dẫn

        }
        $product = $this->productRepository->updateBySlug($data, $slug);
        // Xử lý ảnh phụ (images)
        if ($request->hasFile('sub_images')) {
            foreach ($request->file('sub_images') as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('/images/upload/product/'), $imageName);

                ProductImage::create([
                    'product_id' => $dataProduct->id,
                    'image_url' => '/images/upload/product/' . $imageName
                ]);
            }
        }

        return redirect()->route('exchange.managerNews')->with('success', 'Product updated successfully');
    }

     public function destroy($id)
     {
         $this->productRepository->destroy($id);

         return redirect()->route('exchange.managerNews')->with('success', 'Product updated successfully');
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

}
