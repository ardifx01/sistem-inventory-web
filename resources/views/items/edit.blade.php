@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">

    <h2 class="text-2xl font-bold mb-6">Edit Barang</h2>

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

    <form action="{{ route('items.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Barang --}}
        <div class="mb-4">
            <label class="block font-semibold mb-2">Nama Barang</label>
            <input type="text" name="name" value="{{ old('name', $item->name) }}" 
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kode Item --}}
        <div class="mb-4">
            <label class="block font-semibold mb-2">Kode Item</label>
            <input type="text" name="item_code" value="{{ old('item_code', $item->item_code) }}" 
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('item_code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Barcode --}}
        <div class="mb-4">
            <label class="block font-semibold mb-2">Barcode</label>
            <div class="flex gap-2">
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $item->barcode) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="button" onclick="startScanner()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">Scan</button>
            </div>
            @error('barcode')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <video id="scanner" class="mt-3 w-full rounded-lg border hidden"></video>
        </div>

        {{-- Kategori --}}
        <div class="mb-4">
            <label class="block font-semibold mb-2">Kategori</label>
            <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Lokasi Rak --}}
        <div class="mb-4">
            <label class="block font-semibold mb-2">Lokasi Rak</label>
            <input type="text" name="rack_location" value="{{ old('rack_location', $item->rack_location) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            @error('rack_location')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-between">
            <a href="{{ route('items.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Kembali</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">Update Barang</button>
        </div>
    </form>
</div>

{{-- QR Scanner --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function startScanner() {
        const scanner = document.getElementById('scanner');
        scanner.classList.remove('hidden');

        const html5QrCode = new Html5Qrcode("scanner");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            qrCodeMessage => {
                document.getElementById('barcode').value = qrCodeMessage;
                html5QrCode.stop();
                scanner.classList.add('hidden');
            }
        ).catch(err => {
            console.error(`QR Code start failed: ${err}`);
        });
    }
</script>
@endsection
