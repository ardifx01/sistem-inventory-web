@extends('layouts.app')

@section('content')
@php
    $areas         = $areas ?? ['PO1','PO2'];
    $selectedArea  = $selectedArea ?? 'PO1';
    $levels        = $levels ?? 5;
    $positions     = $positions ?? 10;
    $columns       = $columns ?? 5;

    $slots = $slots ?? [
        'PO1-03-03-04' => ['role' => 'pieces', 'sku' => 'SKU-001', 'qty' => 12],
        'PO1-01-05-01' => ['role' => 'bulky',  'sku' => 'SKU-XL',  'qty' => 3],
        'PO1-02-01-07' => ['role' => 'lower',  'sku' => 'SKU-LWR', 'qty' => 9],
    ];

    $pad2 = fn($n) => sprintf('%02d', $n);
@endphp

<div class="container mx-auto p-6"
     x-data="{
        category: 'pieces',
        selectedRack: null,
        selectedLevel: null,
        selectedSlot: null,
        racks: @js(array_map(fn($i) => $selectedArea . '-' . $pad2($i), range(1, $columns))),
        levels: {{ $levels }},
        slots: {{ $positions }}
     }">

    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">Daftar Rak</h2>
    </div>

    <!-- Dropdown Jenis Rak -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <label for="rackType" class="font-semibold">Pilih Jenis Rak</label>
            <select id="rackType" x-model="category"
                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200">
                <option value="pieces">Pieces (P)</option>
                <option value="bulky">Bulky (B)</option>
                <option value="lower">Lower (L)</option>
            </select>
        </div>
    </div>

    <!-- Daftar Rak -->
    <div class="grid grid-cols-3 gap-4">
        <template x-for="rack in racks" :key="rack">
            <div 
                class="p-4 border rounded-lg cursor-pointer transition text-center"
                :class="selectedRack === rack ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-100 hover:bg-gray-200'"
                @click="selectedRack = rack; selectedLevel = null; selectedSlot = null">
                <span x-text="rack"></span>
            </div>
        </template>
    </div>

    <!-- Pilih Level -->
    <div class="mt-6" x-show="selectedRack">
        <h2 class="text-xl font-semibold mb-4">Level di <span x-text="selectedRack"></span></h2>
        <div class="flex flex-wrap gap-2">
            <template x-for="level in levels" :key="level">
                <button 
                    class="px-4 py-2 rounded-lg border transition"
                    :class="selectedLevel === level ? 'bg-green-500 text-white' : 'bg-gray-200 hover:bg-gray-300'"
                    @click="selectedLevel = level; selectedSlot = null">
                    Level <span x-text="level"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- Pilih Slot -->
    <div class="mt-6" x-show="selectedLevel">
        <h3 class="text-lg font-semibold mb-3">Slot di Level <span x-text="selectedLevel"></span></h3>
        <div class="grid grid-cols-5 gap-3">
            @for($pos = 1; $pos <= $positions; $pos++)
                @php $posCode = $pad2($pos); @endphp
                <div 
                    class="p-3 border rounded-lg cursor-pointer transition"
                    :class="selectedSlot === '{{ $posCode }}' ? 'bg-yellow-400 text-black' : 'bg-white hover:bg-blue-100'"
                    @click="selectedSlot = '{{ $posCode }}'">
                    Slot {{ $posCode }}
                </div>
            @endfor
        </div>
    </div>

    <!-- Detail Slot -->
    <div class="mt-6" x-show="selectedSlot">
        <div class="p-4 border rounded-lg bg-white shadow">
            <h3 class="font-semibold mb-2">Detail Slot</h3>
            <p><strong>Rak:</strong> <span x-text="selectedRack"></span></p>
            <p><strong>Level:</strong> <span x-text="selectedLevel"></span></p>
            <p><strong>Daftar Barang:</strong> <span x-text="selectedSlot"></span></p>
        </div>
    </div>

</div>
@endsection
