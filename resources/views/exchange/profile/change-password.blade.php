@extends('layouts.app')
@section('content')

    <div class="flex gap-6 p-6 bg-gray-100">
        <!-- Sidebar -->
        <div class="container mx-auto mx-auto  " style="max-width: 1250px !important; ">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">{{__('Change Password')}}</h2>

            <form action="{{ route('update-password') }}" method="POST" class="md:col-span-3 bg-white rounded shadow p-6 space-y-6" >
                @csrf
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
