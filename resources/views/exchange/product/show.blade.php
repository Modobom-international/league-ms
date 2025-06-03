@extends('layouts.app')
<style>
    .container {
        max-width: 1250px !important;
    }

    strong {
        font-weight: 400 !important;
    }
</style>
@section('content')
    <div class="container mx-auto py-6 px-4">
        <!-- Breadcrumb -->
        <div class="text-gray-600 rounded-lg mb-4">
            <a href="{{ route('exchange.home') }}" class="hover:underline">{{'Homepage'}}</a> >
            <a href="" class="hover:underline">{{ $product->categories->name }}</a> >
            <span class="text-gray-800 font-semibold">{{ $product->name }}</span>
        </div>

        <div class="grid grid-cols-1 bg-white shadow-md md:grid-cols-2 gap-5 p-6">
            <!-- Hình ảnh sản phẩm -->
            <div>
                <img src="{{ asset($product->images) }}" id="mainImage" class="w-full rounded-lg shadow-md">
                <div class="flex space-x-2 mt-2">
                    <img src="{{ asset($product->images) }}" onmouseover="changeMainImage('{{ asset($product->images) }}')" class="w-16 h-16 object-cover rounded border cursor-pointer">
                    @foreach($product->productImages as $image)
                        <img src="{{ asset($image->image_url) }}" class="w-16 h-16 object-cover rounded border cursor-pointer"
                             onmouseover="changeMainImage('{{ asset($image->image_url) }}')">
                    @endforeach
                </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class=" rounded-lg">
                <h1 class="text-2xl font-bold uppercase p-0">{{ $product->name }}</h1>

                <div class="flex items-center text-black-700 mt-2">
                    <i class="fas fa-check-circle text-gray-500 mr-2"></i>
                    <p>{{ $product->condition }}</p>
                </div>

                <div class="flex items-center text-black-700 mt-1">
                    <i class="fas fa-tags text-gray-500 mr-2"></i>
                    <p>{{ $product->categories->name ?? 'Danh mục khác' }}</p>
                </div>

                <p class="text-red-700 text-3xl font-semibold mt-2">
                    {{ number_format($product->price, 0, ',', '.') }} đ
                </p>

                <div class="flex items-center text-black-700 mt-2">
                    <i class="fas fa-map-marker-alt text-gray-500 mr-2"></i>
                    {{ $product->location }}
                </div>

                <div class="flex items-center text-black-700 mt-1">
                    <i class="fas fa-clock text-gray-500 mr-2"></i>
                    {{ __('Updated') }} {{ $product->updated_at->diffForHumans() }}
                </div>

                @php $isLoggedIn = Auth::check(); @endphp

                <div class="mt-4 flex space-x-2">


                    @if($isLoggedIn && auth()->id() !== $product->user_id)
                        <button
                            id="showPhoneBtn"
                            class="flex-1 text-center border border-gray-300 px-4 py-2 rounded-lg text-lg font-semibold hover:bg-gray-100"
                            onclick="{{ $isLoggedIn ? 'showPhoneNumber()' : 'showLoginModal()' }}">
                            {{ $product->users->phone ? substr($product->users->phone, 0, 6) . '****' : "updating"  }}
                        </button>
                        <a
                            href="{{ route('chat.withSeller', ['product' => $product->id]) }}"
                            class="flex-1 text-center bg-green-500 text-white px-4 py-2 rounded-lg text-lg font-semibold hover:bg-green-600">
                            💬 Chat
                        </a>
                    @elseif(auth()->id() == $product->user_id)
                        <div class="mt-4 flex gap-2">
                            <!-- Nút Delete -->
                            <button
                                class="flex-1 flex items-center justify-center gap-2 border border-red-500 text-red-500 px-4 py-2 rounded-lg text-base font-semibold hover:bg-red-500 hover:text-white transition openDeleteModal"
                                data-url="{{ route('product.destroy', $product->id) }}"
                            >
                                <i class="fas fa-trash-alt"></i>
                                <span>{{ __('Sold/Delete') }}</span>
                            </button>

                            <!-- Nút Edit -->
                            <a
                                href="{{ route('exchange.editNews', $product['slug']) }}"
                                class="flex-1 flex items-center justify-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg text-base font-semibold hover:bg-green-600"
                            >
                                <i class="fas fa-edit"></i>
                                <span>{{ __('Edit Post') }}</span>
                            </a>
                        </div>
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

                    @endif

                </div>

                <!-- Modal đăng nhập -->
                <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
                        <h2 class="text-xl font-bold mb-4">{{ __('You need to login to view phone number') }}</h2>
                        <p class="mb-4 text-gray-600">{{ __("Please login to continue") }}.</p>
                        <div class="flex justify-end space-x-2">
                            <button onclick="closeLoginModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">{{ __("Cancel") }}</button>
                            <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __("Login") }}</a>
                        </div>
                    </div>
                </div>

            @if($isLoggedIn && auth()->id() !== $product->user_id)
                <!-- Thông tin người bán -->
                <div class="mt-6 p-4 bg-gray-100 rounded-lg flex items-center">
                    <img src="{{ asset($product->users->profile_photo_path ?? 'default-avatar.png') }}" class="w-12 h-12 rounded-full border">
                    <div class="ml-3">
                        <h3 class="font-bold">{{ $product->users->name }}</h3>
                        <p class="text-gray-500">{{ 'Feedback' }}: 91% • </p>
                        <p class="text-gray-400">{{ 'Active' }}</p>
                    </div>
                </div>

                <div class="mt-4 flex items-center">
                    <span class="text-yellow-500 text-xl">⭐ {{ $product->rating }}</span>
                    <a href="#" class="text-blue-500 ml-2">{{ $product->reviews_count }} {{ 'Rate' }}</a>
                </div>

                <div class="mt-4 flex space-x-2">
                    <a href="#" class="flex-1 text-center bg-gray-200 text-black px-4 py-2 rounded-lg">{{ 'Does this product come in other colors?' }}</a>
                    <a href="#" class="flex-1 text-center bg-gray-200 text-black px-4 py-2 rounded-lg">{{ 'Is this new or used?' }}</a>
                </div>
                @endif
            </div>
        </div>


        <!-- Mô tả sản phẩm -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                <!-- Mô tả chi tiết -->
                <h2 class="text-lg font-semibold mb-2">{{'Detailed description'}}</h2>
            <p class="mb-2 prose prose-sm " >
                {!! nl2br(e($product->description)) !!}
            </p>

        <!-- Thông số chi tiết -->
            <h2 class="text-lg font-semibold mt-4 mb-2">{{'Detailed specifications'}}</h2>
            <div class="border rounded-lg overflow-hidden grid grid-cols-[auto_1fr]">
                <div class="bg-gray-100 text-gray-600 px-4 py-3 font-medium border-r whitespace-nowrap">{{__("Status")}}:</div>
                <div class="px-4 py-3 font-semibold">{{ $product->condition }}</div>

                <div class="bg-gray-100 text-gray-600 px-4 py-3 font-medium border-t border-r whitespace-nowrap">{{__("Category")}}:</div>
                <div class="px-4 py-3 font-semibold border-t">{{ $product->categories->name }}</div>

                <div class="bg-gray-100 text-gray-600 px-4 py-3 font-medium border-t border-r whitespace-nowrap">{{__("Usage Information")}}:</div>
                <div class="px-4 py-3 font-semibold border-t">{{ __('printed on packaging') }}</div>
            </div>


            <!-- Đăng bán -->
            <div class="flex items-center justify-between bg-gray-100 p-3 mt-4 rounded-lg">
                <div class="flex items-center gap-2">
                    <!-- Thay ảnh bằng icon camera -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                         class="w-8 h-8 text-gray-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7a2 2 0 012-2h2l1-2h6l1 2h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                        <circle cx="12" cy="13" r="3" />
                    </svg>

                    <span class="text-gray-600">{{'Do you have similar products?'}}</span>
                </div>
                <a href="{{route('exchange.ProductNews')}}" class="text-orange-500 font-semibold"> {{'POST NEW'}}</a>
            </div>

        </div>

        <!-- Sản phẩm liên quan -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">{{'Related products'}}</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    <div class="border rounded-lg p-3 flex flex-col h-full shadow-sm hover:shadow-md transition">
                        <a href="{{ route('exchange.productDetail', $related['slug']) }}" class="flex flex-col h-full">
                            {{-- Ảnh --}}
                            <div class="w-full h-40 overflow-hidden rounded">
                                <img src="{{ asset($related->images) }}" class="w-full h-full object-cover" alt="{{ $related->name }}">
                            </div>

                            {{-- Nội dung --}}
                            <div class="flex flex-col justify-between flex-grow mt-3 space-y-1">
                                <h3 class="text-base font-semibold line-clamp-2">{{ $related->name }}</h3>
                                <p class="text-gray-500 text-sm">{{ $related->condition === 'new' ? 'Mới' : 'Đã sử dụng' }}</p>
                                <p class="text-red-500 font-bold">{{ number_format($related->price, 0, ',', '.') }} đ</p>
                                <p class="text-sm text-gray-600">📍 {{ $related->location }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        </div>
    </div>
@endsection
<script>
    function changeMainImage(imageUrl) {
        document.getElementById('mainImage').src = imageUrl;
    }
</script>

<!-- Script xử lý show number -->
<script>
    function showPhoneNumber() {
        const btn = document.getElementById('showPhoneBtn');
        btn.textContent = "{{ $product->users->phone }}"; // thay bằng số thực tế nếu cần
        btn.disabled = true;
        btn.classList.add('cursor-default', 'bg-gray-100', 'text-black');
    }

    function showLoginModal() {
        document.getElementById('loginModal').classList.remove('hidden');
    }

    function closeLoginModal() {
        document.getElementById('loginModal').classList.add('hidden');
    }
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
