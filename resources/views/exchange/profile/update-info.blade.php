@extends('layouts.app')
@section('content')
    <div class="flex gap-6 p-6 bg-gray-100">
        <!-- Sidebar -->

        <div class="container mx-auto mx-auto  "style="max-width: 1250px !important; " >
            <!-- Sidebar -->
            <!-- Form chính -->
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4">{{__('Profile')}}</h2>

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
