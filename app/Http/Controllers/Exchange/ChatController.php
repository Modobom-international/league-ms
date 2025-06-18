<?php

namespace App\Http\Controllers\Exchange;

use App\Events\ChatSent;
use App\Http\Controllers\Controller;
use App\Models\Chatting;
use App\Models\Conversation;
use App\Repositories\CategoryProductRepository;
use App\Repositories\ConversationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{
    private $productRepository;
    private $conversationRepository;
    private $userRepository;
    private $categoryProductRepository;

    public function __construct(
        ProductRepository $productRepository,
        ConversationRepository $conversationRepository,
        CategoryProductRepository $categoryProductRepository,
        UserRepository $userRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->conversationRepository = $conversationRepository;
        $this->categoryProductRepository = $categoryProductRepository;
        $this->userRepository = $userRepository;
    }


    public function listChat()
    {
        $user = Auth::user();

        $conversations = $this->conversationRepository->listChat($user);
        return view('exchange.chat.list-chat', compact('conversations'));
    }

    public function chatWithSeller($productId)
    {
        $product = $this->productRepository->show($productId);
        $user = Auth::user();

        // Tìm conversation đã tồn tại (có thể là buyer hoặc seller)
        $conversation = $this->conversationRepository->getConversation($product, $user);

        // Nếu chưa có thì tạo mới (chỉ nếu user không phải là chủ sản phẩm)
        if (!$conversation) {

            $dataCreate = [
                'product_id' => $product->id,
                'buyer_id' => $user->id,
                'seller_id' => $product->user_id,
            ];

            $this->conversationRepository->createConversation($dataCreate);
        }

        // Lấy danh sách tất cả cuộc trò chuyện liên quan đến user
        $conversations = $this->conversationRepository->getByUser($user);

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

        return view('exchange.chat.show', compact(
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
        $user = Auth::user();

        $conversation = $this->conversationRepository->show($conversationId);
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
