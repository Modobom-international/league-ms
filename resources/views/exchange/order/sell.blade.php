@extends('layouts.app')

@section('title', __('About Us') . ' - Badminton Exchange')

@section('content')
    <div>
        <h2 class="text-xl font-bold mb-4">Đơn bán</h2>
        @foreach ($sales as $order)
            <div class="border p-4 rounded shadow-sm mb-3">
                <h3 class="font-semibold">{{ $order->product->name }}</h3>
                <p class="text-sm">Người mua: {{ $order->buyer->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y') }}</p>
                <p class="text-orange-600 font-bold">{{ number_format($order->price) }} đ</p>
            </div>
        @endforeach
    </div>

@endsection
