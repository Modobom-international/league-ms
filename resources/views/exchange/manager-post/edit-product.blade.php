@extends('layouts.app')
<!-- Summernote CSS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow mt-4">
        <h2 class="text-xl font-bold mb-4">{{ 'New Post Product' }}</h2>

        <form id="" method="POST" action="{{ route('exchange.updatePostProduct', $product['slug']) }}"
            enctype="multipart/form-data">
            @csrf()

            @php
                $images = json_decode($product->images, true) ?? [];
                $coverIndex = $product->cover_index ?? 0;
            @endphp
            {{-- Ảnh Chính --}}
            <div id="image-upload-area" class="border border-dashed border-orange-400 p-4 rounded-md">
                <div class="text-sm text-gray-500 mb-2">
                    <i class="text-blue-500">ⓘ</i> Hình ảnh hợp lệ (đăng từ 01 đến 06 hình)
                </div>

                <div class="flex flex-wrap gap-2" id="image-preview-container">
                    {{-- Hiển thị ảnh cũ --}}
                    @foreach ($images as $index => $img)
                        <div
                            class="relative w-24 h-24 border rounded overflow-hidden cursor-move flex items-center justify-center"
                            data-index="{{ $index }}"
                            style="{{ $index == $coverIndex ? 'border-color: #f97316; border-width: 3px;' : '' }}"
                        >
                            <img src="{{ asset($img) }}" alt="Product Image {{ $index }}" class="object-cover w-full h-full">
                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs delete-image-btn">&times;</button>
                            @if ($index == $coverIndex)
                                <span class="absolute bottom-1 left-1 bg-orange-400 text-white text-xs px-1 rounded"></span>
                            @endif
                        </div>
                    @endforeach

                    {{-- Ảnh mới sẽ thêm vào đây bằng JS --}}
                    <div id="previewContainer" class="flex flex-wrap gap-2"></div>

                    {{-- Nút thêm ảnh --}}
                    <label for="imageInput" class="cursor-pointer w-24 h-24 border-2 border-dashed flex items-center justify-center text-orange-400 font-bold text-xl rounded hover:bg-orange-50">
                        +
                    </label>
                </div>

                <input type="file" id="imageInput" name="images[]" accept="image/*" multiple hidden>
                <input type="hidden" name="cover_index" id="cover-index" value="{{ $coverIndex }}">
                <input type="hidden" name="delete-image-btn" id="delete-image-btn" value="">
                <p class="text-sm text-gray-500 mt-2">Nhấn và giữ để di chuyển hình ảnh</p>
            </div>


            {{-- Chọn danh mục --}}
            <div class="mt-4">
                <label class="block font-semibold">{{ 'Category' }}</label>
                <select name="category" class="w-full border px-3 py-2 rounded">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Thông tin chi tiết --}}
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold">{{ 'Condition' }}</label>
                    <div class="flex gap-4">
                        <label><input type="radio" name="condition" value="new"
                                {{ $product->condition == 'new' ? 'checked' : '' }}> New</label>
                        <label><input type="radio" name="condition" value="used"
                                {{ $product->condition == 'used' ? 'checked' : '' }}> Used</label>
                    </div>
                </div>
            </div>

            {{-- Giá bán --}}
            <div class="mt-4">
                <label class="block font-semibold">{{ 'Price' }}</label>
                <input type="number" name="price" class="w-full border px-3 py-2 rounded"
                    value="{{ old('price', $product->price) }}" required>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tiêu đề & mô tả --}}
            <div class="mt-4">
                <label class="block font-semibold">{{ 'Title' }}</label>
                <input type="text" name="name" class="w-full border px-3 py-2 rounded"
                    value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="description" class="block font-semibold">{{ __('Description') }}</label>
                <textarea id="description" name="description"
                          placeholder="{{ __("- Name product\n- Version, capacity, accessories if any\n- Origin/brand\n- Condition: for example: new, no scratches, 3-month warranty\n- Accept payment/shipping via Cho Tot\n- Warranty, maintenance, return policy for goods/products\n- Delivery address, return of goods/products") }}"
                          class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-orange-500 min-h-[200px]">{{ old('description', trim($product->description)) }}</textarea>

                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Chọn địa chỉ --}}
            <div class="mt-4">
                <label class="block font-semibold">{{ 'Address' }}</label>
                <button type="button" id="open-popup" class="w-full border px-3 py-2 rounded bg-gray-100">
                    {{ 'Select Address' }}
                </button>
                <input type="hidden" name="location" id="location" {{ $product->location }}>
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <div class="text-black-600 mt-2" id="selected-location">{{ $product->location }}</div>
            </div>
            <div id="address-popup" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                    <h3 class="text-lg font-semibold mb-4"> {{ 'Select Address' }}</h3>

                    <label class="block font-semibold">{{ 'Province, city' }}</label>
                    <select id="province" class="w-full border px-3 py-2 rounded mb-2"></select>

                    <label class="block font-semibold">{{ 'District, county, town' }}</label>
                    <select id="district" class="w-full border px-3 py-2 rounded mb-2">
                        <option value="">{{ 'Choose District, county, town' }}</option>
                    </select>

                    <label class="block font-semibold">{{ 'Ward, commune, town' }}</label>
                    <select id="ward" class="w-full border px-3 py-2 rounded mb-2">
                        <option value="">{{ 'Choose Ward, commune, town' }}</option>
                    </select>

                    <label class="block font-semibold">{{ 'Specific address' }}</label>
                    <input type="text" id="street" class="w-full border px-3 py-2 rounded mb-4" placeholder="">

                    <button type="button" id="confirm-address" class="bg-orange-600 text-white px-6 py-2 rounded w-full">
                        {{ 'Address Confirmation' }}
                    </button>
                    <button type="button" id="close-popup"
                        class="mt-2 w-full text-center text-red-600">{{ 'Candle' }}</button>
                </div>
            </div>

            {{-- Nút cập nhật --}}
            <div class="mt-6 text-center">
                <button  type="submit" class=" bg-orange-600 text-white px-6 py-2 rounded">{{ 'Update' }}</button>
            </div>
        </form>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const provinceSelect = document.getElementById("province");
            const districtSelect = document.getElementById("district");
            const wardSelect = document.getElementById("ward");
            const addressPopup = document.getElementById("address-popup");
            const openPopupBtn = document.getElementById("open-popup");
            const closePopupBtn = document.getElementById("close-popup");
            const confirmBtn = document.getElementById("confirm-address");
            const selectedLocation = document.getElementById("selected-location");
            const inputLocation = document.getElementById("location");
            const streetInput = document.getElementById("street");

            // Mở popup
            openPopupBtn.addEventListener("click", function() {
                addressPopup.style.display = "flex";
            });

            // Đóng popup
            closePopupBtn.addEventListener("click", function() {
                addressPopup.style.display = "none";
            });

            // Lấy danh sách tỉnh/thành
            fetch("https://provinces.open-api.vn/api/p/")
                .then(response => response.json())
                .then(data => {
                    data.forEach(province => {
                        let option = new Option(province.name, province.code);
                        provinceSelect.add(option);
                    });
                });

            // Khi chọn tỉnh, load danh sách huyện
            provinceSelect.addEventListener("change", function() {
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                if (!this.value) return;

                fetch(`https://provinces.open-api.vn/api/p/${this.value}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.districts.forEach(district => {
                            let option = new Option(district.name, district.code);
                            districtSelect.add(option);
                        });
                    });
            });

            // Khi chọn huyện, load danh sách xã
            districtSelect.addEventListener("change", function() {
                wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                if (!this.value) return;

                fetch(`https://provinces.open-api.vn/api/d/${this.value}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.wards.forEach(ward => {
                            let option = new Option(ward.name, ward.code);
                            wardSelect.add(option);
                        });
                    });
            });

            // Xác nhận địa chỉ
            confirmBtn.addEventListener("click", function() {
                let province = provinceSelect.selectedOptions[0].text;
                let district = districtSelect.selectedOptions[0].text;
                let ward = wardSelect.selectedOptions[0].text;
                let street = streetInput.value;

                let fullAddress = `${street}, ${ward}, ${district}, ${province}`;
                selectedLocation.textContent = fullAddress;
                inputLocation.value = fullAddress;

                addressPopup.style.display = "none";
            });
        });
    </script>
{{--    <script>--}}
{{--        document.getElementById('mainImageInput').addEventListener('change', function(event) {--}}
{{--            let file = event.target.files[0];--}}
{{--            if (file) {--}}
{{--                let reader = new FileReader();--}}
{{--                reader.onload = function(e) {--}}
{{--                    let img = document.getElementById('mainImagePreview');--}}
{{--                    img.src = e.target.result;--}}
{{--                    img.classList.remove('hidden');--}}
{{--                };--}}
{{--                reader.readAsDataURL(file);--}}
{{--            }--}}
{{--        });--}}

{{--        document.getElementById('subImagesInput').addEventListener('change', function(event) {--}}
{{--            let files = event.target.files;--}}
{{--            let previewContainer = document.getElementById('subImagesPreview');--}}
{{--            previewContainer.innerHTML = ''; // Clear previous previews--}}

{{--            for (let file of files) {--}}
{{--                let reader = new FileReader();--}}
{{--                reader.onload = function(e) {--}}
{{--                    let img = document.createElement('img');--}}
{{--                    img.src = e.target.result;--}}
{{--                    img.classList.add('w-20', 'h-20', 'object-cover', 'rounded', 'border');--}}
{{--                    previewContainer.appendChild(img);--}}
{{--                };--}}
{{--                reader.readAsDataURL(file);--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
    <script>
        let images = [];
        const input = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');

        if (input) {
            input.addEventListener('change', function () {
                [...this.files].forEach(file => {
                    if (images.length >= 6) return;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        images.push({ src: e.target.result, file });
                        renderPreviews();
                    };
                    reader.readAsDataURL(file);
                });
            });
        }

        function renderPreviews() {
            previewContainer.innerHTML = '';
            images.forEach((img, idx) => {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.draggable = true;

                div.innerHTML = `
                <img src="${img.src}" class="w-24 h-24 object-cover rounded border border-gray-300">
                <button onclick="removeImage(${idx})"
                        class="absolute -top-1 -right-1 bg-black text-white rounded-full px-1 text-xs hidden group-hover:block">×</button>
                ${idx === 0 ? `<span class="absolute bottom-0 left-0 bg-black bg-opacity-70 text-white text-xs px-1">Hình bìa</span>` : ''}
            `;

                div.ondragstart = (e) => e.dataTransfer.setData("index", idx);
                div.ondrop = (e) => {
                    e.preventDefault();
                    const fromIndex = e.dataTransfer.getData("index");
                    const toIndex = idx;
                    reorderImages(fromIndex, toIndex);
                };
                div.ondragover = (e) => e.preventDefault();

                previewContainer.appendChild(div);
            });
        }

        function removeImage(index) {
            images.splice(index, 1);
            renderPreviews();
        }

        function reorderImages(from, to) {
            const item = images.splice(from, 1)[0];
            images.splice(to, 0, item);
            renderPreviews();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let deletedOldImages = [];

            let deletedImagesInput = document.getElementById('delete-image-btn');
            if (!deletedImagesInput) {
                deletedImagesInput = document.createElement('input');
                deletedImagesInput.type = 'hidden';
                deletedImagesInput.name = 'delete-image-btn';
                deletedImagesInput.id = 'delete-image-btn';
                document.querySelector('form').appendChild(deletedImagesInput);
            }

            document.querySelectorAll('.delete-image-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const imageDiv = btn.closest('div[data-index]');
                    const index = imageDiv.getAttribute('data-index');
                    if (!deletedOldImages.includes(index)) {
                        deletedOldImages.push(index);
                    }
                    deletedImagesInput.value = deletedOldImages.join(',');
                    imageDiv.remove();
                });
            });
        });
    </script>


@endsection
