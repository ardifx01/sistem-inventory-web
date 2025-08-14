@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    {{-- Kotak Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Barang</h2>
            <p class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalBarang }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Kategori</h2>
            <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalKategori }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Rak</h2>
            <p class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalRak }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Belum Masuk Rak (ZIP)</h2>
            <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $belumMasukRak }}</p>
        </div>
    </div>

    {{-- Barang Baru Ditambahkan --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 w-full">
        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
            Barang Baru Ditambahkan
        </h3>

        <div id="barang-baru-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($barangBaru as $barang)
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-6 shadow text-center min-h-[140px] flex flex-col justify-center">
                    <h4 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-1 break-words">{{ $barang->name }}</h4>
                    <p class="text-base text-gray-600 dark:text-gray-300">Kode: {{ $barang->item_code }}</p>
                    <p class="text-base text-gray-600 dark:text-gray-300">Rak: {{ $barang->rack_location ?? '-' }}</p>
                </div>
            @endforeach
        </div>

    </div>
</div>

{{-- JavaScript Auto Refresh --}}
<script>
    function loadBarangBaru() {
        fetch('/barang-baru')
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach(item => {
                    html += `
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 shadow text-center">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 truncate">${item.name}</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kode: ${item.item_code}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Rak: ${item.rack_location ?? '-'}</p>
                        </div>
                    `;
                });
                document.getElementById('barang-baru-list').innerHTML = html;
            });
    }

    loadBarangBaru();
    setInterval(loadBarangBaru, 30000); // update setiap 30 detik
</script>
@endsection
