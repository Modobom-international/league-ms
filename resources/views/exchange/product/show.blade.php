@extends('layouts.app')
<style>
    .container {
        max-width: 1250px !important;
    }

    strong {
        font-weight: 400 !important;
    }

    .thumbnail:hover {
        border-color: #3b82f6; /* Đổi màu viền khi hover (tuỳ chọn) */
        transform: scale(1.05); /* Phóng nhẹ ảnh nhỏ khi hover (tuỳ chọn) */
        transition: all 0.2s ease;
    }

    #firstImage {
        display: block !important;
        width: 100%;
        height: auto;
        object-fit: contain;
    }
    .relative {
        overflow: visible;
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
            @php
                $images = json_decode($product->images, true) ?? [];
                if (!is_array($images)) {
                    $images = [];
                }
                // Chuẩn hóa đường dẫn
                $images = array_map(function ($img) {
                    return ltrim($img, '/');
                }, $images);

            @endphp

            @if(count($images) > 0)
                <div>
                    <!-- Ảnh chính -->
                    <div class="relative w-full h-[32rem] overflow-hidden border rounded-lg shadow-md p-2">
                        <img
                            id="firstImage"
                            src="{{ asset($images[0] ?? '') }}"
                            class="w-full h-96 object-contain rounded"
                        />
                        <!-- Nút Prev/Next -->
                        <button id="prevBtn" class="absolute top-1/2 left-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-2 py-1 rounded">‹</button>
                        <button id="nextBtn" class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white px-2 py-1 rounded">›</button>
                    </div>

                    <!-- Ảnh nhỏ -->
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach ($images as $idx => $img)
                            <img src="{{ asset($img) }}"
                                 class="thumbnail w-16 h-16 object-cover border cursor-pointer rounded"
                                 data-index="{{ $idx }}">
                        @endforeach
                    </div>
                </div>
            @else
                <div>Không có ảnh để hiển thị</div>
        @endif


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
                    @if($isLoggedIn && auth()->id() !== $product->user_id || !$isLoggedIn)
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
                    @elseif($isLoggedIn && auth()->id() == $product->user_id)
                        <div class=" w-full">
                            <div class="flex w-full gap-2">
                                <!-- Nút Delete -->
                                <button
                                    class="w-1/2 flex items-center justify-center gap-2 border border-red-500 text-red-500 px-4 py-2 rounded-lg text-base font-semibold hover:bg-red-500 hover:text-white transition openDeleteModal"
                                    data-url="{{ route('product.destroy', $product->id) }}"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                    <span>{{ __('Sold/Delete') }}</span>
                                </button>

                                <!-- Nút Edit -->
                                <a
                                    href="{{ route('exchange.editPostProduct', $product['slug']) }}"
                                    class="w-1/2 flex items-center justify-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg text-base font-semibold hover:bg-green-600"
                                >
                                    <i class="fas fa-edit"></i>
                                    <span>{{ __('Edit Post') }}</span>
                                </a>
                            </div>
                        </div>
                        <div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                            <div class="bg-white rounded-lg shadow-lg w-[400px]">
                                <!-- Header -->
                                <div class="bg-orange-400 text-white font-semibold text-lg px-4 py-3 rounded-t-lg">
                                    {{ 'Hide post' }}
                                </div>
                                <!-- Nội dung -->
                                <div class="p-6 space-y-3">
                                    <p class="text-gray-800 font-medium mb-2">{{ __('Please select the reason for hiding this post:') }}</p>
                                    <div class="space-y-2">
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="hide_reason" value="sold_on_platform" class="form-radio text-blue-600">
                                            <span>{{ __('Item sold on this platform') }}</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="hide_reason" value="sold_elsewhere" class="form-radio text-blue-600">
                                            <span>{{ __('Item sold on other platform') }}</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="hide_reason" value="no_longer_interested" class="form-radio text-blue-600">
                                            <span>{{ __('No longer interested in selling') }}</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="hide_reason" value="not_effective" class="form-radio text-blue-600">
                                            <span>{{ __('Listing not effective / poor results') }}</span>
                                        </label>
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="hide_reason" value="other" class="form-radio text-blue-600">
                                            <span>{{ __('Other') }}</span>
                                        </label>
                                    </div>
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
                                            {{ 'Hide' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
                <!-- Thông tin người bán -->
                <div class="border rounded-lg p-4 flex justify-between items-center mt-4 max-w-xl w-full">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-green-800 text-white rounded-full flex items-center justify-center text-lg font-semibold">
                            {{ strtoupper(substr($product->users->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ $product->users->name }}</div>
                            <div class="text-sm text-gray-600">Phản hồi: -- <a href="#"
                                                                               class="text-blue-600 underline">{{ 1 }} đã bán</a></div>
                            <div class="text-sm text-gray-500 flex items-center space-x-1">
                                <span class="w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
                                <span>{{ __('Active') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right border-l pl-4">
                        <div class="text-lg font-semibold text-gray-800 flex items-center justify-end gap-1">
                            {{ 2 }} <span class="text-yellow-500">★</span></div>
                        <a href="#" class="text-sm text-blue-600 underline">{{ 3 }}
                            {{ __('reviews') }}</a>
                    </div>
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
                <a href="{{route('exchange.postProduct')}}" class="text-orange-500 font-semibold"> {{'NEW POST'}}</a>
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
                                @php
                                    $images = json_decode($related->images, true) ?? [];
                                    $mainImage = $images[0] ?? '/images/no-image.png'; // ảnh mặc định nếu không có
                                @endphp
                                <img src="{{ asset($mainImage) }}" class="w-full h-full object-cover" alt="{{ $related->name }}">
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Đây là mảng ảnh, được Laravel render thành JS array
            const images = @json($images);
            const mainImage = document.getElementById("firstImage");
            console.log(mainImage)
            const prevBtn = document.getElementById("prevBtn");
            console.log(prevBtn)
            const nextBtn = document.getElementById("nextBtn");
            console.log(nextBtn)
            let currentIndex = 0;

            function updateImage() {
                mainImage.src = images[currentIndex]
                    ? `{{ asset('') }}` + images[currentIndex]
                    : '';
            }

            prevBtn.addEventListener("click", function () {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateImage();
                }
            });

            nextBtn.addEventListener("click", function () {
                if (currentIndex < images.length - 1) {
                    currentIndex++;
                    updateImage();
                }
            });
        });
    </script>
@endsection
