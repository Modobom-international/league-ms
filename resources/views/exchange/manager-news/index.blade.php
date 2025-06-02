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
                <a href="{{ route('exchange.managerNews') }}">
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
                    <h2 class="text-xl font-bold">{{__('News management')}}</h2>
                    <form action="{{ route('exchange.managerNews') }}" method="GET"
                          class="w-full md:w-auto max-w-lg flex flex-grow items-center mb-2 md:mb-0">
                        <div class="flex border border-gray-300 rounded-lg overflow-hidden w-full">
                            <input type="text" name="q" placeholder="{{ 'Search product...' }}"
                                   class="w-full px-4 py-2 outline-none" value="{{ request('q') }}">
                            <button class="bg-gray-500 px-4 py-2 text-white font-bold">
                                <i class="fas fa-search text-white-500 mr-2"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex border-b mb-4 status-product">
                    <button class="p-2 font-bold border-b-2 status-btn" data-id="accepted">
                        {{__('ALL')}} ({{ $countProductByStatus->accept_count ?? 0 }})
                    </button>
                    <button class="p-2 font-bold ml-4 border-b-2 border-transparent status-btn" data-id="pending">
                        {{__('PENDING APPROVAL')}}({{ $countProductByStatus->pending_count ?? 0 }})
                    </button>
                    <button class="p-2 font-bold ml-4 border-b-2 border-transparent status-btn" data-id="rejected">
                        {{__('REJECTED')}} ({{ $countProductByStatus->reject_count ?? 0 }})
                    </button>
                </div>
            </div>

            <!-- News Listing -->
            <div class="grid grid-cols-1 gap-4">
                @if (count($productNews) > 0)
                    @foreach ($productNews as $product)
                        <div class="border rounded-xl hover:shadow-md transition flex overflow-hidden p-5">
                            <!-- Hình ảnh -->
                            <div class="w-32 sm:w-40 md:w-48 h-28 sm:h-32 md:h-36 flex-shrink-0">
                                <img src="{{ asset($product->images) }}" alt="{{ $product->name }}"
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
                                    <i class="fas fa-clock text-gray-500 mr-2"></i>
                                    {{ __('Updated') }} {{ $product->updated_at->diffForHumans() }}
                                </span>
                                <span>
                                    📍
                                    {{ $product->location }}
                                </span>

                                <div class="flex gap-2 mt-2">
                                    <a href="{{ route('exchange.editNews', $product['slug']) }}">
                                        <button
                                            class="px-3 py-1 border border-gray-500 text-gray-500 rounded hover:bg-gray-500 hover:text-white transition">
                                            {{__('Edit news')}}
                                        </button>
                                    </a>
                                    <button
                                        class="openDeleteModal px-3 py-1 border border-red-500 text-red-500 rounded hover:bg-red-500 hover:text-white transition"
                                        data-url="{{ route('product.destroy', $product->id) }}">
                                        {{__(' Delete post')}}
                                    </button>
                                </div>
                            </div>
                        </div>

                    @endforeach
                @else
                    <div class="text-center py-16 px-4">

                        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{__('No posts found')}}</h2>
                        <p class="text-gray-500 mb-4">{{__('You currently have no news for this status')}}</p>
                        <a href="{{ route('exchange.ProductNews') }}"
                            class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-xl transition">
                            {{__('Post news')}}
                        </a>
                    </div>
                @endif
                <div class="mt-6 flex justify-center">
                    {{ $productNews->onEachSide(1)->links('exchange.paginate.custom-paginate') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác Nhận Xóa -->

    <div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-lg w-[400px]">
            <!-- Header -->
            <div class="bg-orange-400 text-white font-semibold text-lg px-4 py-3 rounded-t-lg">
                {{ 'Delete post' }}
            </div>

            <!-- Nội dung -->
            <div class="p-6">
                <p class="text-gray-800 font-medium">
                    {{ 'When you no longer want the story to appear, select "Delete"' }}
                </p>

            </div>

            <!-- Footer -->
            <div class="flex justify-between border-t px-6 py-4">
                <button id="cancelDelete" class="px-5 py-2 border rounded-md text-gray-700 hover:bg-gray-100">
                    {{ 'Cancel' }}
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                        {{ 'Delete' }}
                    </button>
                </form>
            </div>
        </div>
    </div>




@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.status-product button').click(function() {
            let url = '/manager-news?status=' +
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
