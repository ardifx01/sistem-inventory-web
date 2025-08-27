@extends('layouts.app')

@section('content')
@php
    $areas        = $areas ?? ['PO1','PO2'];
    $selectedArea = $selectedArea ?? 'PO1';
    $positions    = $positions ?? 6; // slot per level
    $columns      = $columns ?? 2;   // jumlah rak

    $pad2 = fn($n) => sprintf('%02d', $n);
@endphp

<div class="container mx-auto p-6"
     x-data="{
        selectedRack: null,
        selectedLevel: null,
        selectedSlot: null,
        category: 'pieces',
        racks: @js(array_map(fn($i) => $selectedArea . '-' . $pad2($i), range(1, $columns))),
        slots: {{ $positions }},
        get levels() {
            if (this.category === 'lower') return 2;
            if (this.category === 'bulky') return 3;
            return 5; // default pieces
        }
     }">

    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">Daftar Rak Gudang</h2>
        <p class="text-gray-500">Klik slot pada rak untuk melihat detail</p>
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

    <!-- Rak Gudang -->
    <div class="flex gap-10 justify-center flex-wrap">
        <template x-for="rack in racks" :key="rack">
            <div class="flex flex-col items-center">
                <!-- Label Rak -->
                <div class="mb-2 font-bold text-lg text-gray-700" x-text="rack"></div>

                <!-- Rak dengan tiang -->
                <div class="flex">
                    <!-- Tiang kiri -->
                    <div class="w-4 bg-gray-700"></div>

                    <!-- Isi Rak (Levels dengan balok tiap level) -->
                    <div class="flex flex-col-reverse">
                        <template x-for="level in levels" :key="level">
                            <div class="flex flex-col">

                                <!-- Slot barang -->
                                <div class="flex gap-1 p-1">
                                    <template x-for="slot in slots" :key="slot">
                                        <div class="w-12 h-20 cursor-pointer transition transform hover:-translate-y-1
                                            flex items-center justify-center text-xs font-semibold rounded-sm border border-gray-500"
                                            :class="{
                                                'bg-yellow-400 text-black shadow-lg': selectedSlot === (rack + '-L' + level + '-S' + slot),
                                                'bg-gradient-to-b from-gray-200 to-gray-400 shadow-md hover:shadow-lg 
                                            dark:from-gray-600 dark:to-gray-800 text-gray-800 dark:text-gray-200':
                                            selectedSlot !== (rack + '-L' + level + '-S' + slot)
                                            }"
                                            @click.stop="selectedRack = rack; selectedLevel = level; selectedSlot = rack + '-L' + level + '-S' + slot"
                                            x-text="'S' + slot">
                                        </div>
                                    </template>
                                </div>

                                <!-- Balok alas level -->
                                <div class="h-2 bg-gray-700"></div>
                            </div>
                        </template>
                    </div>

                    <!-- Tiang kanan -->
                    <div class="w-4 bg-gray-700"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Detail Slot -->
    <div class="mt-8" x-show="selectedSlot">
        <div class="p-4 border rounded-lg bg-white dark:bg-gray-800 shadow">
            <h3 class="font-semibold mb-2">Detail Slot</h3>
            <p><strong>Rak:</strong> <span x-text="selectedRack"></span></p>
            <p><strong>Level:</strong> <span x-text="selectedLevel"></span></p>
            <p><strong>Slot:</strong> <span x-text="selectedSlot"></span></p>
        </div>
    </div>
</div>
@endsection
