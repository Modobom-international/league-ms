<html lang="en"
    class="js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage no-websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers no-applicationcache svg inlinesvg smil svgclippaths">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">

    <meta name="msapplication-TileColor" content="#E45357">
    <meta name="msapplication-TileImage" content="{{ asset('/assets/images/favicons/favicon-144.png') }}">
    <meta name="application-name" content="{{ env('APP_NAME', 'Badminton.io') }}">
    <meta name="msapplication-tooltip" content="{{ env('APP_NAME', 'Badminton.io') }}">
    <meta name="description"
        content="{{ __('Run your badminton league for free, badminton scheduling and online results and statistics displayed on your free website.') }}">
    <meta name="keywords"
        content="{{ __('badminton scheduling,badminton scheduler,badminton league,badminton league website,manage badminton league online,run badminton league online') }}">
    <meta name="robots" content="Index, Follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cambria:wght@300;400;500&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="canonical" href="https://badminton.io">
    <link rel="alternate" hreflang="en-US" href="https://badminton.io">
    <link rel="alternate" hreflang="af" href="https://badminton.io">
    <link rel="alternate" hreflang="x-default" href="https://badminton.io">
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/logo-no-background.png') }}">

    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/page/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content/league.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/page/homepage.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/4.1.5/css/flag-icons.min.css">
    @yield('css')
</head>

<style>
    .navbar-toggler {
        background: white !important;
    }

    .navbar-nav .nav-item a:hover {
        color: lightgrey;
    }

    .navbar-nav .nav-item a {
        color: white;
        font-size: 17px;
    }

    .dropdown-menu .dropdown-item {
        color: black !important;
        clear: both;
        display: block;
        font-weight: 400;
        line-height: 1.428571429;
        padding: 10px;
        white-space: nowrap;
        font-size: 16px !important;

    }

    .dropdown-menu .dropdown-item:hover {
        background: #e3e3e3 !important;
    }

    .btn-register {
        margin-left: 5px !important;
    }

    .navbar-nav .nav-link.show {
        color: white !important;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        padding: 10px;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }

    .dropdown-content a {
        color: black !important;
        padding: 10px;
        text-decoration: none;
        display: block;
        clear: both;
        font-weight: 400 !important;
        line-height: 1.428571429;
        white-space: nowrap;
        font-size: 16px !important;
    }

    .dropdown-content a:hover {
        background-color: #ddd;
    }

    .nav-item:hover .dropdown-content {
        display: block;
    }

    /* Mobile menu */
    @media (max-width: 768px) {
        .nav-bar {
            flex-direction: column;
            align-items: center;
        }

        .nav-item {
            width: 100%;
        }

        .dropdown-content {
            position: static;
            width: 100%;
        }
    }

    .navbar-nav .btn {
        font-weight: 600;
    }

    /* Badge notification nhỏ lại cho mobile */
    @media (max-width: 576px) {
        .nav-link .badge {
            font-size: 0.6rem;
            padding: 0.2em 0.4em;
        }

        .reg {
            margin-bottom: 10px;
        }
    }

    /* Highlight thông báo chưa đọc */
    .noti-unread > a {
        font-weight: 700;
        background-color: #f8f9fa;
    }

    /* Avatar bo tròn */
    .avatar-user, .rounded-circle {
        border-radius: 50%;
    }
</style>

<body>
    <header style="background-color: #222">

        <nav class="navbar navbar-expand-lg sticky-top navbar-light m-0 shadow-sm">
            <div class="container">
                <a href="{{ route('home') }}">
                    <img class="logo-image" src="{{ asset('/images/logo-no-background.png') }}" alt="{{ env('APP_NAME', 'Badminton.io') }}" width="100" height="100">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav ms-auto align-items-lg-center">

                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle" href="#" id="leagueDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('LEAGUE') }}▼
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="leagueDropdown">
                                <li><a class="dropdown-item" href="{{ route('list.league') }}">{{ __('List League') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('league.createTour') }}">{{ __('Create League') }}</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle" href="#" id="groupDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('GROUP') }}▼
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="groupDropdown">
                                <li><a class="dropdown-item" href="{{ route('list.group') }}">{{ __('List Group') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('group.createGroup') }}">{{ __('Create Group') }}</a></li>
                            </ul>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link text-uppercase" href="{{ route('news') }}">{{ __('NEWS') }}</a>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link text-uppercase" href="{{ route('match') }}">{{ __('MATCH CENTER') }}</a>
                        </li>

                        <li class="nav-item mx-2">
                            <a class="nav-link text-uppercase" target="_blank" href="{{ route('exchange.home') }}">{{ __('exchange') }}</a>
                        </li>

                        @if (Auth::check() && Auth::user()->role == 'admin')
                            <li class="nav-item mx-2" style="background: #312f2f;">
                                <a class="nav-link text-uppercase" href="{{ route('dashboard') }}">{{ __('DASHBOARD') }}</a>
                            </li>
                        @endif

                        @if (Auth::check())
                            <li class="nav-item dropdown mx-2">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ Auth::user()->profile_photo_path ? asset(Auth::user()->profile_photo_path) : asset('/images/no-image.png') }}"
                                         alt="Avatar" width="25" height="25" class="rounded-circle me-2" />
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('My profile') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('my.league') }}">{{ __('My league') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('my.group') }}">{{ __('My group') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}">{{ __('Logout') }}</a></li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item mx-2">
                                <a href="{{ route('login') }}" class="btn btn-outline px-3 ">{{ __('Log In') }}</a>
                            </li>
                            <li class="nav-item mx-2 reg">
                                <a href="{{ route('register_user') }}" class="btn btn-danger px-3">{{ __('Register') }}</a>
                            </li>
                        @endif

                        @if (Auth::check())
                            @php
                                $count = 0;
                                $listNotification = Cache::get('notification_next_match_' . Auth::user()->id);
                                if (isset($listNotification)) {
                                  foreach ($listNotification as $notification) {
                                    if ($notification->status == 0) {
                                      $count++;
                                    }
                                  }
                                }
                            @endphp
                            <li class="nav-item dropdown mx-2">
                                <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    @if($count > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  {{ $count }}
                  <span class="visually-hidden">unread notifications</span>
                </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 300px;">
                                    @if (count($dataUser->league ?? []) && count($listNotification) > 0)
                                        @foreach ($listNotification as $notification)
                                            <li @class(['noti-unread' => $notification->status == 0])><a class="dropdown-item" href="#">{{ $notification->content }}</a></li>
                                        @endforeach
                                    @else
                                        <li><a class="dropdown-item" href="#">{{ __('Empty Notification') }}</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @php
                            $languages = [
                              'en' => ['label' => 'English', 'emoji' => '🇺🇸'],
                              'vi' => ['label' => 'Tiếng Việt', 'emoji' => '🇻🇳'],
                            ];
                            $current = Session::get('locale', 'en');
                        @endphp

                        <li class="nav-item dropdown mx-2">
                            <button class="btn btn-light dropdown-toggle" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                                {{ $languages[$current]['emoji'] }} {{ $languages[$current]['label'] }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                                @foreach ($languages as $code => $lang)
                                    <li>
                                        <a class="dropdown-item {{ $current == $code ? 'active' : '' }}" href="{{ route('app.setLocale', ['locale' => $code]) }}">
                                            {{ $lang['emoji'] }} {{ $lang['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

    </header>
    @yield('content')

    <!-- footer -->
    <div data-wpr-lazyrender="1" class="wrapper-footer bg-light py-4">
        <div class="wrapper-copyright">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-12 col-md-10">
                        <img
                            src="{{ asset('/images/MOBOBOM.png') }}"
                            alt="Modobom Logo"
                            class="img-fluid mb-3"
                            style="max-width: 150px;"
                        >
                        <p class="" style="font-size: 18px !important;">
                            <strong>badminton.com</strong> is a brand in the key project
                            <a style="color: red !important;" href="https://vnisocial.com/" class="text-decoration-underline">VNISOCIAL ECOSYSTEM</a>
                            of international company
                            <a style="color: red !important;" href="https://modobom.com/" class="text-decoration-underline">Modobom</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <!-- footer -->
    <div style="background: #222; text-transform: uppercase; margin-top: 10px;">
        <footer class="container py-5 text-white">
            <div class="row">
                <!-- Column 1 -->
                <div class="col-12 col-md-3 mb-4 text-center text-md-start">
                    <h4>{{ __('Criteria') }}</h4>
                    <p class="small text-white-50">
                        {{ __('Efficiency and ease-of-use are our mission, simplifying the process of running a sports league.') }}
                    </p>
                    <p class="small text-white-50">
                        {{ __('Badminton.io is available to all at no cost. Additionally, we offer premium plans that include additional functionality.') }}
                    </p>
                </div>

                <!-- Column 2 -->
                <div class="col-12 col-md-3 mb-4 text-center text-md-start">
                    <h4>{{ __('About us') }}</h4>
                    <ul class="list-unstyled small">
                        <li>
                            <a href="{{ route('about') }}" class="text-white-50 text-decoration-none">{{ __('About us') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3 -->
                <div class="col-12 col-md-3 mb-4 text-center text-md-start">
                    <h4>{{ __('Features') }}</h4>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('list.league') }}" class="text-white-50 text-decoration-none">{{ __('League') }}</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">{{ __('Shop') }}</a></li>
                        <li><a href="{{ route('list.group') }}" class="text-white-50 text-decoration-none">{{ __('Group') }}</a></li>
                        <li><a href="{{ route('news') }}" class="text-white-50 text-decoration-none">{{ __('News') }}</a></li>
                    </ul>
                </div>

                <!-- Column 4 -->
                <div class="col-12 col-md-3 mb-4 text-center text-md-start">
                    <h4 class="h5">{{ __('Badminton.io') }}</h4>
                    <p class="small text-white-50">
                        <a href="{{ route('term.and.conditions') }}" class="text-white-50 text-decoration-none">{{ __('Terms & Conditions') }}</a><br>
                        <a href="{{ route('privacy') }}" class="text-white-50 text-decoration-none">{{ __('Privacy') }}</a><br>
                        <a href="{{ route('home') }}" class="text-white-50 text-decoration-none">{{ __('Badminton.io') }}</a>
                    </p>
                    <ul class="list-unstyled d-flex justify-content-center justify-content-md-start mt-3">
                        <li class="me-3">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('/images/logo-no-background.png') }}" width="70" height="70" alt="logo">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center border-top pt-4 mt-4 text-white-50 small text-center text-md-start">
                <p class="mb-2 mb-md-0 px-2">
                    © 2023 Badminton.io. {{ __('All rights reserved. The content of this website is the property of Badminton.io or used under licence. No part may be copied, republished or transmitted without written permission.') }}
                </p>
                <ul class="list-unstyled d-flex justify-content-center justify-content-md-end mb-0 px-2">
                    <li class="ms-3"><a class="text-white-50" href="#"><i class="bi bi-twitter"></i></a></li>
                    <li class="ms-3"><a class="text-white-50" href="#"><i class="bi bi-instagram"></i></a></li>
                    <li class="ms-3"><a class="text-white-50" href="#"><i class="bi bi-facebook"></i></a></li>
                </ul>
            </div>
        </footer>
    </div>

    <!-- jQuery -->
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="{{ asset('/js/page/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('/js/page/common.min.js') }}"></script>
<script>
    $('.open-btn').click(function() {
        $('nav.container').toggleClass('active');
    })

    const toggleMenuClicked = () => {
        const body = document.body;
        const openIcon = document.getElementById("open-icon");
        const closeIcon = document.getElementById("close-icon");

        body.classList.toggle("open");

        if (body.classList.contains("open")) {
            openIcon.style.display = "none";
            closeIcon.style.display = "flex";
        } else {
            openIcon.style.display = "flex";
            closeIcon.style.display = "none";
        }
    };
</script>
<script>
    $(document).ready(() => {
        let isMenuAlreadyOpen = false;
        $('#open-icon').on('click', () => {
            $('body').css("overflow", isMenuAlreadyOpen ? "auto" : "hidden")
            isMenuAlreadyOpen = !isMenuAlreadyOpen
        })
    })
</script>
<script>
    $(document).ready(function() {
        @if (session('success'))
        toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
        toastr.error("{{ session('error') }}");
        @endif

        @if (session('warning'))
        toastr.warning("{{ session('warning') }}");
        @endif

        @if (session('info'))
        toastr.info("{{ session('info') }}");
        @endif
    });
</script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right", // Vị trí hiển thị
        "timeOut": "5000"
    };
</script>


<script>
    document.querySelectorAll('.open-new-tab').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            window.open(this.href, '_blank');
        });
    });
</script>
@yield('js')
</html>
