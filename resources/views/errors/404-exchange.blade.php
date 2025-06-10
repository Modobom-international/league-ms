@extends('layouts.app') {{-- hoặc layout riêng nếu bạn dùng layout khác --}}

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-white text-gray-800">
        <img src="{{ asset('/images/MOBOBOM.png') }}" alt="Logo" class="mb-6" style="max-width: 120px;">
        <h1 class="text-4xl font-bold mb-4">404 - Không tìm thấy trang</h1>
        <p class="text-lg text-gray-600 mb-6">Trang bạn tìm kiếm không tồn tại hoặc bạn chưa đăng nhập.</p>
        <a href="{{ route('exchange.home') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition">
            Quay lại trang chủ
        </a>
    </div>
@endsection
