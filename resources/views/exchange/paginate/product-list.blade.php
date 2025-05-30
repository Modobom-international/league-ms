@foreach ($products as $product)
    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md hover:scale-105 transition">
        <div class="relative">
            <img src="{{ asset($product->images) }}" alt="image" class="w-full h-48 object-cover">

        </div>
        <div class="p-3 space-y-1">
            <a href="{{route('exchange.productDetail', $product['slug'])}}">
                <h3 class=" text-xl font-semibold line-clamp-2">{{ $product->name }}</h3>
            </a>
            <div class="flex justify-between items-center text-red-600 font-semibold mt-4">
                <p class="text-xl">{{ number_format($product->price, 0, ',', '.') }} đ</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>📍 {{ $product->location }}</span>
            </div>
            <div class="flex items-center text-gray-700  mt-1">
                <i class="fas fa-clock  mr-2"></i>
                {{ __('Updated') }} {{ $product->updated_at->diffForHumans() }}
            </div>
        </div>
    </div>
@endforeach
