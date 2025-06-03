@extends('layouts.app')

@section('title', __('About Us') . ' - Badminton Exchange')

@section('content')
    <div class="bg-white py-8">
        <div class="container mx-auto px-4 md:px-8">
            {{-- DETAILED INTRO --}}
            <div class="max-w-4xl mx-auto mt-12 space-y-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center">
                    {{ __('About Badminton Exchange') }}
                </h1>

                <p class="text-gray-700 text-lg leading-relaxed">
                    <strong>{{ __('Badminton Exchange') }}</strong>
                    {{ __('is an online platform built for the badminton community in Vietnam.') }}
                    {{ __('We help players connect to') }} <span class="text-blue-600 font-semibold">{{ __('buy, sell, or give away') }}</span>
                    {{ __('badminton gear quickly and conveniently.') }}
                </p>

                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ __('Our Mission') }}</h2>
                        <p class="text-gray-700 leading-relaxed">
                            {{ __('To create a friendly ecosystem where players can access quality gear at affordable prices.') }}
                            {{ __('We also aim to contribute to') }} <span class="font-semibold text-green-600">{{ __('environmental sustainability') }}</span>
                            {{ __('by promoting reuse and exchange of sports products.') }}
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ __('Core Values') }}</h2>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                            <li>{{ __('Community-first mindset') }}</li>
                            <li>{{ __('Encourage healthy and respectful sports culture') }}</li>
                            <li>{{ __('Make it easy for new players to get started') }}</li>
                            <li>{{ __('Integrity – Honesty – Transparency') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-gray-100 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ __('Contact Us') }}</h2>
                    <p class="text-gray-700">
                        {{ __('If you have any questions, feedback, or would like to partner with us, feel free to reach out:') }}
                    </p>
                    <ul class="mt-3 space-y-2 text-gray-700">
                        <li><strong>{{ __('Email') }}:</strong> support@badminton-exchange.com</li>
                        <li><strong>{{ __('Facebook') }}:</strong> <a href="#" class="text-blue-600 hover:underline">Badminton Exchange VN</a></li>
                        <li><strong>{{ __('Hotline') }}:</strong> 0909 123 456</li>
                    </ul>
                </div>

                <div class="text-center text-sm text-gray-500 mt-10">
                    © 2025 {{ __('Badminton Exchange') }}. {{ __('All rights reserved.') }}
                </div>
            </div>
        </div>
    </div>
@endsection
