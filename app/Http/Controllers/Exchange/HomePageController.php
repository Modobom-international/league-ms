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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HomePageController extends Controller
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

    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);
        $products = $this->productRepository->loadMore($page);
        // Render HTML từ view (file paginate.product-list.blade.php)
        $view = view('exchange.paginate.product-list', compact('products'))->render();

        return response()->json([
            'products' => $view, // Trả về HTML thay vì JSON
            'next_page' => $products->nextPageUrl()
        ]);
    }




}
