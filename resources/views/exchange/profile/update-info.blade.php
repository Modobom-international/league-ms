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
            <!-- Sidebar -->
            <!-- Form chính -->
            <form class="md:col-span-3 bg-white rounded shadow p-6 space-y-6" method="POST"
                action="{{ route('exchange.update', $dataUser['id']) }}" enctype="multipart/form-data">
                @csrf
                <h2 class="text-xl font-semibold">{{ __('Information') }}</h2>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium mb-1">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ $dataUser->name }}"
                            class="w-full border rounded px-3 py-2" />
                        @if ($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div>
                        <label class="block font-medium mb-1">{{ __('Phone') }} </label>
                        <input type="text" name="phone" value="{{ old('phone', $dataUser->phone) }}"
                            class="w-full border rounded px-3 py-2" />
                        @if ($errors->has('phone'))
                            <span class="text-danger">{{ $errors->first('phone') }}</span>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-medium mb-1">{{ __('Address') }}</label>
                        <input type="text" name="address" value="{{ old('address', $dataUser->address) }}"
                            class="w-full border rounded px-3 py-2" />
                        @if ($errors->has('address'))
                            <span class="text-danger">{{ $errors->first('address') }}</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block font-medium mb-1">Email</label>
                        <input type="email" value="tranthuy240814@gmail.com"
                            class="w-full border rounded px-3 py-2 bg-gray-100" disabled />
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div>
                        <div>
                            <label class="block font-medium mb-1">{{ __('Date of birth') }}</label>
                            <input type="date" name="age" value="{{ old('age', $dataUser->age) }}"
                                class="w-full border rounded px-3 py-2" />
                            @if ($errors->has('age'))
                                <span class="text-danger">{{ $errors->first('age') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <button type="submit"
                        class="bg-orange-400 text-white px-6 py-2 rounded hover:bg-yellow-500 transition">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
