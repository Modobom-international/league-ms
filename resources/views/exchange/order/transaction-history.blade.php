@extends('layouts.app')

@section('title', __('History Order') . ' - Badminton Exchange')
@section('content')
        <div class="container mx-auto mt-4" style="max-width: 1250px !important; ">
            <div class="space-y-4">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{__('Transaction History')}}</h2>

                @foreach ($history as $order)
                    <div class="bg-white border border-gray-200 rounded-2xl shadow p-4 hover:shadow-md transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        {{-- Bên trái: thông tin sản phẩm --}}
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            @php
                                $images = json_decode($order->product->images, true) ?? [];
                                $mainImage = $images[0] ?? '/images/no-image.png';
                            @endphp
                            <img src="{{ asset($mainImage) }}"
                                 alt="Ảnh sản phẩm"
                                 class="w-20 h-20 object-cover rounded-lg border">

                            <div class="flex-1 min-w-0">
                                <h2 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ $order->product->name ?? 'Sản phẩm không xác định' }}</h2>
                                <p class=" text-gray-600 truncate">
                                    @if ($order->buyer_id === auth()->id())
                                        Bạn đã <span class="text-green-600 font-medium">mua</span> từ <strong>{{ $order->seller->name }}</strong>
                                    @else
                                        Bạn đã <span class="text-blue-600 font-medium">bán</span> cho <strong>{{ $order->buyer->name }}</strong>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400">{{ $order->created_at->format('H:i d/m/Y') }}</p>
                            </div>
                        </div>

                        {{-- Bên phải: giá --}}
                        <div class="text-right sm:text-left">
                        <span class="text-orange-600 text-lg font-bold block sm:inline">
                            {{ number_format($order->product->price) }} đ
                        </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

@endsection

