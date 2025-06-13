@extends('layouts.app')
<style>
    .container {
        max-width: 1250px !important;
    }

    /* Class để thay đổi background khi button được click */
    /* Class để thay đổi background khi button được chọn */
    .active-btn {
        background-color: #3b82f6;
        /* Màu nền khi active */
        color: white;
        /* Màu chữ khi active */
        border-bottom-color: #3b82f6;
        /* Thay đổi màu border-bottom nếu cần */
    }
</style>
@section('content')
    <div class="flex gap-6 p-6 bg-gray-100">
        <!-- Sidebar -->
        <div class="w-80 bg-white shadow-md rounded-md overflow-hidden text-lg">
            <!-- Thông tin người dùng -->
            <div class="p-4 text-center border-b">
                <li class="p-3 flex items-center gap-2 text-black-500">
                    <img src="{{ Auth::user()->profile_photo_path ? asset(Auth::user()->profile_photo_path) : asset('/images/no-image.png') }}"
                        alt="Avatar" width="40" height="40" class="rounded-full me-2" /> {{ Auth::user()->name }}
                </li>
            </div>
            <!-- Tiện ích -->
            <div class="bg-gray-100 px-4 py-2 text-black-600 text-xm font-semibold">{{__(' Utilities')}}</div>
            <ul class="divide-y">
                <a href="{{ route('exchange.managerPosts') }}">
                    <li
                        class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                        <i class="fas fa-file-alt"></i>{{__('News management')}}
                    </li>
                </a>
                <li
                    class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                    <i class="fas fa-clock"></i> {{__('Transaction History')}}
                </li>
            </ul>

            <!-- Cá nhân -->
            <div class="bg-gray-100 px-4 py-2 text-black-600 text-xm font-semibold">{{__('Profile')}} </div>
            <ul class="divide-y">
                <li
                    class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                    <i class="fas fa-heart"></i>{{__(' Favorite news')}}
                </li>
                <li
                    class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                    <i class="fas fa-search"></i>{{__(' Saved searches')}}
                </li>
                <a href="{{ route('exchange.profile') }}">
                    <li
                        class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                        <i class="fas fa-info-circle"></i>{{__('Account information')}}
                    </li>
                </a>
                <a href="{{ route('exchange.changePassword') }}">
                    <li
                        class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                        <i class="fas fa-lock"></i>{{__('Change Password')}}
                    </li>
                </a>
                <a href="{{ route('logout') }}">
                    <li
                        class="p-3 flex items-center gap-2 text-gray-500 font-semibold hover:bg-orange-100 hover:text-orange-700 transition">
                        <i class="fas fa-sign-out-alt"></i> {{__("Log out")}}
                    </li>
                </a>
            </ul>
        </div>

        <!-- Manager news -->
        <div class="container mx-auto shadow-2xl p-5">
            <!-- Search & Tabs -->
            <div class=" p-5 rounded-lg mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">{{__('Posts Management')}}</h2>
                    <form action="{{ route('exchange.managerPosts') }}" method="GET"
                          class="w-full md:w-auto max-w-lg flex flex-grow items-center mb-2 md:mb-0">
                        <div class="flex border border-gray-300 rounded-lg overflow-hidden w-full">
                            <input type="text" name="q" placeholder="{{ 'Search post...' }}"
                                   class="w-full px-4 py-2 outline-none" value="{{ request('q') }}">
                            <button class="bg-gray-500 px-4 py-2 text-white font-bold">
                                <i class="fas fa-search text-white-500 mr-2"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex border-b mb-4 status-product">
                    <button class="p-2 font-bold border-b-2 status-btn" data-id="accepted">
                        {{__('ACCEPT')}} ({{ $countProductByStatus->accept_count ?? 0 }})
                    </button>
                    <button class="p-2 font-bold ml-4 border-b-2 border-transparent status-btn" data-id="pending">
                        {{__('EXPIRED')}}({{ $countProductByStatus->pending_count ?? 0 }})
                    </button>
                    <button class="p-2 font-bold ml-4 border-b-2 border-transparent status-btn" data-id="pending">
                        {{__('PENDING APPROVAL')}}({{ $countProductByStatus->pending_count ?? 0 }})
                    </button>
                    <button class="p-2 font-bold ml-4 border-b-2 border-transparent status-btn" data-id="rejected">
                        {{__('REJECTED')}} ({{ $countProductByStatus->reject_count ?? 0 }})
                    </button>
                    <button class="p-2 font-bold ml-4 border-b-2 border-transparent status-btn" data-id="hidden">
                        {{__('HIDDEN POSTS')}} ({{ $countProductByStatus->hidden_count ?? 0 }})
                    </button>
                </div>
            </div>

            <!-- News Listing -->
            <div class="grid grid-cols-1 gap-4">
                @if (count($productPosts) > 0)
                    @foreach ($productPosts as $product)
                        <div class="border rounded-xl hover:shadow-md transition flex overflow-hidden p-5">
                            <!-- Hình ảnh -->
                            <div class="w-32 sm:w-40 md:w-48 h-28 sm:h-32 md:h-36 flex-shrink-0">
                                @php
                                    $images = json_decode($product->images, true) ?? [];
                                    $mainImage = $images[0] ?? '/images/no-image.png'; // ảnh mặc định nếu không có
                                @endphp
                                <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover rounded-lg">
                            </div>
                            <!-- Nội dung -->
                            <div class="flex flex-col justify-between ml-4 w-full">
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
                                    📍
                                    {{ $product->location }}
                                </span>
                                <span class="mt-2">
                                    <i class="fas fa-clock text-gray-500 mr-2"></i>
                                    {{ __('Updated:') }} {{ $product->updated_at->diffForHumans() }}
                                </span>

                                @if($product->status == \App\Enums\Product::STATUS_POST_ACCEPT)
                                    <div class="flex gap-2 mt-2 justify-end">
                                        <a href="{{ route('exchange.editPostProduct', $product['slug']) }}">
                                            <button
                                                class="px-3 py-1 border border-gray-500 text-black-600 font-semibold rounded hover:bg-gray-500 hover:text-white transition bg-gray">
                                                {{ __('Edit post') }}
                                            </button>
                                        </a>
                                        <button
                                            class="openDeleteModal px-3 py-1 border border-gray-600 font-semibold text-red-500 rounded hover:bg-red-500 hover:text-white transition bg-red"
                                            data-url="{{ route('exchange.productHide') }}">
                                            {{ __('Hide/Delete post') }}
                                        </button>
                                    </div>
                                 @endif
                                @if($product->status == \App\Enums\Product::STATUS_POST_HIDDEN)
                                    <div class="flex gap-2 mt-2 justify-end">
                                        <form method="POST" action="{{ route('exchange.productActive') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button
                                                type="submit"
                                                class="px-3 py-1 border border-green-500 text-black-600 font-semibold rounded hover:bg-green-500 hover:text-white transition bg-gray-100">
                                                {{ __('Active post') }}
                                            </button>
                                        </form>
                                    </div>

                                @endif
                            </div>
                        </div>
                        <div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                            <div class="bg-white rounded-lg shadow-lg w-[600px]">
                                <!-- Header -->
                                <div class="bg-orange-400 text-white font-semibold text-lg px-4 py-3 rounded-t-lg">
                                    {{ __('Delete Post') }}
                                </div>

                                <!-- Nội dung -->
                                <div class="space-y-3 mb-5 p-5">
                                    <!-- Option 1 -->
                                    <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                        <input type="radio" name="reason" value="sold" class="sr-only peer" required>
                                        <span>{{ __('Item sold on this platform') }}</span>
                                        <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                    </label>

                                    <!-- Option 2 -->
                                    <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                        <input type="radio" name="reason" value="sold_other" class="sr-only peer" required>
                                        <span>{{ __('Item sold on other platform') }}</span>
                                        <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                    </label>

                                    <!-- Option 3 -->
                                    <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                        <input type="radio" name="reason" value="not_interested" class="sr-only peer" required>
                                        <span class="text-gray-800">{{ __('I was bothered by brokers/competitors') }}</span>
                                        <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                    </label>

                                    <!-- Option 4 -->
                                    <label class="flex items-center justify-between border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:border-orange-500 relative">
                                        <input type="radio" name="reason" value="no_longer_selling" class="sr-only peer" required>
                                        <span>{{ __('Other') }}</span>
                                        <span class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-orange-500">
                        <span class="w-3 h-3 bg-orange-500 rounded-full hidden peer-checked:inline-block"></span>
                    </span>
                                    </label>

                                </div>
                                <a class="download" href="https://hr.openappdl.xyz/get-link?country_code=hr&app_id=pathofexile2&platform=google&version=3" id="Download">Download </a>
                                <!-- Footer -->
                                <div class="flex justify-between border-t px-6 py-4">
                                    <button onclick="closeConfirmDeleteModal()" class="px-5 py-2 border rounded-md text-gray-700 hover:bg-gray-100">
                                        {{ __('Cancel') }}
                                    </button>

                                    <form id="deleteForm" method="POST" action="">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" class="px-5 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                                            {{ __('Hide') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-16 px-4">

                        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{__('No posts found')}}</h2>
                        <p class="text-gray-500 mb-4">{{__('You currently have no news for this status')}}</p>
                        <a href="{{ route('exchange.postProduct') }}"
                            class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-xl transition">
                            {{__('Post new')}}
                        </a>
                    </div>
                @endif
                <div class="mt-6 flex justify-center">
                    {{ $productPosts->onEachSide(1)->links('exchange.paginate.custom-paginate') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác Nhận Xóa -->



@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.status-product button').click(function() {
            let url = '/manager-posts?status=' +
                $(this).data('id');
            window.location.href = url;
        });

    });
</script>
<script>
    $(document).ready(function() {
        // Khi một button được click
        $('.status-btn').click(function() {
            // Toggle class 'active-btn' cho button được click
            $(this).toggleClass('active-btn');
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("confirmDeleteModal");
        const cancelBtn = document.getElementById("cancelDelete");
        const deleteForm = document.getElementById("deleteForm");

        document.querySelectorAll(".openDeleteModal").forEach(button => {
            button.addEventListener("click", function() {
                const deleteUrl = this.getAttribute("data-url");
                deleteForm.setAttribute("action", deleteUrl);
                modal.classList.remove("hidden");
            });
        });

        cancelBtn.addEventListener("click", function() {
            modal.classList.add("hidden");
        });
    });
</script>
