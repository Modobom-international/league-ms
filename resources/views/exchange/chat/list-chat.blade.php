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
            <div class="px-4 pb-2 mt-4">
                <input type="text" id="searchChat" placeholder="🔍 Tìm kiếm..."
                       class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300">
            </div>
            <ul>
                @foreach($conversations as $conv)
                    @php
                        $authUser = Auth::guard('canstum')->user();
                        $otherUser = $conv->buyer_id === $authUser->id ? $conv->seller : $conv->buyer;
                        $images = json_decode($conv->product->images, true) ?? [];
                        $mainImage = $images[0] ?? '/images/no-image.png';
                    @endphp

                    <li class="hover:bg-gray-100 cursor-pointer border-t chat-item"
                        data-name="{{ strtolower($otherUser->name) }}"
                        data-product="{{ strtolower($conv->product->name) }}">
                        <a href="{{ route('chat.show', $conv->id) }}">
                            <div class="flex items-center justify-between">
                                <div class="flex p-3">
                                    <img class="w-[3rem] h-[3rem] rounded-lg object-cover"
                                         src="{{ asset($otherUser->profile_photo_path ?? '/images/default-avatar.png') }}"
                                         alt="avatar">
                                    <div class="ml-4">
                                        <div class="font-semibold text-black">{{ $otherUser->name }}</div>
                                        <div class="text-sm text-gray-700">{{ Str::limit($conv->product->name, 20) }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit(optional($conv->messages->last())->content, 30) }}
                                        </div>
                                    </div>
                                </div>
                                <img class="w-20 h-20 rounded-lg mr-4 object-cover p-2"
                                     src="{{ asset($mainImage) }}" alt="product image">
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>


        <!-- Khung chat -->
        <div class="chat-detail w-2/3 flex flex-col justify-between h-full">
            <div class="flex-grow flex items-center justify-center text-black-700">
                <div class="relative w-full overflow-hidden rounded-xl mt-4" id="custom-slider">
                    <!-- Slides wrapper -->
                    <div class="flex transition-transform duration-700 ease-in-out" id="slider-track">
                        <div class="w-full shrink-0 flex flex-col items-center">
                            <img src="{{ asset('images/exchange/chat1.jpg') }}" class="w-50 h-50 object-cover" alt="Slide 1" />
                            <p class="text-center text-gray-700 text-xl mt-2 font-semibold">
                                {{ __('Please select a conversation to get started.') }}
                            </p>
                        </div>
                        <div class="w-full shrink-0 flex flex-col items-center">
                            <img src="{{ asset('images/exchange/chat3.jpg') }}" class="w-50 h-50 object-cover" alt="Slide 2" />
                            <p class="text-center text-gray-700 text-xl mt-2 font-semibold">
                                {{ __('Quick and convenient exchanges.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Pagination Dots (currently hidden) -->
                    <div class="w-full flex justify-center mt-3 z-10 hidden">
                        <div class="flex space-x-2" id="slider-dots">
                            <div class="w-2 h-2 bg-white/50 rounded-full" data-index="0"></div>
                            <div class="w-2 h-2 bg-white/50 rounded-full" data-index="1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
@endsection
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchChat');
        const chatItems = document.querySelectorAll('.chat-item');

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();

            chatItems.forEach(item => {
                const name = item.dataset.name || '';
                const product = item.dataset.product || '';
                if (name.includes(keyword) || product.includes(keyword)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>


