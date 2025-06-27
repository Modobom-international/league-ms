{{-- resources/views/exchange/favorites/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Sản phẩm đã lưu')

@section('content')
    <div class="container mx-auto max-w-screen-lg bg-white mt-6 p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">🧡 Danh sách sản phẩm yêu thích</h2>

        @forelse ($favoriteProducts as $product)
            <div class="flex items-start gap-4 border-b py-4">
                @php
                    $images = json_decode($product->images, true) ?? [];
                    $mainImage = $images[0] ?? '/images/no-image.png';
                @endphp
                <img src="{{ asset($mainImage) }}" class="w-28 h-28 object-cover rounded-lg border" alt="Product Image">

                <div class="flex-1">
                    <a href="{{ route('exchange.productDetail', $product->slug) }}"
                       class="text-lg font-semibold text-gray-900 hover:text-orange-500">
                        {{ $product->name }}
                    </a>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                        {{ \Illuminate\Support\Str::limit($product->description, 100, '...') }}
                    </p>
                    <p class="text-red-600 font-bold mt-1">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                    <p class="text-xs text-gray-400">{{ $product->updated_at->diffForHumans() }} • {{ $product->location }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-600">Bạn chưa lưu sản phẩm nào.</p>
        @endforelse

        <div class="mt-6">
            {{ $favoriteProducts->links('exchange.paginate.custom-paginate') }}
        </div>
    </div>
@endsection
