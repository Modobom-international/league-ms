@extends('layouts.app')

@section('content')

    <!-- Load thư viện -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>

    <script>
        window.authUserId = {{ auth()->id() }};

        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env("PUSHER_APP_KEY") }}',
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true,
            // encrypted: true, // có thể bỏ vì mặc định forceTLS=true là đủ
        });

        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Pusher connected');
        });

        window.Echo.connector.pusher.connection.bind('error', (err) => {
            console.error('❌ Pusher connection error:', err);
        });
    </script>
    <style>
        .flex justify-end
        {
            margin-top: 5px !important;
        }

        @media (max-width: 767px) {
            .sidebar {
                display: none;
            }
            .chat-detail {
                width: 100% !important;
            }
        }

        /* Trên desktop (768px trở lên): hiển thị cả 2 */
        @media (min-width: 768px) {
            .sidebar {
                display: block;
                width: 33.3333%; /* 1/3 */
            }
            .chat-detail {
                width: 66.6666%; /* 2/3 */
            }
        }
    </style>
    <div class="container mx-auto max-w-screen-lg flex h-screen bg-white mt-4">
        <!-- Sidebar -->
        <div class="sidebar  w-1/3 border-r overflow-y-auto">
            <div class="p-4">
                <input type="text" placeholder="Tìm hội thoại..." class="w-full px-3 py-2 border rounded">
            </div>
            <ul>
                @foreach ($conversations as $conv)
                    @php
                        $authUser = auth()->user();
                        $otherUser = $conv->buyer_id === $authUser->id ? $conv->seller : $conv->buyer;
                    @endphp
                    <li class="p-3 hover:bg-black-100 cursor-pointer border-t">
                        <a href="{{ route('chat.show', $conv->id) }}">
                            <div class="flex items-center justify-between p-3">
                                <div class="flex ">
                                    <div>
                                        <img class="image w-[3rem] rounded-lg"
                                             src="{{ asset($otherUser->profile_photo_path ?? '/images/default-avatar.png') }}"
                                             alt="avatar">
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold text-black-600">{{ $otherUser->name }}</div>
                                        <div class="font-sm text-black-600">{{ Str::limit($conv->product->name, 30) }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ Str::limit(optional($conv->messages->last())->content, 20) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <img class="image w-[3rem] rounded-lg" src="{{ asset($conv->product->images) }}"
                                         alt="avatar">
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Chat Area -->
        <div class="chat-detail w-2/3 flex flex-col">
        @if ($conversation)
            @php
                $authUser = auth()->user();
                $product = $conversation->product;
                $partner = $conversation->buyer_id === $authUser->id ? $conversation->seller : $conversation->buyer;
            @endphp

            <!-- Header -->
                <div class="flex items-center justify-between border-solid mt-1 border-b">
                    <div class="flex p-3">
                        <div>
                            <img class="image w-[3rem] rounded-lg"
                                 src="{{ asset($partner->profile_photo_path ?? '/images/default-avatar.png') }}"
                                 alt="avatar">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-black-600">{{ $partner->name }}</div>
                            <div class="font-semibold text-black-600">đang hoạt động</div>
                        </div>
                    </div>
                </div>

                <div class="flex p-3">
                    <div>
                        <img class="image w-[3rem] rounded-lg" src="{{ asset($product->images) }}" alt="avatar">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm text-black-600">{{ $product->name }}</div>
                        <div class="font-semibold text-orange-600">{{ number_format($product->price) }} đ</div>
                    </div>
                </div>
                <hr class="mt-2">

                <!-- Messages -->
                <div id="chat-box" data-conversation-id="{{ $conversation->id }}" class="flex-1 overflow-y-auto p-4 space-y-2 bg-black-50">
                    <div id="messages">
                        @foreach ($messages as $msg)
                            <div class="flex {{ $msg->user_id == $authUser->id ? 'justify-end' : 'justify-start' }} mt-4">
                                <div class="px-4 py-2 rounded-lg max-w-xs {{ $msg->user_id == $authUser->id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }}">
                                    @if ($msg->content)
                                        <p>{{ $msg->content }}</p>
                                    @endif
                                    @if ($msg->file)
                                        <img src="{{ asset('storage/' . $msg->file) }}" alt="Image" class="mt-2 rounded">
                                    @endif
                                    <div class="text-xs text-right text-white-400 mt-1">
                                        {{ $msg->created_at->format('H:i d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Chat Input -->
                <form id="chatForm" class="p-4 border-t bg-white">
                    @csrf
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach (['Sản phẩm này còn không ạ?', 'Bạn có ship hàng không?', 'Bạn có những size nào?'] as $quickMsg)
                            <button type="button" onclick="insertQuickReply(`{{ $quickMsg }}`)"
                                    class="bg-black-100 px-3 py-1 rounded-full text-sm">
                                {{ $quickMsg }}
                            </button>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="chatInput" name="content" placeholder="Nhập tin nhắn"
                               class="flex-1 px-4 py-2 border rounded-full">
                        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-full">Gửi</button>
                    </div>
                </form>

                <script>
                    // Chèn nhanh tin nhắn mẫu
                    function insertQuickReply(text) {
                        document.getElementById('chatInput').value = text;
                        document.getElementById('chatInput').focus();
                    }

                    const conversationId = document.getElementById('chat-box').dataset.conversationId;
                    const messagesContainer = document.getElementById('messages');

                    console.log('Đang kết nối đến channel:', `chat.${conversationId}`);

                    // Bắt sự kiện gửi form
                    document.getElementById('chatForm').addEventListener('submit', async function (e) {
                        e.preventDefault();

                        const input = document.getElementById('chatInput');
                        const content = input.value.trim();
                        if (!content) return;

                        try {
                            // Gửi tin nhắn qua Axios
                            await axios.post("{{ route('chat.send', $conversation->id) }}", {
                                content: content
                            });

                            // Xóa input sau khi gửi
                            input.value = '';

                            // Tự hiển thị tin nhắn vừa gửi
                            const wrapper = document.createElement('div');
                            wrapper.className = 'flex justify-end';

                            const bubble = document.createElement('div');
                            bubble.className = 'px-4 py-2 rounded-lg max-w-xs bg-blue-500 text-white';
                            bubble.textContent = content;

                            wrapper.appendChild(bubble);
                            messagesContainer.appendChild(wrapper);
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;

                        } catch (error) {
                            console.error('Lỗi gửi tin nhắn:', error);
                        }
                    });

                    // Lắng nghe tin nhắn từ người khác
                    console.log("Đang lắng nghe sự kiện trên channel:", `chat.${conversationId}`);

                    window.Echo.private(`chat.${conversationId}`)
                        .listen('ChatSent', (e) => {
                            console.log("🔥 Đã nhận được sự kiện ChatSent:", e);

                            const msg = e.message;
                            console.log("📩 Nội dung message:", msg);

                            if (msg.user_id === window.authUserId) {
                                console.log("🚫 Tin nhắn của chính mình, không hiển thị lại");
                                return;
                            }

                            const wrapper = document.createElement('div');
                            wrapper.className = 'flex justify-start';

                            const bubble = document.createElement('div');
                            bubble.className = 'px-4 py-2 rounded-lg max-w-xs bg-gray-200 text-black';
                            bubble.textContent = msg.content;

                            wrapper.appendChild(bubble);
                            messagesContainer.appendChild(wrapper);
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        });
                </script>


            @else
                <div class="flex items-center justify-center flex-1">
                    <p class="text-black-500">Chọn một cuộc trò chuyện để bắt đầu.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
