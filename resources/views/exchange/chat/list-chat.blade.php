@extends('layouts.app')

@section('content')
    <style>
        /* Mobile: ẩn chat-detail, chỉ show sidebar full width */
        @media (max-width: 767px) {
            .sidebar {
                width: 100% !important;
                display: block !important;
            }
            .chat-detail {
                display: none !important;
            }
        }

        /* Desktop: show cả 2 phần */
        @media (min-width: 768px) {
            .sidebar {
                width: 33.3333% !important;
                display: block !important;
            }
            .chat-detail {
                width: 66.6666% !important;
                display: flex !important;
            }
        }
    </style>
    <div class="container mx-auto max-w-screen-lg bg-white mt-4 flex h-screen">
        <!-- Danh sách cuộc trò chuyện -->
        <div class="sidebar w-1/3 border-r border-gray-300 overflow-y-auto">
            <h2 class="p-4 font-bold text-xl border-b">Conversations</h2>
            <ul>
                @foreach($conversations as $conv)
                    @php
                        $authUser = auth()->user();
                        $otherUser = $conv->buyer_id === $authUser->id ? $conv->seller : $conv->buyer;
                    @endphp
                    <li class=" hover:bg-black-100 cursor-pointer border-t">
                        <a href="{{ route('chat.show', $conv->id) }}">
                            <div class="flex items-center justify-between ">
                                <div class="flex p-3">
                                    <div>
                                        <img class="image w-[3rem] rounded-lg" src="{{asset($otherUser->profile_photo_path ?? '/images/default-avatar.png')}}" alt="avatar" >
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold text-black-600">{{ $otherUser->name }}</div>
                                        <div class="font-sm text-black-600">{{ Str::limit($conv->product->name, 20) }}</div>
                                        <div class="text-sm text-gray-600">
                                            {{ Str::limit(optional($conv->messages->last())->content, 30) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <img class="image w-[2rem] rounded-lg mr-4" src="{{asset($conv->product->images)}}" alt="avatar" >

                                </div>
                            </div>


                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Khung chat -->
        <div class="chat-detail w-2/3 flex flex-col justify-between h-full">
            <div class="flex-grow flex items-center justify-center text-black-700">
                Hãy chọn một cuộc trò chuyện để bắt đầu.
            </div>
        </div>

    </div>
@endsection
