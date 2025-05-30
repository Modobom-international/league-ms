<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chợ Cầu Lông')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="bg-gray-100">
    <header class="bg-gray-800 py-2 shadow-md sticky top-0 shadow z-40">
        <div class="mx-auto flex flex-wrap items-center justify-between px-4">
            <!-- Logo + Danh mục -->
            <div class="flex items-center space-x-4 w-full md:w-auto mb-2 md:mb-0">
                <a href="{{ route('exchange.home') }}" class="text-2xl font-bold text-white">Badminton Exchange</a>

                <!-- Danh mục (Dropdown) -->
                <div class="relative">
                    <button id="category-btn"
                        class="px-4 py-2 bg-yellow-500 text-white font-bold rounded flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                        {{ 'CATEGORY' }}
                    </button>
                    <div id="category-menu" class="absolute left-0 mt-2 w-64 bg-white shadow-lg rounded-lg z-50 hidden">
                        @foreach ($categories as $category)
                            <a href="{{ route('exchange.categoryDetail', $category['slug']) }}"
                                class="block px-4 py-3 text-gray-700 hover:bg-gray-300 flex items-center">
                                <img src="{{ asset($category->image) }}" id="mainImage"
                                    class="w-10 h-10 rounded-lg shadow-md mr-4">
                                <span>
                                    {{ $category->name }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Thanh tìm kiếm + Lọc theo khu vực -->
            <form action="{{ route('exchange.home') }}" method="GET"
                class="w-full md:w-auto max-w-lg flex flex-grow items-center mb-2 md:mb-0">
                <div class="flex border border-gray-300 rounded-lg overflow-hidden w-full">
                    <input type="text" name="q" placeholder="{{ 'Search product...' }}"
                        class="w-full px-4 py-2 outline-none">
                    <button class="bg-gray-500 px-4 py-2 text-white font-bold">
                        <i class="fas fa-search text-white-500 mr-2"></i>
                    </button>
                </div>
            </form>

            <!-- Tiện ích & User -->
            <div class="flex items-center space-x-4 text-white w-full md:w-auto justify-end">
                <button id="notification-btn" class="relative bg-gray-200 p-2 rounded-full">
                    🔔
                    <span id="notification-count"
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-2 rounded-full hidden">
                        0
                    </span>
                </button>

                <a href="{{ route('exchange.managerNews') }}">
                    <button>📋 {{ 'Manager news' }}</button>
                </a>

                @auth
                    <div class="relative inline-block text-left" id="user-dropdown-wrapper">
                        <button id="user-toggle-btn" class="flex items-center space-x-2 hover:text-yellow-500">
                            <img src="{{ asset(Auth::user()->profile_photo_path ?? '/images/no-image.png') }}"
                                class="w-8 h-8 rounded-full border" alt="User Avatar">
                            <span class="font-semibold text-white-800">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="user-dropdown"
                            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl z-50 p-4 space-y-4">
                            <div class="flex items-center space-x-3 border-b pb-3">
                                <img src="{{ asset(Auth::user()->profile_photo_path ?? '/images/no-image.png') }}"
                                    class="w-12 h-12 rounded-full border">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">⭐ 0.0 | 0 người theo dõi</p>
                                    <p class="text-xs text-gray-400">TK: C0882xxxxx</p>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-600 mb-1">Quản lý đơn hàng</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="#"
                                        class="hover:bg-gray-100 px-2 py-1 text-black rounded block text-base">🛒 Đơn
                                        mua</a>
                                    <a href="#"
                                        class="hover:bg-gray-100 px-2 py-1 text-black rounded block text-base">📦 Đơn
                                        bán</a>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-600 mb-1">Dịch vụ khác</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('exchange.profile') }}"
                                        class="hover:bg-gray-100 px-2 py-1 text-black rounded block text-base">🢁‍ Thông tin
                                        cá nhân</a>
                                    <a href="#"
                                        class="hover:bg-gray-100 px-2 py-1 text-black rounded block text-base">❤ Trợ
                                        giúp</a>
                                </div>
                            </div>
                            <div class="text-center pt-2 border-t">
                                <a href="{{ route('logout') }}">
                                    <button class="text-red-500 hover:underline text-base">Đăng xuất</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('exchange.productSale') }}"
                        class="bg-orange-600 text-white px-4 py-2 rounded-lg font-bold">
                        + {{ 'POST NEW' }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="button white">{{ __('Log In') }}</a>
                    <a href="{{ route('register_user') }}" class="button btn-register">{{ __('Register') }}</a>
                    @endif
                </div>
            </div>
        </header>
        @if (session('success'))
            <div id="alert-success"
                class="fixed top-5 right-5 bg-green-500 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-md transition-opacity duration-300">
                {{ session('success') }}
            </div>
        @endif
        <div class="">
            @yield('content')
        </div>

    </body>
    <!-- footer -->
    <div class="container mx-auto py-8 px-4 gap-6">
        <div class="wrapper-copyright">
            <div class="container">
                <div class="w-full md:w-10/12 mx-auto text-center">
                    <img src="{{ asset('/images/MOBOBOM.png') }}" alt="Modobom Logo" class="mx-auto mb-4"
                        style="max-width: 150px;">
                    <p class="text-base md:text-lg text-gray-700">
                        <strong>badminton exchange</strong> is a brand in the key project
                        <a href="https://vnisocial.com/" class="text-red-600 underline hover:text-red-800 transition">
                            VNISOCIAL ECOSYSTEM
                        </a>
                        of international company
                        <a href="https://modobom.com/" class="text-red-600 underline hover:text-red-800 transition">
                            Modobom
                        </a>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white mt-10">

        <div class="container mx-auto py-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Cột 1: Logo & Giới thiệu -->
            <div>
                <h2 class="text-2xl font-bold text-white-400">{{ 'Badminton Market' }}</h2>
                <p class="mt-2 text-gray-400">
                    {{ 'Badminton.io, the largest badminton trading and buying platform in Vietnam. A place to connect badminton enthusiasts.' }}
                </p>
            </div>

            <!-- Cột 2: Liên kết -->
            <div>
                <h3 class="text-lg font-semibold text-white-400">{{ 'Link' }}</h3>
                <ul class="mt-2 space-y-2 text-gray-400">
                    <li><a href="{{ route('exchange.home') }}" class="hover:text-yellow-300">{{ 'Homepage' }}</a>
                    </li>
                    <li><a href="{{ route('exchange.home') }}" class="hover:text-yellow-300">{{ 'Product' }}</a>
                    </li>
                    <li><a href="#" class="hover:text-yellow-300">{{ 'News' }}</a></li>
                </ul>
            </div>

            <!-- Cột 3: Liên hệ -->
            <div>
                <h3 class="text-lg font-semibold text-white-400">{{ 'Contact' }}</h3>
                <p class="mt-2 text-gray-400">Email: support@chocaulong.vn</p>
                <p class="text-gray-400">Hotline: 0123 456 789</p>
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-yellow-300"><i
                            class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="text-gray-400 hover:text-yellow-300"><i
                            class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="text-gray-400 hover:text-yellow-300"><i
                            class="fab fa-youtube fa-2x"></i></a>
                </div>
            </div>
        </div>

        <!-- Bản quyền -->
        <div class="border-t border-gray-700 text-center py-4 text-gray-500">
            {{ '&copy; 2025 Badminton Market. All rights reserved.' }}
        </div>
    </footer>

    </html>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const categoryBtn = document.getElementById("category-btn");
            const categoryMenu = document.getElementById("category-menu");

            // Toggle menu khi click vào nút danh mục
            categoryBtn.addEventListener("click", function(event) {
                event.stopPropagation(); // Ngăn không cho sự kiện lan ra ngoài
                categoryMenu.classList.toggle("hidden");
            });

            // Ẩn menu khi click ra ngoài
            document.addEventListener("click", function(event) {
                if (!categoryBtn.contains(event.target) && !categoryMenu.contains(event.target)) {
                    categoryMenu.classList.add("hidden");
                }
            });
        });
    </script>

    <script>
        setTimeout(() => {
            let alertBox = document.getElementById('alert-success');
            if (alertBox) {
                alertBox.classList.add('opacity-0');
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 3000);
    </script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Kết nối Pusher
            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                encrypted: true
            });

            var notificationCount = 0;
            var notificationBtn = document.getElementById("notification-btn");
            var notificationCountEl = document.getElementById("notification-count");
            var notificationList = document.getElementById("notification-list");

            // Lắng nghe sự kiện 'product.accepted'
            var channel = pusher.subscribe('product-accepted');
            channel.bind('product.accepted', function(data) {
                notificationCount++;
                notificationCountEl.textContent = notificationCount;
                notificationCountEl.classList.remove("hidden");

                // Thêm thông báo vào danh sách
                var notificationItem = document.createElement("div");
                notificationItem.className = "p-2 border-b text-sm";
                notificationItem.innerHTML = `
                <p class="font-bold">${data.product.name}</p>
                <p class="text-gray-500 text-xs">đã được duyệt</p>
            `;
                notificationList.prepend(notificationItem);
            });

            // Xử lý khi click vào nút 🔔
            notificationBtn.addEventListener("click", function() {
                notificationList.classList.toggle("hidden");
                notificationCount = 0;
                notificationCountEl.classList.add("hidden");
            });
        });
    </script>

    <script>
        const toggleBtn = document.getElementById('user-toggle-btn');
        const dropdown = document.getElementById('user-dropdown');

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('user-dropdown-wrapper');
            if (!wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
