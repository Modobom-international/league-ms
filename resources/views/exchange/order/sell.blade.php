@extends('layouts.app')

@section('title', __('Selling Orders') . ' - Badminton Exchange')

@section('content')
    <div class="container mx-auto mt-6 px-4" style="max-width: 1250px;">
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">📦 {{ __('Đơn bán') }}</h2>

            @forelse ($sales as $order)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    {{-- Trái: ảnh + thông tin --}}
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        @php
                            $images = json_decode($order->product->images, true) ?? [];
                            $mainImage = $images[0] ?? '/images/no-image.png';
                        @endphp
                        <img src="{{ asset($mainImage) }}"
                             alt="{{ $order->product->name }}"
                             class="w-20 h-20 object-cover rounded-lg border">

                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">
                                {{ $order->product->name ?? 'Sản phẩm không xác định' }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1 truncate">
                                📥 Đã bán cho: <strong>{{ $order->buyer->name }}</strong>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">🕒 {{ $order->created_at->format('H:i d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Phải: giá --}}
                    <div class="text-right sm:text-left">
                        <span class="text-orange-600 font-bold text-lg block sm:inline">
                            {{ number_format($order->product->price) }} đ
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-gray-500 mt-6">
                    {{ __('Bạn chưa có đơn bán nào.') }}
                </div>
            @endforelse
        </div>
    </div>
@endsection
