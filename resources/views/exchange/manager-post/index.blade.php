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
    <div class="flex gap-6 bg-gray-100 mt-4">
        <!-- Sidebar -->
        <!-- Manager news -->
        <div class="container mx-auto">
            <!-- Search & Tabs -->
            <!-- Wrapper Sticky -->
            <div class="sticky top-[155px] md:top-[56px] z-30 bg-white pt-4 pb-2 shadow-md rounded-b-md">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3 p-4">
                    <h2 class="text-xl font-bold">{{__('Posts Management')}}</h2>
                    <form action="{{ route('exchange.managerPosts') }}" method="GET" class="w-full md:w-auto max-w-lg flex flex-grow items-center">
                        <div class="flex border border-gray-300 rounded-lg overflow-hidden w-full">
                            <input type="text" name="q" placeholder="{{ 'Search post...' }}"
                                   class="w-full px-4 py-2 outline-none" value="{{ request('q') }}">
                            <button class="bg-gray-500 px-4 py-2 text-white font-bold">
                                <i class="fas fa-search text-white mr-2"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabs scrollable -->
                <div class="overflow-x-auto px-4">
                    <div class="flex whitespace-nowrap gap-2 border-b status-product">
                        <button class="p-2 font-bold border-b-2 status-btn" data-id="accepted">
                            {{__('ACCEPT')}} ({{ $countProductByStatus->accept_count ?? 0 }})
                        </button>
                        <button class="p-2 font-bold border-b-2 border-transparent status-btn" data-id="pending">
                            {{__('EXPIRED')}}({{ $countProductByStatus->pending_count ?? 0 }})
                        </button>
                        <button class="p-2 font-bold border-b-2 border-transparent status-btn" data-id="pending">
                            {{__('PENDING APPROVAL')}}({{ $countProductByStatus->pending_count ?? 0 }})
                        </button>
                        <button class="p-2 font-bold border-b-2 border-transparent status-btn" data-id="rejected">
                            {{__('REJECTED')}} ({{ $countProductByStatus->reject_count ?? 0 }})
                        </button>
                        <button class="p-2 font-bold border-b-2 border-transparent status-btn" data-id="hidden">
                            {{__('HIDDEN POSTS')}} ({{ $countProductByStatus->hidden_count ?? 0 }})
                        </button>
                    </div>
                </div>
            </div>


            <!-- News Listing -->
            <div class="grid p-2 grid-cols-1 gap-4 mt-4">
                @if (count($productPosts) > 0)
                    @foreach ($productPosts as $product)
                        <div class="border rounded-xl hover:shadow-md transition flex overflow-hidden p-5 flex-col sm:flex-row">
                            <!-- Hình ảnh -->
                            <div class="w-full sm:w-40 md:w-48 h-48 sm:h-32 md:h-36 flex-shrink-0">
                                @php
                                    $images = json_decode($product->images, true) ?? [];
                                    $mainImage = $images[0] ?? '/images/no-image.png';
                                @endphp
                                <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover rounded-lg">
                            </div>

                            <!-- Nội dung -->
                            <div class="flex flex-col justify-between mt-4 sm:mt-0 sm:ml-4 w-full">
                                <div>
                                    <a href="{{ route('exchange.productDetail', $product->slug) }}"
                                       class="block font-semibold text-xl text-gray-900 hover:text-yellow-500 line-clamp-1">
                                        {{ $product->name }}
                                    </a>
                                    <h2 class="text-red-600 font-bold text-xl mt-1">
                                        {{ is_numeric($product->price) ? number_format($product->price, 0, ',', '.') . ' đ' : $product->price }}
                                    </h2>
                                </div>

                                <p class="mt-1">
                                    {{ Str::limit(strip_tags($product->description), 100, '...') }}
                                </p>

                                <span class="mt-2">📍 {{ $product->location }}</span>
                                <span class="mt-2"><i class="fas fa-clock text-gray-500 mr-2"></i>{{ __('Updated:') }} {{ $product->updated_at->diffForHumans() }}</span>

                                <!-- Nút xử lý -->
                                @if($product->status == \App\Enums\Product::STATUS_POST_ACCEPT)
                                    <div class="flex flex-wrap gap-2 mt-2 justify-end">
                                        <a href="{{ route('exchange.editPostProduct', $product['slug']) }}">
                                            <button class="px-3 py-2 border border-gray-500 text-sm text-black-600 font-semibold rounded hover:bg-gray-500 hover:text-white transition bg-gray-100">
                                                {{ __('Edit post') }}
                                            </button>
                                        </a>
                                        <button class="openDeleteModal px-3 py-2 border border-gray-600 text-sm font-semibold text-red-500 rounded hover:bg-red-500 hover:text-white transition bg-red"
                                                data-url="{{ route('exchange.productHide') }}">
                                            {{ __('Hide/Delete post') }}
                                        </button>
                                    </div>
                                @endif

                                @if($product->status == \App\Enums\Product::STATUS_POST_HIDDEN)
                                    <div class="flex flex-wrap gap-2 mt-2 justify-end">
                                        <form method="POST" action="{{ route('exchange.productActive') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit"
                                                    class="px-3 py-1 border border-green-500 text-sm text-black-600 font-semibold rounded hover:bg-green-500 hover:text-white transition bg-gray-100">
                                                {{ __('Active post') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
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

            <!-- Pagination -->
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
