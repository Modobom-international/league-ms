@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-screen-lg flex h-screen bg-white  mt-4">
        <!-- Sidebar -->
        <div class="w-1/3 border-r overflow-y-auto">
            <div class="p-4">
                <input type="text" placeholder="Tìm hội thoại..." class="w-full px-3 py-2 border rounded">
            </div>
            <ul>
                @foreach($conversations as $conv)
                    @php
                        $authUser = auth()->user();
                        $otherUser = $conv->buyer_id === $authUser->id ? $conv->seller : $conv->buyer;
                    @endphp
                    <li class="p-3 hover:bg-black-100 cursor-pointer border-t">
                        <a href="{{ route('chat.show', $conv->id) }}">
                            <div class="flex items-center justify-between p-3">
                                <div class="flex ">
                                    <div>
                                        <img class="image w-[3rem] rounded-lg" src="{{asset($otherUser->profile_photo_path ?? '/images/default-avatar.png')}}" alt="avatar" >
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
                                    <img class="image w-[3rem] rounded-lg " src="{{asset($conv->product->images)}}" alt="avatar" >

                                </div>
                            </div>


                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Chat Area -->
        <div class="w-2/3 flex flex-col">
        @if($conversation)
            @php
                $authUser = auth()->user();
                $product = $conversation->product;
                $partner = $conversation->buyer_id === $authUser->id
                    ? $conversation->seller
                    : $conversation->buyer;
            @endphp

            <!-- Header -->
                <div class="flex items-center justify-between border-solid mt-1 border-b">
                    <div class="flex p-3">
                        <div>
                            <img class="image w-[3rem] rounded-lg" src="{{asset($partner->profile_photo_path ?? '/images/default-avatar.png')}}" alt="avatar" >
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-black-600">{{ $partner->name }}</div>
                            <div class="font-semibold text-black-600">đang hoạt động</div>
                        </div>
                    </div>

                </div>
                <div class="flex p-3">
                    <div>
                        <img class="image w-[3rem] rounded-lg" src="{{asset($product->images)}}" alt="avatar" >
                    </div>
                    <div class="ml-4">
                        <div class="text-sm text-black-600">{{ $product->name }}</div>
                        <div class="font-semibold text-orange-600">{{ number_format($product->price) }} đ</div>
                    </div>
                </div>
            <hr class="mt-2">
                <!-- Messages -->
                <div class=" flex-1 overflow-y-auto p-4 space-y-2 bg-black-50">
                    @foreach($messages as $msg)
                        <div class="flex {{ $msg->user_id == $authUser->id ? 'justify-end' : 'justify-start' }}">
                            <div class=" px-4 py-2 rounded-lg max-w-xs">
                                @if ($msg->content)
                                    <p class="px-4 py-2 rounded-lg {{ $msg->user_id == $authUser->id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }}">{{ $msg->content }}</p>
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

                <!-- Chat Input -->
                <form action="{{ route('chat.send', $conversation->id) }}" method="POST" class="p-4 border-t bg-white">
                    @csrf
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach(['Sản phẩm này còn không ạ?', 'Bạn có ship hàng không?', 'Bạn có những size nào?'] as $quickMsg)
                            <button type="button" onclick="insertQuickReply(`{{ $quickMsg }}`)" class="bg-black-100 px-3 py-1 rounded-full text-sm">
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
                    function insertQuickReply(text) {
                        document.getElementById('chatInput').value = text;
                        document.getElementById('chatInput').focus();
                    }
                </script>
            @else
                <div class="flex items-center justify-center flex-1">
                    <p class="text-black-500">Chọn một cuộc trò chuyện để bắt đầu.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
