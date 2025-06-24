@extends('layouts.app')
@section('content')
    <!-- resources/views/exchange/user/info.blade.php -->
    <div class="container mx-auto mt-6 px-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Info -->
            <div class="lg:w-1/4 bg-white p-4 rounded-lg shadow">
                <div class="flex flex-col items-center">
                    <img src="{{ asset($user->profile_photo_path ?? '/images/no-image.png') }}"
                         class="w-20 h-20 rounded-full border">
                    <a href="{{ route('exchange.profilePost', Hashids::encode($user->id)) }}">
                        <h2 class="text-lg font-bold mt-2">{{ $user->name }}</h2>
                    </a>
                    <p class="text-lg text-gray-500">{{__('No ratting yet')}}</p>
                    <button class="bg-orange-500 text-white px-4 py-2 rounded mt-2 text-sm">+ {{__('Following')}}</button>
                </div>

                <ul class="mt-4 text-lg text-gray-700 space-y-2">
                    <li>📅 {{__('Join')}}:{{ $user->created_at?->diffForHumans() ?? __('N/A') }}
                    </li>
                    <li>
                        ✉ {{ $user->email }}<br>
                        📞 {{ $user->phone }}
                    </li>
                    <li>📍 {{__('Address')}}: {{ $user->address ?? 'Updating' }}</li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="lg:w-3/4 bg-white p-4 rounded-lg shadow">
                <!-- Tabs -->
                <div class="flex border-b">
                    <button id="tab-active"
                            class="tab-btn flex-1 text-center py-3 font-semibold border-b-2 border-orange-500 text-black">
                        {{__('Showing')}} ({{ count($activeProducts) ?? 0 }})
                    </button>
                    <button id="tab-sold"
                            class="tab-btn flex-1 text-center py-3 font-semibold border-b-2 border-transparent text-gray-400">
                        {{__('Sold')}} ({{ count($soldProducts) ?? 0 }})
                    </button>
                </div>

                <!-- Content -->
                <!-- Content -->
                <div class="p-4">
                    <!-- Đang hiển thị -->
                    <div id="products-active" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($activeProducts as $product)
                            <div class="border rounded-lg p-2">
                                <img src="{{ asset(json_decode($product->images)[0] ?? '/images/no-image.png') }}"
                                     class="w-full h-48 object-cover rounded-lg">
                                <a href="{{ route('exchange.productDetail', $product->slug) }}">
                                    <h3 class="font-bold mt-2">{{ $product->name }}</h3>
                                </a>
                                <p class="text-red-500 font-semibold">{{ number_format($product->price) }} đ</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $product->created_at->diffForHumans() }} - {{ $product->location }}
                                </p>
                            </div>
                        @empty
                            <p class="col-span-3 text-center text-gray-500">Chưa có sản phẩm đang hiển thị.</p>
                        @endforelse
                    </div>


                <!-- Đã bán (ẩn ban đầu) -->
                    <div id="products-sold" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 hidden">
                        @forelse($soldProducts as $product)
                            <div class="border rounded-lg p-2">
                                <img src="{{ asset(json_decode($product->images)[0] ?? '/images/no-image.png') }}"
                                     class="w-full h-48 object-cover rounded-lg">
                                <h3 class="font-bold mt-2">{{ $product->name }}</h3>
                                <p class="text-red-500 font-semibold">{{ number_format($product->price) }} đ</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $product->created_at->diffForHumans() }} - {{ $product->location }}
                                </p>
                            </div>
                        @empty
                            <p class="col-span-3 text-center text-gray-500">Chưa có sản phẩm đã bán.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabActive = document.getElementById("tab-active");
        const tabSold = document.getElementById("tab-sold");

        const contentActive = document.getElementById("products-active");
        const contentSold = document.getElementById("products-sold");

        function activateTab(activeBtn, inactiveBtn, showContent, hideContent) {
            activeBtn.classList.add("border-orange-500", "text-black");
            activeBtn.classList.remove("border-transparent", "text-gray-400");

            inactiveBtn.classList.remove("border-orange-500", "text-black");
            inactiveBtn.classList.add("border-transparent", "text-gray-400");

            showContent.classList.remove("hidden");
            hideContent.classList.add("hidden");
        }

        tabActive.addEventListener("click", function () {
            activateTab(tabActive, tabSold, contentActive, contentSold);
        });

        tabSold.addEventListener("click", function () {
            activateTab(tabSold, tabActive, contentSold, contentActive);
        });
    });
</script>

