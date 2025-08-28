@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Tambah Barang') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

            {{-- Notifikasi sukses --}}
            @if(session('success'))
                <div class="bg-green-200 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi error global --}}
            @if ($errors->any())
                <div class="bg-red-200 text-red-800 p-4 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="text-2xl font-bold mb-4 text-center">Tambah Barang</h2>

            <form method="POST" action="{{ route('items.store') }}">
                @csrf

                {{-- Nama Barang --}}
{{-- Nama Barang (dscription) --}}
<div>
    <x-input-label for="dscription" :value="__('Nama')" />
    <x-text-input id="dscription" class="block mt-1 w-full"
                  type="text" name="dscription" value="{{ old('dscription') }}"
                  required autofocus />
    <x-input-error :messages="$errors->get('dscription')" class="mt-2" />
</div>

{{-- Kode Item (itemCode) --}}
<div class="mt-4">
    <x-input-label for="itemCode" :value="__('Kode Item')" />
    <x-text-input id="itemCode" class="block mt-1 w-full"
                  type="text" name="itemCode" value="{{ old('itemCode') }}"
                  required />
    <x-input-error :messages="$errors->get('itemCode')" class="mt-2" />
</div>

{{-- Barcode (codeBars) --}}
<div class="mt-4">
    <x-input-label for="codeBars" :value="__('Barcode (Opsional)')" />
    <div class="flex gap-2">
        <x-text-input id="codeBars" class="block w-full"
                      type="text" name="codeBars" value="{{ old('codeBars') }}" />
        <button type="button" id="start-scan"
            class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md
                   font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
            Scan
        </button>
    </div>
    <div id="scanner-container" class="mt-3 hidden">
        <video id="preview" class="border rounded-lg w-full"></video>
    </div>
    <x-input-error :messages="$errors->get('codeBars')" class="mt-2" />
</div>


                {{-- Kategori --}}
                <div class="mt-4">
                    <x-input-label for="category_id" :value="__('Kategori')" />
                    <div class="flex gap-2">
                        <select id="category_id" name="category_id"
                                class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700
                                       dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500
                                       focus:ring-indigo-500"
                                required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="btn-add-category"
                            class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                            + Kategori
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                {{-- Lokasi Rak --}}
                <div class="mt-4">
                    <x-input-label for="rack_location" :value="__('Lokasi Rak (Opsional)')" />
                    <x-text-input id="rack_location" class="block mt-1 w-full"
                                  type="text" name="rack_location" value="{{ old('rack_location') }}"
                                  placeholder="Contoh: P01-01-02-01" />
                    <p class="text-sm text-gray-500 mt-1">Jika kosong, otomatis akan menjadi <strong>ZIP</strong></p>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end mt-6 space-x-2">
                    <a href="{{ route('items.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md
                              font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600
                              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 mr-3">
                        Kembali
                    </a>
                    <x-primary-button>
                        {{ __('Tambah') }}
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Kategori --}}
<div id="modal-category" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold mb-4">Tambah Kategori</h3>
        <input type="text" id="new-category-name"
               class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 mb-4"
               placeholder="Nama kategori baru">
        <div class="flex justify-end gap-2">
            <button id="cancel-add-category" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Batal</button>
            <button id="save-category" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
    </div>
</div>

{{-- Script Barcode Scanner --}}
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const startScanBtn = document.getElementById('start-scan');
    const previewElem = document.getElementById('preview');
    const scannerContainer = document.getElementById('scanner-container');
    const barcodeInput = document.getElementById('barcode');

    startScanBtn.addEventListener('click', async () => {
        scannerContainer.classList.remove('hidden');
        try {
            const devices = await codeReader.listVideoInputDevices();
            const selectedDeviceId = devices[0].deviceId;
            codeReader.decodeFromVideoDevice(selectedDeviceId, previewElem, (result) => {
                if (result) {
                    barcodeInput.value = result.text;
                    codeReader.reset();
                    scannerContainer.classList.add('hidden');
                }
            });
        } catch (error) {
            console.error(error);
        }
    });

    // Modal Tambah Kategori
    const modalCategory = document.getElementById('modal-category');
    const btnAddCategory = document.getElementById('btn-add-category');
    const cancelAddCategory = document.getElementById('cancel-add-category');
    const saveCategory = document.getElementById('save-category');
    const categorySelect = document.getElementById('category_id');

    btnAddCategory.addEventListener('click', () => {
        modalCategory.classList.remove('hidden');
    });

    cancelAddCategory.addEventListener('click', () => {
        modalCategory.classList.add('hidden');
    });

    saveCategory.addEventListener('click', async () => {
        const name = document.getElementById('new-category-name').value.trim();
        if (!name) return alert('Nama kategori tidak boleh kosong');

        try {
            const response = await fetch("{{ route('categories.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name })
            });
            const data = await response.json();
            if (data.success) {
                // Tambahkan kategori baru ke dropdown
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.selected = true;
                categorySelect.appendChild(option);
                modalCategory.classList.add('hidden');
                document.getElementById('new-category-name').value = '';
            } else {
                alert(data.message || 'Gagal menambah kategori');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan');
        }
    });
</script>
@endsection
