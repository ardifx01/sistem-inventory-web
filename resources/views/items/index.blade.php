@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl sm:text-2xl font-semibold">Daftar Barang</h2>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('items.index') }}" id="searchForm"
          class="bg-white/5 backdrop-blur rounded-xl border border-white/10 p-4 sm:p-5">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 items-center">

            {{-- Search input --}}
            <div class="md:col-span-6 lg:col-span-7 order-1">
                <label for="search" class="sr-only">Pencarian</label>
                <div class="flex">
                    <input type="text" name="search" id="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama, kode item, atau barcode…"
                           class="w-full px-4 py-2.5 rounded-l-lg border border-r-0 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-200" />

                    {{-- tombol cari --}}
                    <button type="submit" id="searchBtn"
                            class="px-3 md:px-4 border border-gray-300 border-l-0 border-r-0 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                        </svg>
                    </button>

                    {{-- tombol scan --}}
                    <button type="button" id="openScannerBtn"
                        class="px-3 md:px-4 rounded-r-lg border border-l-0 border-gray-300 flex items-center justify-center">
                        <!-- Heroicons Camera -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                    </button>

                </div>
                <p id="scanHint" class="mt-2 text-xs text-gray-400 hidden">
                    Hasil scan: <span class="font-semibold" id="scanResult"></span>
                </p>
            </div>

            {{-- Kategori + ZIP + Reset --}}
            <div class="md:col-span-6 lg:col-span-5 order-2 flex flex-wrap md:flex-nowrap items-center gap-3">
                <div class="flex-1">
                    <label for="category_id" class="sr-only">Kategori</label>
                    <select name="category_id" id="category_id"
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-200"
                            onchange="document.getElementById('searchForm').submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="zip_only" value="1"
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                           {{ request()->boolean('zip_only') ? 'checked' : '' }}
                           onchange="document.getElementById('searchForm').submit()">
                    Barang zip
                </label>

                <a href="{{ route('items.index') }}"
                   class="px-3 sm:px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700 shadow">
                   Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Tombol Tambah Barang --}}
    @if(in_array(Auth::user()->role, ['admin','superadmin']))
    <div class="flex">
        <a href="{{ route('items.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">
            <span class="hidden sm:inline">+ Tambah Barang</span>
            <span class="sm:hidden">+ Tambah</span>
        </a>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-100 dark:bg-gray-700/60 text-gray-700 dark:text-gray-200">
                    <tr>
                        @if(in_array(Auth::user()->role, ['admin','superadmin']))
                        <th class="px-4 py-3 text-center">
                            <input type="checkbox" id="selectAll" class="w-4 h-4">
                        </th>
                        @endif
                        <th class="px-4 py-3 text-left">Nama Barang</th>
                        <th class="px-4 py-3 text-center">Kode Item</th>
                        <th class="px-4 py-3 text-center">Barcode</th>
                        <th class="px-4 py-3 text-center">Kategori</th>
                        <th class="px-4 py-3 text-center">Lokasi Rak</th>
                        @if(in_array(Auth::user()->role, ['admin','superadmin']))
                        <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        @if(in_array(Auth::user()->role, ['admin','superadmin']))
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="itemCheckbox w-4 h-4" value="{{ $item->id }}">
                        </td>
                        @endif
                        <td class="px-4 py-3">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->item_code }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->barcode ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            {{ $item->rack_location && $item->rack_location !== 'ZIP' ? $item->rack_location : 'ZIP' }}
                        </td>
                        @if(in_array(Auth::user()->role, ['admin','superadmin']))
                        <td class="px-4 py-3 text-center space-x-2">
                            <a href="{{ route('items.edit', $item->id) }}"
                            class="inline-block px-3 py-1 my-2 rounded bg-yellow-500 text-white hover:bg-yellow-600">
                                Edit
                            </a>
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700"
                                        onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ in_array(Auth::user()->role, ['admin','superadmin']) ? 7 : 6 }}"
                            class="px-4 py-6 text-center text-gray-500">
                            Barang tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $items->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Modal Barcode Scanner --}}
<div id="barcodeModal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
    <div class="relative w-full max-w-3xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-5">
        <h3 class="text-lg font-semibold mb-1 text-gray-900 dark:text-gray-100">Scan Barcode</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Arahkan kamera ke barcode barang</p>
        <div id="reader" class="w-full"></div>

        <div class="mt-5 flex justify-end gap-2">
            <button id="closeScannerBtn"
                    class="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">
                Tutup
            </button>
        </div>

        <button type="button" aria-label="close"
                class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white text-gray-700 shadow"
                onclick="safeStopScanner(true)">✕</button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    const barcodeModal   = document.getElementById('barcodeModal');
    const openScannerBtn = document.getElementById('openScannerBtn');
    const closeScannerBtn= document.getElementById('closeScannerBtn');
    const searchInput    = document.getElementById('search');

    function startScanner() {
        if (html5QrCode) return;
        html5QrCode = new Html5Qrcode("reader");
        Html5Qrcode.getCameras().then(devices => {
            const config = { fps: 10, qrbox: 250 };
            const camera = devices?.length ? { facingMode: "environment" } : { facingMode: "user" };
            html5QrCode.start(camera, config, onScanSuccess, onScanError)
                .catch(err => console.error('QR start failed:', err));
        }).catch(err => console.error('Camera error:', err));
    }

    function onScanSuccess(codeText) {
        searchInput.value = codeText;
        safeStopScanner(true);
        document.getElementById('searchForm').submit();
    }

    function onScanError(_) {}

    function safeStopScanner(closeModal = false) {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
                if (closeModal) barcodeModal.classList.add('hidden');
            }).catch(() => {
                html5QrCode = null;
                if (closeModal) barcodeModal.classList.add('hidden');
            });
        } else if (closeModal) {
            barcodeModal.classList.add('hidden');
        }
    }

    openScannerBtn.addEventListener('click', () => {
        barcodeModal.classList.remove('hidden');
        startScanner();
    });

    closeScannerBtn.addEventListener('click', () => safeStopScanner(true));

    barcodeModal.addEventListener('click', (e) => {
        if (e.target === barcodeModal) safeStopScanner(true);
    });

    const selectAll      = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.itemCheckbox');
    const bulkDeleteBtn  = document.getElementById('bulkDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const bulkDeleteIds  = document.getElementById('bulkDeleteIds');

    function toggleBulk() {
        const anyChecked = document.querySelectorAll('.itemCheckbox:checked').length > 0;
        if (bulkDeleteBtn) bulkDeleteBtn.classList.toggle('hidden', !anyChecked);
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            toggleBulk();
        });
        itemCheckboxes.forEach(cb => cb.addEventListener('change', toggleBulk));
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', () => {
            const ids = Array.from(document.querySelectorAll('.itemCheckbox:checked')).map(cb => cb.value);
            if (!ids.length) return;
            if (confirm('Yakin ingin menghapus barang terpilih?')) {
                bulkDeleteIds.value = ids.join(',');
                bulkDeleteForm.submit();
            }
        });
    }

        {{-- Tombol Tambah Barang + Hapus Terpilih --}}
    @if(in_array(Auth::user()->role, ['admin','superadmin']))
    <div class="flex gap-2">
        <a href="{{ route('items.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow">
            <span class="hidden sm:inline">+ Tambah Barang</span>
            <span class="sm:hidden">+ Tambah</span>
        </a>

        {{-- Tombol hapus terpilih (hidden default) --}}
        <form id="bulkDeleteForm" action="{{ route('items.bulkDelete') }}" method="POST">
            @csrf
            <input type="hidden" name="ids" id="bulkDeleteIds">
            <button type="button" id="bulkDeleteBtn"
                    class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow">
                Hapus Terpilih
            </button>
        </form>
    </div>
    @endif

</script>
@endsection
