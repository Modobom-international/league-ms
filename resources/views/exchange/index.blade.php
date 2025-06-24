@extends('layouts.app')
<style>
    .widget-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        border-top: 4px solid #1f2937;
    }

    .widget-title a {
        color: #333;
        text-transform: uppercase;
        font-size: 18px;
        font-weight: 700;
    }

    .container {
        max-width: 1280px !important;
    }

    #slider-track img {
        height: 500px
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .mt-4 {
        margin-top: 35px !important;
    }

    #short-text {
        font-size: 18px;
    }

    .product-card {
        height: 100%;
        min-height: 380px;
        /* hoặc chiều cao bạn muốn */
    }

    .product-title {
        min-height: 3.5rem;
        /* giữ cho tên sản phẩm luôn cao, kể cả ngắn */
    }

    .product-location,
    .product-updated {
        min-height: 1.5rem;
    }
</style>
@section('content')
    <div class="container mx-auto px-4 mt-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 p-4 bg-white rounded-lg max-w-full overflow-x-auto">
            @foreach ($categories as $category)
                <a href="{{ route('exchange.categoryDetail', $category->slug) }}"
                   class="group flex flex-col items-center text-center space-y-2 rounded-lg p-3 transition duration-300 hover:opacity-80 w-full max-w-[120px] mx-auto">

                    <div class="w-20 h-20 shadow-md flex items-center justify-center overflow-hidden">
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}"
                             class="w-full h-full object-contain" />
                    </div>

                    <span class="text-xs sm:text-sm font-medium text-gray-800 transition group-hover:text-green-600 break-words">
                {{ $category->name }}
            </span>
                </a>
            @endforeach
        </div>


        <div class="relative w-full overflow-hidden rounded-xl mt-4" id="custom-slider">
            <!-- Slides wrapper -->
            <div class="flex transition-transform duration-700 ease-in-out" id="slider-track">
                <img src="{{ asset('images/exchange/1.png') }}" class="w-full h-500 shrink-0 object-cover" alt="Slide 1" />
                <img src="{{ asset('images/exchange/2.png') }}" class="w-full h-500 shrink-0 object-cover" alt="Slide 2" />
            </div>

            <!-- Optional: Pagination -->
            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                <div class="w-2 h-2 bg-white/50 rounded-full" data-index="0"></div>
                <div class="w-2 h-2 bg-white/50 rounded-full" data-index="1"></div>
            </div>
        </div>
        @if (!request('q'))

            @if ($recommended->isNotEmpty())
                <div class="relative mt-4 shadow-2xl p-5 border rounded-lg">
                    <h2 class="text-xl font-bold mb-4">{{ __('Recommend for you') }}</h2>
                    <!-- Nút Prev -->
                    <button id="prevBtn"
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white text-xl px-2 py-1 border rounded shadow">&#10094;</button>
                    <!-- Container scroll ngang -->
                    <div id="productContainer" class="flex overflow-hidden gap-3 scroll-smooth">
                        @foreach ($recommended as $index => $product)
                            <div class="product-item w-full md:w-1/3 flex-shrink-0" data-page="{{ floor($index / 3) }}">
                                <div
                                    class="product-card border rounded-lg overflow-hidden shadow-sm hover:shadow-md hover:scale-105 transition flex flex-col">
                                    <div class="relative">
                                        @php
                                            $images = json_decode($product->images, true) ?? [];
                                            $mainImage = $images[0] ?? '/images/no-image.png'; // ảnh mặc định nếu không có
                                        @endphp
                                        <img src="{{ asset($mainImage) }}" alt="image"
                                            class="w-full h-48 object-cover">
                                    </div>
                                    <div class="p-3 space-y-1 flex-1 flex flex-col justify-between">
                                        <div>
                                            <a href="{{route('exchange.productDetail', $product->slug)}}">
                                                <h3 class="product-title text-xl font-semibold line-clamp-2">
                                                    {{ $product->name }}</h3>
                                            </a>

                                            <div class="flex justify-between items-center text-red-600 font-semibold mt-2">
                                                <p class="text-xl">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="product-location flex items-center gap-2 text-sm text-gray-600">
                                                <span>📍 {{ $product->location }}</span>
                                            </div>
                                            <div class="product-updated flex items-center text-gray-700 mt-1">
                                                <i class="fas fa-clock mr-2"></i>
                                                {{ __('Updated') }} {{ $product->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Nút Next -->
                    <button id="nextBtn"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white px-2 py-1 text-xl border rounded shadow">&#10095;</button>
                </div>
            @endif
        @endif

        <!-- Sản phẩm nổi bật -->
        <div class="p-4 shadow-2xl p-5 mt-4 border rounded-lg">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                🔥 {{ __('Featured Posts') }}
            </h2>
            <div id="product-container" class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 ">
                <!-- Card Item -->
                @include('exchange.paginate.product-list', ['products' => $products])
            </div>
            <div class="text-center mt-6">
                <button id="loadMoreBtn" data-page="2" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ __('Load More') }}
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md mt-4 mx-auto ">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">
                {{ __('Badminton Exchange') }} – {{ __('Trusted Secondhand Badminton Marketplace') }}
            </h2>

            <div id="short-text" class="text-gray-700 space-y-3 line-clamp-7">
                <p>
                    <span class="font-semibold">{{ __('Badminton Exchange') }}</span>
                    {{ __('is a leading platform for') }}
                    <span class="text-blue-600">{{ __('buying, selling, and exchanging secondhand badminton equipment') }}</span>
                    {{ __('in Vietnam, connecting a passionate badminton community.') }}
                </p>
                <p>
                    {{ __('We offer solutions that help you save up to') }}
                    <span class="font-medium">{{ __('70%') }}</span>
                    {{ __('on high-quality products from trusted brands like') }}
                    <span class="text-yellow-600">Yonex, Victor, Li-Ning</span>
                    {{ __('with full accessories from rackets, shoes to bags.') }}
                </p>
                <p>
                    <span class="font-medium">{{ __('Product variety') }}:</span>
                    {{ __('Rackets (Astrox, Nanoflare), shoes (Power Cushion), bags and genuine accessories.') }}
                </p>
                <p>
                    <span class="font-medium">{{ __('Secure transactions') }}:</span>
                    {{ __('Verified sellers, reputation rating system, and support for meeting at badminton courts.') }}
                </p>
                <p>
                    <span class="font-medium">{{ __('Easy search') }}:</span>
                    {{ __('Smart filters by brand, price, and product condition.') }}
                </p>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-4">
                <a href="{{ route('exchange.aboutUs') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                    {{ __('See more') }}
                </a>
                <div class="flex items-center">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border-2 border-white"
                             src="https://randomuser.me/api/portraits/women/12.jpg" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-white"
                             src="https://randomuser.me/api/portraits/men/32.jpg" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-white"
                             src="https://randomuser.me/api/portraits/women/44.jpg" alt="">
                    </div>
                    <span class="ml-3 text-sm text-gray-500">{{ __('Over') }} <span class="font-bold">1000+</span> {{ __('members') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md mt-4 mx-auto ">
            <div class="container my-5 ">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ __('Some satisfied customer reviews ') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <!-- Testimonial 1 -->
                    <div class="bg-white p-6 rounded-xl ">
                        <div class="flex items-center">
                            <img src="{{ asset('images/upload/league/tien.jpg') }}" alt="Avatar"
                                class="w-14 h-14 rounded-full object-cover">
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold mb-0">Minh Tiến</h5>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-700">
                            "An toàn, đáng tin cậy và dễ sử dụng giao diện người dùng. Nhìn chung, một cộng đồng tuyệt vời
                            sẽ ở!"
                        </p>
                        <span class="text-yellow-400 text-xl mt-2 block">★★★★★</span>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-white p-6 rounded-xl ">
                        <div class="flex items-center">
                            <img src="{{ asset('images/upload/league/vy.jpg') }}" alt="Avatar"
                                class="w-14 h-14 rounded-full object-cover">
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold mb-0">Thảo Vy</h5>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-700">
                            "Nền tảng dễ dàng và thuận tiện. Nó thuận tiện để mua và bán đồ. Hãy trò chuyện trực tiếp với
                            các băng chuyền khác."
                        </p>
                        <span class="text-yellow-400 text-xl mt-2 block">★★★★★</span>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-white p-6 rounded-xl ">
                        <div class="flex items-center">
                            <img src="{{ asset('images/upload/league/tung.jpg') }}" alt="Avatar"
                                class="w-14 h-14 rounded-full object-cover">
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold mb-0">Tùng Nguyễn</h5>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-700">
                            "Giao diện rất thân thiện và dễ sử dụng. "
                        </p>
                        <span class="text-yellow-400 text-xl mt-2 block">★★★★★</span>
                    </div>
                </div>

            </div>
        </div>


    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let currentPage = 1;

        $('#loadMoreBtn').on('click', function() {
            currentPage++;

            $.ajax({
                url: "{{ route('exchange.loadMore') }}",
                type: "GET",
                data: {
                    page: currentPage
                },
                beforeSend: function() {
                    $('#loadMoreBtn').text('Loading...'); // Hiển thị trạng thái loading
                },
                success: function(response) {
                    if (response.products.trim() === '') {
                        $('#loadMoreBtn').hide(); // Ẩn nút nếu không còn sản phẩm
                    } else {
                        $('#product-container').append(response.products);
                        $('#loadMoreBtn').text('Load more'); // Khôi phục lại trạng thái nút
                    }
                },
                error: function() {
                    alert('Error to update data!');
                    $('#loadMoreBtn').text('Load more'); // Khôi phục lại nếu lỗi
                }
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const items = document.querySelectorAll('.product-item');
        const itemsPerPage = 3;
        const totalPages = Math.ceil(items.length / itemsPerPage);
        let currentPage = 0;

        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');

        function renderPage(page) {
            items.forEach(item => {
                const pageIndex = parseInt(item.getAttribute('data-page'));
                item.style.display = (pageIndex === page) ? 'block' : 'none';
            });

            prevBtn.style.display = (page === 0) ? 'none' : 'block';
            nextBtn.style.display = (page === totalPages - 1) ? 'none' : 'block';
        }

        nextBtn.addEventListener('click', function() {
            if (currentPage < totalPages - 1) {
                currentPage++;
                renderPage(currentPage);
            }
        });

        prevBtn.addEventListener('click', function() {
            if (currentPage > 0) {
                currentPage--;
                renderPage(currentPage);
            }
        });

        renderPage(currentPage); // Hiển thị trang đầu
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sliderTrack = document.getElementById('slider-track');
        const slides = sliderTrack.children;
        const totalSlides = slides.length;
        const paginationDots = document.querySelectorAll('[data-index]');

        let currentIndex = 0;
        const slideInterval = 4000; // 4s

        function goToSlide(index) {
            const slideWidth = slides[0].clientWidth;
            sliderTrack.style.transform = `translateX(-${slideWidth * index}px)`;

            // Update dots
            paginationDots.forEach(dot => dot.classList.remove('bg-white', 'bg-orange-500'));
            paginationDots[index].classList.add('bg-orange-500');
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % totalSlides;
            goToSlide(currentIndex);
        }

        // Auto slide
        let interval = setInterval(nextSlide, slideInterval);

        // Optional: Click to navigate
        paginationDots.forEach(dot => {
            dot.addEventListener('click', function () {
                clearInterval(interval); // Stop auto slide on manual click
                currentIndex = parseInt(dot.getAttribute('data-index'));
                goToSlide(currentIndex);
            });
        });

        // Initial state
        goToSlide(currentIndex);
    });
</script>

