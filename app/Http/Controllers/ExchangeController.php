<?php

namespace App\Http\Controllers;

use App\Events\ChatSent;
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

    public function rule()
    {
        $categories = $this->categoryProductRepository->index();
        return view('exchange.rule', compact('categories'));
    }

    public function productDetail($slug)
    {
        $product = $this->productRepository->productDetail($slug);

        if(!$product) {
            return redirect()->route('exchange.home')->with('success', 'Product not found!');
        }

        $relatedProducts = $this->productRepository->relateProduct($product, $slug);

        return view('exchange.product.show', compact('product', 'relatedProducts'));
    }
    /**
     * Show the form for creating a new resource.
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

    public function destroy($id)
     {
         $this->productRepository->destroy($id);

         return redirect()->route('exchange.managerPosts')->with('success', 'Product updated successfully');
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

    public function show(Conversation $conversation)
    {
        Auth::guard('canstum')->user();

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
        Auth::guard('canstum')->user();

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
