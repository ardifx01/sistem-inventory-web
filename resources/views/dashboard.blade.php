@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 w-full max-w-6xl">
        
        <!-- Total Barang -->
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <h2 class="text-lg font-bold text-gray-700">Total Barang</h2>
            <p class="text-3xl font-extrabold text-blue-600">{{ $totalBarang }}</p>
        </div>

        <!-- Total Kategori -->
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <h2 class="text-lg font-bold text-gray-700">Total Kategori</h2>
            <p class="text-3xl font-extrabold text-green-600">{{ $totalKategori }}</p>
        </div>

        <!-- Total Rak -->
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <h2 class="text-lg font-bold text-gray-700">Total Rak</h2>
            <p class="text-3xl font-extrabold text-yellow-600">{{ $totalRak }}</p>
        </div>

        <!-- Barang Belum Masuk Rak -->
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <h2 class="text-lg font-bold text-gray-700">Belum Masuk Rak</h2>
            <p class="text-3xl font-extrabold text-red-600">{{ $belumMasukRak }}</p>
        </div>

    </div>
</div>
@endsection
