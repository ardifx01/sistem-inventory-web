@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('items.index') }}" class="flex flex-wrap items-center gap-2 mb-4">
        {{-- Search Bar --}}
        <input type="text"
               name="search"
               id="search"
               value="{{ request('search') }}"
               placeholder="Cari barang..."
               class="flex-1 px-4 py-2 border rounded-md dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        {{-- Dropdown Kategori --}}
        <select name="category_id"
                onchange="this.form.submit()"
                class="px-3 py-2 border rounded-md dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Semua Kategori --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        {{-- Tombol Search --}}
        <button type="submit" id="searchBtn"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
            🔍
        </button>

        {{-- Tombol Barcode Scan --}}
        <button type="button" id="openScannerBtn"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 shadow">
            📷
        </button>
    </form>

    {{-- Tombol Tambah Barang --}}
    @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
    <div class="flex gap-2">
        <a href="{{ route('items.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
            + Tambah Barang
        </a>

        {{-- Tombol Hapus Massal --}}
        <form id="bulkDeleteForm" action="{{ route('items.bulkDelete') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <input type="hidden" name="ids" id="bulkDeleteIds">
        </form>
        <button type="button" id="bulkDeleteBtn"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 shadow hidden">
            🗑 Hapus Terpilih
        </button>
    </div>
    @endif

    {{-- Tabel Daftar Barang --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Barang</h2>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-sm min-w-[800px]">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                            <th class="border px-4 py-2 text-center">
                                <input type="checkbox" id="selectAll">
                            </th>
                        @endif
                        <th class="border px-4 py-2">Nama Barang</th>
                        <th class="border px-4 py-2">Kode Item</th>
                        <th class="border px-4 py-2">Barcode</th>
                        <th class="border px-4 py-2">Kategori</th>
                        <th class="border px-4 py-2">Lokasi Rak</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                                <td class="border px-4 py-2 text-center">
                                    <input type="checkbox" class="itemCheckbox" value="{{ $item->id }}">
                                </td>
                            @endif
                            <td class="border px-4 py-2">{{ $item->name }}</td>
                            <td class="border px-4 py-2 text-center">{{ $item->item_code }}</td>
                            <td class="border px-4 py-2 text-center">{{ $item->barcode ?? '-' }}</td>
                            <td class="border px-4 py-2 text-center">{{ $item->category->name ?? '-' }}</td>
                            <td class="border px-4 py-2 text-center">{{ $item->rack_location ?? 'ZIP' }}</td>
                            <td class="border px-4 py-2 text-center space-x-2">
                                @if(in_array(Auth::user()->role, ['admin', 'superadmin']))
                                    <a href="{{ route('items.edit', $item->id) }}"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                        Edit
                                    </a>
                                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                                onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array(Auth::user()->role, ['admin', 'superadmin']) ? 7 : 6 }}" class="text-center py-4 text-gray-500">
                                Barang tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Barcode Scanner --}}
<div id="barcodeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-3/4 max-w-2xl relative">
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
    // Barcode Scanner
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
                        document.querySelector('form').submit();
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

    window.addEventListener('click', (e) => {
        if (e.target === barcodeModal) {
            html5QrCode.stop().catch(err => console.error(err));
            barcodeModal.classList.add('hidden');
        }
    });

    // Bulk Delete Logic
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.itemCheckbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkDeleteIds = document.getElementById('bulkDeleteIds');

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleBulkDeleteBtn();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', toggleBulkDeleteBtn);
    });

    function toggleBulkDeleteBtn() {
        const anyChecked = document.querySelectorAll('.itemCheckbox:checked').length > 0;
        bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
    }

    bulkDeleteBtn.addEventListener('click', () => {
        const selectedIds = Array.from(document.querySelectorAll('.itemCheckbox:checked'))
                                .map(cb => cb.value);
        if (selectedIds.length > 0 && confirm('Yakin ingin menghapus barang terpilih?')) {
            bulkDeleteIds.value = selectedIds.join(',');
            bulkDeleteForm.submit();
        }
    });
</script>
@endsection
