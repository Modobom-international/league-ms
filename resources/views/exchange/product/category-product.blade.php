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

    .top-16 {
        top: 3.5rem !important;
    }

    span {
        font-weight: 400 !important;
    }

    strong {
        font-weight: 400 !important;
    }
</style>
@section('content')
    <!-- Thanh lọc sản phẩm dạng sticky -->
    <div class="sticky top-16 z-40 bg-white border-b">
        <div class="container mx-auto px-4">
            <form action="{{ route('exchange.categoryDetail', $category->slug) }}" method="GET" class="w-full">
                <div class="flex flex-wrap gap-3 py-3 items-center">
                    <input type="hidden" name="slug" value="{{ $category->slug }}">
                    <!-- Location Filter -->
                    <select name="location" class="w-96 px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">{{ __('Location') }}</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province }}" {{ request('location') == $province ? 'selected' : '' }}>
                                {{ $province }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Condition Filter -->
                    <select name="condition" class="w-96 px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">{{ 'Condition' }}</option>
                        <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>{{ __('New') }}
                        </option>
                        <option value="used" {{ request('condition') == 'used' ? 'selected' : '' }}>{{ __('Use') }}
                        </option>
                    </select>

                    {{-- Sort Filter --}}
                    <select name="sort" class="w-96 px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">{{ __('Arrange') }}</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                            {{ __('Low') }} → {{ __('High') }}</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                            {{ __('High') }} → {{ __('Low') }}</option>
                    </select>

                    <!-- Search Button -->
                    <button type="submit"
                        class="px-6 py-2 bg-gray-500 text-white  rounded-xl hover:bg-gray-500 transition">
                        <i class="fas fa-filter text-white-500 mr-2"></i> {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="container mx-auto px-4 text-gray-600 mt-4  mb-4">
        <a href="{{ route('exchange.home') }}" class="hover:underline">{{ 'Homepage' }}</a> >
        <span style="font-weight: 700 !important; ">{{ $category->name }}</span>
    </div>
    <div class="container mx-auto px-4">
        <div class="mt-4">
            <div class="w-full lg:w-[70%] space-y-4 shadow-sm">
                @if (isset($products) && count($products) > 0)
                    @foreach ($products as $product)
                        <div class="border rounded-xl  hover:shadow-md transition flex overflow-hidden p-5">
                            <!-- Hình ảnh -->
                            <div class="w-32 sm:w-40 md:w-48 h-28 sm:h-32 md:h-36 flex-shrink-0">
                                <img src="{{ asset($product->images) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover rounded-l-xl">
                            </div>
                            <!-- Nội dung -->
                            <div class="flex flex-col justify-between p-3 w-full">
                                <div>
                                    <a href="{{ route('exchange.productDetail', $product->slug) }}"
                                        class="block font-semibold text-xl text-gray-900 hover:text-yellow-500 line-clamp-1 text-xxl">
                                        {{ $product->name }}
                                    </a>
                                    <h2 class="text-red-600 font-bold text-xl mt-1">
                                        {{ is_numeric($product->price) ? number_format($product->price, 0, ',', '.') . ' đ' : $product->price }}
                                    </h2>
                                </div>

                                <!-- Mô tả ngắn -->
                                <p class="">
                                    {{ Str::limit(strip_tags($product->description), 100, '...') }}
                                </p>
                                <!-- Địa điểm -->
                                <span class="mt-2">
                                    <i class="fas fa-clock text-gray-500 mr-2"></i>
                                    {{ __('Updated') }} {{ $product->updated_at->diffForHumans() }} 📍
                                    {{ $product->location }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center">
                        <h4>{{ __('No products found') }}</h4>
                    </div>
                @endif
            </div>

        </div>
        <div class="mt-6 flex justify-center">
            {{ $products->onEachSide(1)->links('exchange.paginate.custom-paginate') }}
        </div>
    </div>
@endsection
