@extends('layouts.app')
@section('content')
    <div class="p-5 relative w-full  bg-red bg-center flex justify-center">
        <form action="{{ route('products.searchInProduct') }}" method="GET" class="flex items-center space-x-3">
            <!-- Lọc theo khu vực -->
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg">🔍</button>

            <select name="location" class="px-3 py-2 rounded-lg border border-gray-300">
                <option value="">{{'All Location'}}</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province }}">{{ $province }}</option>
                @endforeach
            </select>

            <!-- Lọc theo danh mục -->
            <select name="category" class="px-3 py-2 rounded-lg border border-gray-300">
                <option value="">{{'Category'}}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <div class="relative">
                <button type="button" id="togglePriceFilter"
                        class="px-3 py-2 rounded-lg border border-gray-300">
                    {{'Chose price'}}
                </button>

                <!-- Danh sách khoảng giá (ẩn ban đầu) -->
                <div id="priceFilter" class="absolute left-0 w-[400px] max-w-xl bg-white shadow-lg rounded-lg border mt-2 p-6 hidden">

                <label class="text-gray-700 font-semibold">{{'Chose price'}}:</label>

                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @php
                            $priceRanges = [
                                ['label' => 'Dưới 500000', 'min' => 0, 'max' => 500000],
                                ['label' => '500000 - 10000000', 'min' => 500000, 'max' => 10000000],
                                ['label' => '10000000 - 20000000', 'min' => 10000000, 'max' => 20000000],
                                ['label' => 'Trên 20000000', 'min' => 20000000, 'max' => 200000000],

                            ];
                        @endphp

                        @foreach ($priceRanges as $range)
                            <button type="button" class="price-option px-3 py-2 text-center border rounded-lg bg-white text-gray-800"
                                    data-min="{{ $range['min'] }}" data-max="{{ $range['max'] }}">
                                {{ $range['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Nút đóng -->
                    <button type="button" id="closePriceFilter"
                            class="mt-3 px-4 py-2 bg-gray-400 text-white rounded-lg w-full">
                        {{'Close'}}
                    </button>
                </div>
            </div>

            <!-- Input ẩn để submit giá trị -->
            <input type="hidden" name="min_price" id="minPrice">
            <input type="hidden" name="max_price" id="maxPrice">



            <!-- Lọc theo tình trạng sản phẩm -->
            <select name="condition" class="px-3 py-2 rounded-lg border border-gray-300">
                <option value="">{{'status'}}</option>
                <option value="new">{{'New'}}</option>
                <option value="used">{{'Used'}}</option>
            </select>



            <!-- Nút tìm kiếm -->
        </form>

    </div>
    <div class="container mx-auto px-4 mt-4">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @if(count($products) > 0)
                @foreach ($products as $product)
                    <div class="bg-white p-6 rounded-lg shadow-md hover:scale-105">
                        <a href="{{route('exchange.productDetail', $product['slug'])}}">
                            @php
                                $images = json_decode($product->images, true) ?? [];
                                $mainImage = $images[0] ?? '/images/no-image.png'; // ảnh mặc định nếu không có
                            @endphp
                            <img src="{{ asset($mainImage) }}" class=" w-full  object-cover rounded-lg">
                            <h4 class="mt-2 font-semibold">{{ $product->name }}</h4>
                        </a>
                        <p class="text-gray-600">{{ $product->condition }}</p>
                        <p class="text-red-500 font-bold">{{ number_format($product->price, 0, ',', '.') }} đ</p>
                        <div class="flex items-center text-gray-500 text-sm mt-2">
                            <i class="fas fa-map-marker-alt "></i> {{ $product->location }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center">
                    <h4 >{{ __('No products found!') }}</h4>
                </div>
            @endif
        </div>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleButton = document.getElementById("togglePriceFilter");
        const priceFilter = document.getElementById("priceFilter");
        const closeButton = document.getElementById("closePriceFilter");
        const priceOptions = document.querySelectorAll(".price-option");
        const minPriceInput = document.getElementById("minPrice");
        const maxPriceInput = document.getElementById("maxPrice");

        // Mở menu chọn giá
        toggleButton.addEventListener("click", function () {
            priceFilter.classList.toggle("hidden");
        });

        // Đóng menu khi nhấn "Đóng"
        closeButton.addEventListener("click", function () {
            priceFilter.classList.add("hidden");
        });

        // Chọn giá và cập nhật input
        priceOptions.forEach(button => {
            button.addEventListener("click", function () {
                let min = this.getAttribute("data-min");
                let max = this.getAttribute("data-max");

                // Cập nhật input
                minPriceInput.value = min;
                maxPriceInput.value = max;

                // Thay đổi màu cho nút được chọn
                priceOptions.forEach(btn => btn.classList.remove("bg-blue-500", "text-white"));
                this.classList.add("bg-blue-500", "text-white");

                // Ẩn menu sau khi chọn
                priceFilter.classList.add("hidden");
            });
        });
    });

</script>
