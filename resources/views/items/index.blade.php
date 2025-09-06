@extends('layouts.app')

@section('content')
@php
    $selectedCategoryIds = collect(request()->input('categories', []))
        ->filter(fn($v) => trim($v) !== '')
        ->map(fn($v) => (int)$v)
        ->values()
        ->all();
@endphp

{{-- Container untuk Notifikasi Dinamis --}}
<div id="notification-container" class="fixed top-5 right-5 z-[70] flex flex-col gap-2"></div>

<div class="p-4 sm:p-6 space-y-6">

    {{-- Search & Filter --}}
    @include('items.search', ['categories' => $categories, 'selectedCategoryIds' => $selectedCategoryIds])
    
    {{-- Hidden input untuk filter kategori yang dipilih oleh chips. Ini digunakan oleh JavaScript untuk menyimpan state filter. --}}
    <div id="categoriesHiddenInputs" style="display: none;">
        @foreach($selectedCategoryIds as $catId)
            <input type="hidden" name="categories[]" value="{{ $catId }}">
        @endforeach
    </div>

    {{-- Toolbar atas tabel --}}
    @if(in_array(Auth::user()->role, ['admin','superadmin']))
    <div class="flex items-center w-full mb-3">
        <a href="{{ route('items.create') }}" id="tambahBarangBtn"
           class="flex-none px-4 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 text-sm font-medium">
            + Tambah Barang
        </a>

        <form action="{{ route('items.import') }}" method="POST" enctype="multipart/form-data" class="ml-2">
            @csrf
            <input type="file" name="files[]" accept=".xlsx,.xls,.csv" id="importFileInput" multiple class="hidden">
            <button type="button" id="importBtn"
                    class="px-4 py-2.5 rounded-lg bg-green-600 text-white hover:bg-green-700 text-sm font-medium">
                Import
            </button>
            <button type="submit" id="submitImportBtn" class="hidden"></button>
        </form>

        <a href="{{ route('items.downloadTemplate') }}" 
           class="ml-2 px-4 py-2.5 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-sm font-medium inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Template
        </a>

        <script>
            document.getElementById('importBtn').addEventListener('click', function(){
                document.getElementById('importFileInput').click();
            });

            document.getElementById('importFileInput').addEventListener('change', function(){
                document.getElementById('submitImportBtn').click();
            });
        </script>

        <div class="ml-auto flex items-center">
            {{-- Tombol 'Hapus Terpilih' akan muncul/sembunyi via JS --}}
            <button id="deleteSelectedBtn" type="button"
                    class="hidden flex-none px-4 py-2.5 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">
                Hapus Terpilih
            </button>

            {{-- form bulk delete (hidden) --}}
            <form id="bulk-delete-form" action="{{ route('items.bulkDelete') }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
                <div id="bulk-delete-inputs"></div>
            </form>
        </div>
    </div>
    @endif

    
    
    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200/20 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Daftar Barang</h2>
            
            {{-- Dropdown Items per Page --}}
            <div class="flex items-center gap-2">
                <label for="perPageSelect" class="text-sm text-gray-600 dark:text-gray-400">
                    Tampilkan:
                </label>
                <div class="relative">
                    <select id="perPageSelect" 
                            class="appearance-none px-3 py-1.5 pr-8 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200 cursor-pointer"
                            style="-webkit-appearance: none; -moz-appearance: none; background-image: none;">
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    {{-- Custom dropdown arrow --}}
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400">item per halaman</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]" id="itemsTable">
                <thead class="bg-gray-100 dark:bg-gray-700/60 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Barang</th>
                        <th class="px-4 py-3 text-center">Kode Item</th>
                        <th class="px-4 py-3 text-center">Barcode</th>
                        <th class="px-4 py-3 text-center">Kategori</th>
                        <th class="px-4 py-3 text-center">Lokasi Rak</th>
                        @if(in_array(Auth::user()->role, ['admin','superadmin']))
                        <th class="px-4 py-3 text-center">Aksi</th>
                        <th class="px-4 py-3 text-center">
                            {{-- Checkbox ini diatur oleh JS untuk memilih semua item --}}
                            <input type="checkbox" id="selectAll" class="w-4 h-4">
                        </th>
                        @endif
                    </tr>
                </thead>
                <tbody id="itemsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @if($items->isEmpty())
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Barang tidak ditemukan.
                            </td>
                        </tr>
                    @else
                        @foreach($items as $item)
                        <tr>
                            {{-- Name diganti jadi dscription --}}
                            <td class="px-4 py-3">{{ $item->dscription }}</td>

                            {{-- item_code diganti jadi itemCode --}}
                            <td class="px-4 py-3 text-center">{{ $item->itemCode }}</td>

                            {{-- barcode diganti jadi codeBars --}}
                            <td class="px-4 py-3 text-center">{{ $item->codeBars ?? '-' }}</td>

                            <td class="px-4 py-3 text-center">{{ $item->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->rack_location }}</td>
                            @if(in_array(Auth::user()->role, ['admin','superadmin']))
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-x-2">
                                    <a href="{{ route('items.edit', $item->id) }}"
                                       class="px-3 py-0.5 rounded bg-yellow-500 text-white hover:bg-yellow-600 text-sm">
                                        Edit
                                    </a>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="px-3 py-0.5 rounded bg-red-600 text-white hover:bg-red-700 text-sm delete-item-btn"
                                                data-item-id="{{ $item->id }}"
                                                data-item-name="{{ $item->dscription }}">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" class="itemCheckbox w-4 h-4" value="{{ $item->id }}">
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
@if(session('import_success') !== null)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successCount = {{ session('import_success', 0) }};
    const failedCount = {{ session('import_failed', 0) }};
    const failedItems = @json(session('failed_items', []));
    const errorDetails = @json(session('error_details', []));
    
    let title, icon, html;
    
    if (failedCount === 0) {
        // Perfect Import
        title = '✅ Import Selesai';
        icon = 'success';
        html = `
            <div class="import-result">
                <div class="success-badge">
                    <div class="big-number">${successCount}</div>
                    <div class="label">Item Berhasil</div>
                </div>
                <div class="success-message">
                    Semua data berhasil diimport ke sistem
                </div>
            </div>
            
            <style>
                .import-result { text-align: center; }
                .success-badge { background: #F0FDF4; padding: 20px; border-radius: 12px; border: 2px solid #10B981; margin: 15px 0; }
                .big-number { font-size: 28px; font-weight: bold; color: #10B981; }
                .label { font-size: 14px; color: #065F46; margin-top: 5px; }
                .success-message { color: #065F46; margin-top: 15px; font-weight: 500; }
            </style>
        `;
        
        Swal.fire({
            title: title,
            html: html,
            icon: icon,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#10B981',
            allowOutsideClick: false
        });
        
    } else {
        // Partial or Full Failure
        title = '✅ Import Selesai';
        icon = failedCount === (successCount + failedCount) ? 'warning' : 'warning';
        
        html = `
            <div class="import-result">
                <div class="stats-grid">
                    <div class="stat-card success">
                        <div class="big-number">${successCount}</div>
                        <div class="label">Item Berhasil</div>
                    </div>
                    <div class="stat-card failed">
                        <div class="big-number">${failedCount}</div>
                        <div class="label">Item Gagal</div>
                    </div>
                </div>
                
                ${failedCount > 0 ? `
                <div class="error-info">
                    <p style="color: #374151; font-weight: 600;">💡 <strong>Download error list untuk detail</strong></p>
                </div>
                ` : ''}
            </div>
            
            <style>
                .import-result { text-align: center; }
                .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
                .stat-card { padding: 20px; border-radius: 10px; }
                .stat-card.success { background: #F0FDF4; border: 2px solid #10B981; }
                .stat-card.failed { background: #FEF2F2; border: 2px solid #EF4444; }
                .big-number { font-size: 28px; font-weight: bold; }
                .success .big-number { color: #10B981; }
                .failed .big-number { color: #EF4444; }
                .label { font-size: 14px; color: #6B7280; margin-top: 8px; font-weight: 500; }
                .error-info { 
                    background: #F3F4F6; 
                    padding: 15px; 
                    border-radius: 8px; 
                    margin-top: 20px; 
                    text-align: left;
                    border: 1px solid #D1D5DB;
                }
                .error-info p {
                    color: #374151;
                    margin: 0;
                    font-weight: 600;
                }
            </style>
        `;
        
        Swal.fire({
            title: title,
            html: html,
            icon: icon,
            showConfirmButton: true,
            showDenyButton: failedCount > 0,
            confirmButtonText: 'Tutup',
            denyButtonText: '📥 Download Error List',
            confirmButtonColor: '#6B7280',
            denyButtonColor: '#EF4444',
            width: '480px',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isDenied) {
                downloadErrorReport(errorDetails);
            }
        });
    }
});

function downloadErrorReport(errorDetails) {
    // Show loading
    Swal.fire({
        title: 'Menyiapkan Error Report...',
        text: 'Sedang membuat file dengan detail error',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("items.import.error-report") }}';
    form.style.display = 'none';
    
    // CSRF Token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Error details data
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'error_details';
    dataInput.value = JSON.stringify(errorDetails);
    form.appendChild(dataInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Close loading and show success message briefly
    setTimeout(() => {
        Swal.fire({
            title: '✅ Download Dimulai',
            text: 'File error report akan segera terdownload',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1000);
}
</script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            title: 'Gagal',
            text: "{{ session('error') }}",
            icon: 'error',
        });
    </script>
@endif

        </div>

        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-3">
            {{-- Informasi Data --}}
            <div id="paginationInfo" class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} 
                dari {{ $items->total() }} total barang
                @if(request('per_page') && request('per_page') != 25)
                    ({{ request('per_page') }} item per halaman)
                @endif
            </div>
            
            {{-- Pagination Links --}}
            <div class="flex-shrink-0" id="paginationLinks">
                {{ $items->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- ========== MODAL KONFIRMASI HAPUS (SATU ATAU BULK) ========== --}}
    @if(in_array(Auth::user()->role, ['admin','superadmin']))
    <div id="confirmDeleteModal"
         class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl">
            <div class="p-5">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Hapus item?
                </h3>
                <p id="modalBody" class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak bisa dibatalkan.
                </p>
                {{-- Aksi modal diinject oleh JS --}}
                <div id="modalActions" class="mt-5 flex justify-end gap-2">
                    {{-- Tombol akan di-inject oleh JavaScript --}}
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- ========== /MODAL KONFIRMASI ========== --}}

    {{-- MODAL CRUD KATEGORI --}}
    @if(in_array(Auth::user()->role, ['admin','superadmin']))
    <div id="categoryCrudModal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6">
            <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Kelola Kategori</h3>
            {{-- Tombol Tutup --}}
            <button type="button" id="closeCategoryCrudModalBtn" aria-label="close"
                class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white text-gray-700 shadow flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 10.586l4.95-4.95a1.5 1.5 0 012.121 2.121L14.121 12l4.95 4.95a1.5 1.5 0 01-2.121 2.121L12 14.121l-4.95 4.95a1.5 1.5 0 01-2.121-2.121L9.879 12l-4.95-4.95a1.5 1.5 0 012.121-2.121L12 10.586z"/>
                </svg>
            </button>

            <div class="mb-4 border border-gray-300 dark:border-gray-700 rounded-lg p-3 space-y-3">
                <div class="flex items-center gap-2">
                    <input type="text" id="categorySearch" placeholder="Cari kategori..."
                           class="flex-1 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                </div>

                <form id="addCategoryForm" action="{{ route('categories.store') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nama kategori baru"
                           class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:bg-gray-800 dark:text-gray-100" required>
                    <button type="submit"
                            class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Tambah</button>
                </form>
            </div>
            
            {{-- #1: Lokasi Notifikasi di dalam modal --}}
            <div id="modalNotificationContainer" class="mt-4"></div>

            <div class="max-h-60 overflow-y-auto">
                <ul class="space-y-2" id="categoryList">
                    {{-- Kategori ditampilkan berdasarkan urutan dari controller --}}
                    @foreach($categories as $cat)
                        <li class="category-item flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-2" data-name="{{ $cat->name }}">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $cat->name }}</span>
                            <div class="flex items-center gap-2">
                                {{-- #3: Menggunakan properti is_default untuk validasi --}}
                                @if(!$cat->is_default)
                                    <button type="button" class="p-1 rounded-full text-yellow-500 hover:text-yellow-600 edit-category-btn"
                                            data-id="{{ $cat->id }}" data-name="{{ $cat->name }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-400 hover:text-yellow-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                    <form id="delete-category-form-{{ $cat->id }}" action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" class="p-1 rounded-full text-red-500 hover:text-red-600 delete-category-btn"
                                                data-id="{{ $cat->id }}" data-name="{{ $cat->name }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-400 hover:text-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    {{-- #3: Menonaktifkan tombol untuk kategori default --}}
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300">Default</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                    <li id="categoryNotFoundMsg" class="hidden text-center text-gray-500 py-4">Kategori tidak ditemukan.</li>
                </ul>
            </div>

            {{-- MODAL EDIT KATEGORI --}}
            <div id="editCategoryModal"
                 class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6">
                    <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Edit Kategori</h3>
                    <form id="editCategoryForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="editCategoryName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kategori</label>
                            <input type="text" id="editCategoryName" name="name" required
                                   class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" id="cancelEditBtn" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- MODAL KONFIRMASI HAPUS KATEGORI DENGAN PILIHAN --}}
            <div id="confirmDeleteCategoryModal"
                 class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4">
                <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 text-center">
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">Hapus Kategori?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Kategori ini memiliki <span id="itemCountSpan" class="font-semibold text-indigo-500">0</span> barang.
                        Pilih opsi di bawah ini:
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
                        <button type="button" id="deleteCategoryMoveBtn" class="flex-1 px-4 py-2 rounded-lg border border-indigo-600 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900 transition-colors">
                            Pindahkan ke "Belum Dikategorikan"
                        </button>
                        <button type="button" id="deleteCategoryDeleteBtn" class="flex-1 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                            Hapus Semua Barang & Kategori
                        </button>
                    </div>
                    <button type="button" id="cancelCategoryDeleteBtn" class="mt-4 text-gray-600 dark:text-gray-400 hover:underline">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
        </div>
    </div>
</div>
@endsection