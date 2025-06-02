@extends('layouts.app')


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
        <div class="container mx-auto mx-auto  ">

            <form action="{{ route('update-password') }}" method="POST" class="md:col-span-3 bg-white rounded shadow p-6 space-y-6" >
                @csrf
                <h2 class="text-xl font-semibold">{{__('Change Password')}}</h2>
                @if (session('status'))
                    <div class="mb-4 text-green-600 text-base font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-4">
                    <label for="oldPasswordInput" class="block text-black-700 font-semibold mb-1">{{__('Old password')}}</label>
                    <input name="old_password" type="password" id="oldPasswordInput"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('old_password') border-red-500 @enderror">
                    @error('old_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    @if(session('error'))
                        <div class="text-red-500 text-sm mt-2">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="newPasswordInput" class="block text-black-700 font-semibold mb-1">{{__('New Password')}}</label>
                    <input name="new_password" type="password" id="newPasswordInput"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('new_password') border-red-500 @enderror">
                    @error('new_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="confirmNewPasswordInput" class="block text-black-700 font-semibold mb-1">{{__('Confirm new password')}}</label>
                    <input name="new_password_confirmation" type="password" id="confirmNewPasswordInput"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="text-center">
                    <button type="submit"
                            class="bg-orange-400 text-white px-6 py-2 rounded hover:bg-yellow-500 transition">
                        {{__('Save')}}
                    </button>
                </div>
            </form>

        </div>
        <!-- Form chính -->

    </div>
@endsection
