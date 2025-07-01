@foreach ($products as $product)
    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md hover:scale-105 transition">
        <div class="relative">
            @php
                $images = json_decode($product->images, true) ?? [];
                $mainImage = $images[0] ?? '/images/no-image.png';
                $isFavorited = Auth::check() && Auth::user()->hasFavorited($product->id); // Kiểm tra yêu thích
            @endphp

            <img src="{{ asset($mainImage) }}" alt="image" class="w-full h-48 object-cover">

            {{-- ❤️ Icon yêu thích ở góc phải --}}
            <button
                class="favorite-btn absolute toggle-favorite-btn top-2 right-2 bg-white bg-opacity-80 rounded-full p-2 shadow-md text-xl transition-colors duration-200"
                data-product-id="{{ $product->id }}"
                id="favorite-btn-{{ $product->id }}"
            >
                <i class="fas fa-heart {{ $isFavorited ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}"></i>
            </button>

        </div>

        <div class="p-3 space-y-1">
            <a href="{{ route('exchange.productDetail', $product['slug']) }}">
                <h3 class="text-xl font-semibold line-clamp-2">{{ $product->name }}</h3>
            </a>
            <div class="flex items-center text-gray-700 mt-1">
                {{ \Illuminate\Support\Str::limit($product->description, 80, ' [...]') }}
            </div>
            <div class="flex justify-between items-center text-red-600 font-semibold mt-2">
                <p class="text-xl">{{ number_format($product->price, 0, ',', '.') }} đ</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>📍 {{ $product->location }}</span>
            </div>
            <div class="flex items-center text-gray-700 mt-1">
                @if ($product->users->profile_photo_path)
                    <img src="{{ asset($product->users->profile_photo_path) }}" class="w-8 h-8 rounded-full shadow object-cover" />
                @else
                    <div class="w-5 h-5 bg-blue-500 text-white text-sm rounded-full flex items-center justify-center font-semibold shadow">
                        {{ strtoupper(substr($product->users->name, 0, 1)) }}
                    </div>
                    @endif
                    &ensp;.&ensp;
                <i class="fas fa-clock mr-2"></i>
                {{ __('Updated') }} {{ $product->updated_at->diffForHumans() }}
            </div>
        </div>
    </div>
@endforeach
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('.toggle-favorite-btn').click(function () {
            let button = $(this);
            let productId = button.data('product-id');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            let icon = button.find('i'); // 🔥 Lấy icon bên trong nút

            $.ajax({
                url: '/favorite/' + productId,
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function (data) {
                    if (data.favorited) {
                        icon.removeClass('text-gray-400 hover:text-red-500').addClass('text-red-500');
                    } else {
                        icon.removeClass('text-red-500').addClass('text-gray-400 hover:text-red-500');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/exchange-login';
                    } else {
                        console.error('Lỗi AJAX:', xhr.responseText);
                    }
                }
            });
        });
    });
</script>




