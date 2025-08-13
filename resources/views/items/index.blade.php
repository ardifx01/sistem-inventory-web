@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4 text-white">Daftar Barang</h2>

    {{-- Search & Filter --}}
    {{-- Dibungkus form GET agar terhubung ke ItemController@index --}}
    <form method="GET" action="{{ route('items.index') }}" class="flex items-center gap-2 mb-4">
        {{-- Search Bar --}}
        <input type="text" 
               name="search" {{-- ✅ Diberi name agar controller menerima parameter --}}
               id="search" 
               value="{{ request('search') }}" {{-- ✅ Menjaga nilai search saat reload --}}
               placeholder="Cari barang..."
               class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300">

        {{-- Dropdown Kategori --}}
        <select name="category_id" 
                onchange="this.form.submit()" {{-- ✅ Auto-submit saat pilih kategori --}}
                class="px-2 py-2 border rounded-lg">
            <option value="">-- Semua Kategori --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        {{-- Tombol Search --}}
        <button type="submit" id="searchBtn" class="p-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            🔍
        </button>

        {{-- Tombol Barcode Scan --}}
        <button type="button" id="openScannerBtn" class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
            📷
        </button>
    </form>

    {{-- Tombol Tambah Barang --}}
    @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
    <div class="mb-4">
        <a href="{{ route('items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Tambah Barang
        </a>
    </div>
    @endif

    {{-- Tabel Daftar Barang --}}
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Nama Barang</th>
                    <th class="border p-2">Kode Item</th>
                    <th class="border p-2">Barcode</th>
                    <th class="border p-2">Kategori</th>
                    <th class="border p-2">Lokasi Rak</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="border p-2 text-white">{{ $item->name }}</td>
                        <td class="border p-2 text-white">{{ $item->item_code }}</td>
                        <td class="border p-2 text-white">{{ $item->barcode ?? '-' }}</td>
                        <td class="border p-2 text-white">{{ $item->category->name ?? '-' }}</td>
                        <td class="border p-2 text-white">{{ $item->rack_location ?? 'ZIP' }}</td>
                        <td class="border p-2 text-white">
                            @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                                {{-- Tombol Edit --}}
                                <a href="{{ route('items.edit', $item->id) }}" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                    Edit
                                </a>
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border p-4 text-center text-gray-500">
                            Barang tidak ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Barcode Scanner --}}
<div id="barcodeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-3/4 max-w-2xl relative" id="barcodeModalContent">
        <h3 class="text-lg font-bold mb-2">Scan Barcode</h3>
        <p class="text-gray-600 mb-4">Scan barcode barang di dalam kamera</p>
        <div id="reader" style="width: 100%;"></div>
        <button id="closeScannerBtn" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            Tutup
        </button>
    </div>
</div>

{{-- Script HTML5-Qrcode --}}
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    const openScannerBtn = document.getElementById('openScannerBtn');
    const closeScannerBtn = document.getElementById('closeScannerBtn');
    const barcodeModal = document.getElementById('barcodeModal');
    let html5QrCode;

    openScannerBtn.addEventListener('click', () => {
        barcodeModal.classList.remove('hidden');
        html5QrCode = new Html5Qrcode("reader");
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 250 },
                    qrCodeMessage => {
                        document.getElementById('search').value = qrCodeMessage;
                        document.querySelector('form').submit(); // ✅ Langsung jalankan pencarian
                        html5QrCode.stop();
                        barcodeModal.classList.add('hidden');
                    }
                );
            }
        }).catch(err => console.error(err));
    });

    closeScannerBtn.addEventListener('click', () => {
        html5QrCode.stop().catch(err => console.error(err));
        barcodeModal.classList.add('hidden');
    });

    // Tutup popup jika klik di luar modal
    window.addEventListener('click', (e) => {
        if (e.target === barcodeModal) {
            html5QrCode.stop().catch(err => console.error(err));
            barcodeModal.classList.add('hidden');
        }
    });
</script>
@endsection
