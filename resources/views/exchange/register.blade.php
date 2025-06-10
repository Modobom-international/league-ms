<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đăng ký | Chợ Tốt cầu lông</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-cover bg-center flex flex-col items-center justify-center space-y-6"
      style="background-color: #dadada">

<div class="flex justify-center">
    <img
        src="{{ asset('/images/MOBOBOM.png') }}"
        alt="Modobom Logo"
        class="mb-3"
        style="max-width: 100px;"
    />
</div>

<div class="bg-white bg-opacity-90 rounded-lg shadow-lg max-w-md w-full p-5">

    <h2 class="text-3xl font-semibold text-gray-800 mb-6 text-center">{{ __("Register") }}</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('exchange.Register') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-gray-700 mb-2 font-medium">{{ __("Name") }}</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
            />
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-2 font-medium">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
            />
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 mb-2 font-medium">{{ __('Password') }}</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
            />
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 mb-2 font-medium">{{ __('Confirm Password') }}</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <button type="submit" class="w-full bg-[#1f2937]/60 hover:bg-[#3c4148]/60 text-white font-semibold py-3 rounded-md transition">
            {{ __("Register") }}
        </button>
    </form>

    <p class="mt-6 text-center text-gray-600">
        {{__('Already have an account?')}}
        <a href="{{ route('exchange.LoginForm') }}" class="text-blue-600 hover:underline">{{ __("Login") }}</a>
    </p>
</div>
</body>
</html>
