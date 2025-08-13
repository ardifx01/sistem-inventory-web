@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">

    <h2 class="text-2xl font-bold mb-4 text-white">Tambah Barang</h2>

    {{-- Notifikasi pesan sukses --}}
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi error validasi global --}}
    @if ($errors->any())
        <div class="bg-red-200 text-red-800 p-4 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('items.store') }}" class="bg-white p-6 rounded-lg shadow-lg space-y-4">
        @csrf

        {{-- Nama Barang --}}
        <div>
            <label class="block mb-1 font-semibold">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" 
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kode Item --}}
        <div>
            <label class="block mb-1 font-semibold">Kode Item</label>
            <input type="text" name="item_code" value="{{ old('item_code') }}" 
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
            @error('item_code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Barcode --}}
        <div>
            <label class="block mb-1 font-semibold">Barcode</label>
            <div class="flex gap-2">
                <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                    class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                <button type="button" id="start-scan" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Scan</button>
            </div>
            <div id="scanner-container" class="mt-3 hidden">
                <video id="preview" class="border rounded-lg w-full"></video>
            </div>
            @error('barcode')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kategori --}}
        <div>
            <label class="block mb-1 font-semibold">Kategori</label>
            <select name="category_id" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Lokasi Rak --}}
        <div>
            <label class="block mb-1 font-semibold">Lokasi Rak (Opsional)</label>
            <input type="text" name="rack_location" value="{{ old('rack_location') }}" placeholder="Contoh: P01-01-02-01" 
                class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <p class="text-sm text-gray-500 mt-1">Jika kosong, otomatis akan menjadi <strong>ZIP</strong></p>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-between">
            <a href="{{ route('items.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">Kembali</a>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">Simpan</button>
        </div>
    </form>
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
</script>
@endsection
