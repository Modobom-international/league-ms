@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-screen-lg bg-white mt-6 p-6 rounded shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">🛒 Đơn mua</h2>

        @forelse ($purchases as $order)
            <div class="flex gap-4 items-center border border-gray-200 rounded-lg p-4 mb-4 shadow-sm hover:shadow-md transition">
                {{-- Ảnh sản phẩm --}}
                <div class="w-24 h-24 flex-shrink-0">
                    <img src="{{ asset(json_decode($order->product->images)[0] ?? '/images/no-image.png') }}"
                         alt="{{ $order->product->name }}"
                         class="w-full h-full object-cover rounded-md border">
                </div>

                {{-- Thông tin chi tiết --}}
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $order->product->name }}
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">👤 Người bán: <span class="font-medium">{{ $order->seller->name }}</span></p>
                    <p class="text-sm text-gray-500 mt-1">🗓 Ngày mua: {{ $order->created_at->format('d/m/Y') }}</p>
                </div>

                {{-- Giá --}}
                <div class="text-right">
                    <p class="text-orange-600 font-bold text-lg"> {{ number_format($order->product->price) }} đ</p>
                </div>
            </div>
        @empty
            <p class="text-gray-500">Bạn chưa có đơn mua nào.</p>
        @endforelse
    </div>
@endsection
