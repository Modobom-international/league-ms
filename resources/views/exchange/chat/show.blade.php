@extends('layouts.app')

@section('content')

    <!-- Load thư viện -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.3/dist/echo.iife.js"></script>

    <script>
        window.authUserId = {{ auth()->id() }};

        // Không khởi tạo Echo ở đây nữa nếu đã import và khởi tạo trong JS riêng (echo-chatting.js).
        // Nếu bạn vẫn muốn khởi tạo trong blade, thì làm như sau:

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env("REVERB_APP_KEY") }}',           // hoặc bỏ nếu bạn truyền client trực tiếp
            wsHost: '{{ env("REVERB_HOST") }}',
            wsPort: {{ env("REVERB_PORT") ?? 80 }},
            wssPort: {{ env("REVERB_PORT") ?? 443 }},
            forceTLS: {{ (env("REVERB_SCHEME") ?? 'https') === 'https' ? 'true' : 'false' }},
            enabledTransports: ['ws', 'wss'],
        });

        // Bind event connection - sửa thành reverb (thay vì pusher)
        if (window.Echo.connector && window.Echo.connector.reverb) {
            window.Echo.connector.reverb.connection.bind('connected', () => {
                console.log('✅ Reverb connected');
            });

            window.Echo.connector.reverb.connection.bind('error', (err) => {
                console.error('❌ Reverb connection error:', err);
            });
        } else {
            console.warn('Reverb connector not found');
        }
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
                            <div class="flex items-center justify-between ">
                                <div class="flex ">
                                    <div>
                                        <img class="image w-[3rem] rounded-lg"
                                             src="{{ asset($otherUser->profile_photo_path ?? '/images/default-avatar.png') }}"
                                             alt="avatar">
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold text-black-600">{{ $otherUser->name }}</div>
                                        <div class="font-sm text-black-600">{{ Str::limit($conv->product->name, 20) }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ Str::limit(optional($conv->messages->last())->content, 20) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $images = json_decode($conv->product->images, true) ?? [];
                                        $mainImage = $images[0] ?? '/images/no-image.png'; // ảnh mặc định nếu không có
                                    @endphp
                                    <img class="image w-[3rem] rounded-lg" src="{{ asset($mainImage) }}"
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
                $partner = $conversation->buyer_id === $authUser->id ? $conversation->seller: $conversation->buyer;
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

                <div class="flex justify-between items-center p-3 border-b">
                    {{-- Thông tin sản phẩm --}}
                    <div class="flex items-center">
                        @php
                            $images = json_decode($product->images, true) ?? [];
                            $mainImage = $images[0] ?? '/images/no-image.png';
                        @endphp
                        <img class="w-[3rem] h-[3rem] object-cover rounded-lg" src="{{ asset($mainImage) }}" alt="avatar">

                        <div class="ml-4">
                            <div class="text-sm text-black font-semibold">{{ $product->name }}</div>
                            <div class="font-semibold text-orange-600">{{ number_format($product->price) }} đ</div>
                        </div>
                    </div>

                    {{-- Nút đánh dấu đã bán --}}
                    <div>
                        @if ($product->is_sold)
                            <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs">Đã bán</span>
                        @else
                            @if(($authUser->id == $product->user_id))
                            <button
                                onclick="openModal({{ $product->id }})"
                                class="bg-gray-500 hover:~bg-gray-500/5-600 text-white text-xs px-3 py-1 rounded-lg"
                            >
                                Đã bán / Ẩn tin
                            </button>
                        @endif
                        @endif
                    </div>
                </div>

                <div id="markAsSoldModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
                        <h2 class="text-xl text-center font-bold mb-5">{{ __('Hide Listing') }}</h2>
                        <form id="markAsSoldForm" method="POST" action="{{route('product.markAsSold', $product->id)}}">
                            @csrf
                            <input type="hidden" name="product_id" id="modal_product_id">
                            <input type="hidden" name="buyer_id" value="{{$partner->id}}">

                            <div class="space-y-3 mb-5">
                                <!-- Option 1 -->
                                <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                    <input type="radio" name="reason" value="sold" class="sr-only peer" required>
                                    <span class="text-gray-800">{{ __('Sold via Badminton') }}</span>
                                    <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                </label>

                                <!-- Option 2 -->
                                <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                    <input type="radio" name="reason" value="sold_other" class="sr-only peer" required>
                                    <span class="text-gray-800">{{ __('Sold through other channels') }}</span>
                                    <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                </label>

                                <!-- Option 3 -->
                                <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                    <input type="radio" name="reason" value="not_interested" class="sr-only peer" required>
                                    <span class="text-gray-800">{{ __('I was bothered by brokers/competitors') }}</span>
                                    <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                </label>

                                <!-- Option 4 -->
                                <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                    <input type="radio" name="reason" value="no_longer_selling" class="sr-only peer" required>
                                    <span class="text-gray-800">{{ __('I no longer want to sell') }}</span>
                                    <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                </label>

                                <!-- Option 5 -->
                                <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                    <input type="radio" name="reason" value="other" class="sr-only peer" required>
                                    <span class="text-gray-800">{{ __('Other') }}</span>
                                    <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                </label>
                            </div>

                            <div class="text-sm text-red-500 mb-4">
                                <strong>{{ __('Note:') }}</strong> {{ __('Listing and promotion fees will not be refunded when you hide the listing.') }}
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">{{ __('Cancel') }}</button>
                                <button type="submit" class="px-5 py-2 text-white bg-orange-500 rounded hover:bg-orange-600 font-semibold">
                                    {{ __('Hide Listing') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

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
                    @if(!($authUser->id == $product->user_id))
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach (['Sản phẩm này còn không ạ?', 'Bạn có ship hàng không?', 'Bạn có những size nào?'] as $quickMsg)
                            <button type="button" onclick="insertQuickReply(`{{ $quickMsg }}`)"
                                    class="bg-wh px-3 py-1 rounded-full text-sm bg-[#f5deb3]">
                                {{ $quickMsg }}
                            </button>
                        @endforeach
                    </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <input type="text" id="chatInput" name="content" placeholder="Nhập tin nhắn"
                               class="flex-1 px-4 py-2 border rounded-full">
                        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-full">Gửi</button>
                    </div>
                </form>
            @else
                <div class="flex items-center justify-center flex-1">
                    <p class="text-black-500">Chọn một cuộc trò chuyện để bắt đầu.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
<script>
    // Chèn nhanh tin nhắn mẫu
    document.addEventListener('DOMContentLoaded', function () {
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

    window.Echo.channel(`chat.${conversationId}`)
        .listen('.ChatSent', (e) => {
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
    });
</script>
<script>
    function openModal(productId) {
        document.getElementById('modal_product_id').value = productId;
        document.getElementById('markAsSoldForm').action = '/product/' + productId + '/mark-as-sold'; // Route xử lý
        document.getElementById('markAsSoldModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('markAsSoldModal').classList.add('hidden');
        document.getElementById('markAsSoldForm').reset();
    }
</script>
