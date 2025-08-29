@extends('layouts.app')

@section('content')
@php
    $columns      = $columns ?? 2;   // jumlah rak
    $positions    = $positions ?? 6; // slot per level
@endphp

<div class="container mx-auto p-6 relative"
     x-data="{
        category: 'pieces',
        selectedRack: null,
        selectedLevel: null,
        selectedSlot: null,
        popupX: 0,
        popupY: 0,
        show: false,
        timeout: null,
<<<<<<< HEAD
        category: 'pieces',
        racks: @js(array_map(fn($i) => $selectedArea . '-' . $pad2($i), range(1, $columns))),
=======
        columns: {{ $columns }},
>>>>>>> f3abefb12cb5318269788d23d63f5f81d6ece192
        slots: {{ $positions }},

        get levels() {
            if (this.category === 'lower') return 1;
            if (this.category === 'bulky') return 2;
            return 5; // default pieces
        },
<<<<<<< HEAD
        showPopup(rack, level, slot, el) {
            clearTimeout(this.timeout);
            this.selectedRack = rack;
            this.selectedLevel = level;
            this.selectedSlot = rack + '-L' + level + '-S' + slot;
=======
        get categoryCode() {
            if (this.category === 'pieces') return 'P';
            if (this.category === 'bulky') return 'B';
            if (this.category === 'lower') return 'L';
            return 'P';
        },
        get racks() {
            let arr = [];
            for (let i = 1; i <= this.columns; i++) {
                arr.push(this.categoryCode + '01-' + String(i).padStart(2, '0'));
            }
            return arr;
        },

        showPopup(rack, level, slot, el) {
            clearTimeout(this.timeout);
            this.selectedRack  = rack;
            this.selectedLevel = level;
            this.selectedSlot  = rack + '-' + String(level).padStart(2, '0') + '-' + String(slot).padStart(2, '0');
>>>>>>> f3abefb12cb5318269788d23d63f5f81d6ece192

            let rect = el.getBoundingClientRect();
            this.popupX = rect.left + window.scrollX + rect.width / 2;
            this.popupY = rect.top + window.scrollY - 8;

            this.show = true;
        },
        hidePopup() {
            clearTimeout(this.timeout);
<<<<<<< HEAD
            this.timeout = setTimeout(() => this.show = false, 150); // delay 150ms biar smooth
=======
            this.timeout = setTimeout(() => this.show = false, 150);
>>>>>>> f3abefb12cb5318269788d23d63f5f81d6ece192
        }
     }">

    <!-- Header -->
    <div class="text-center mb-6">
<<<<<<< HEAD
        <h2 class="text-2xl font-bold">Contoh Rak Gudang</h2>
        <p >Arahkan kursor ke slot untuk melihat detail</p>
=======
        <h2 class="text-2xl font-bold">Daftar Rak Gudang</h2>
        <p class="text-gray-500">Arahkan kursor ke slot untuk melihat detail</p>
>>>>>>> f3abefb12cb5318269788d23d63f5f81d6ece192
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
            <div class="flex flex-col items-center" @mouseleave="hidePopup()">
                <!-- Label Rak -->
                <div class="mb-2 font-bold text-lg dark:text-gray-200 text-gray-700" x-text="rack"></div>

                <!-- Rak dengan tiang -->
                <div class="flex">
                    <div class="w-4 dark:bg-gray-200 bg-gray-700"></div> <!-- Tiang kiri -->

                    <div class="flex flex-col-reverse">
                        <template x-for="level in levels" :key="level">
                            <div class="flex flex-col">
                                <!-- Slot barang -->
                                <div class="flex gap-1 p-1">
                                    <template x-for="slot in slots" :key="slot">
                                        <div class="w-12 h-20 cursor-crosshair transition transform hover:-translate-y-1
                                            flex items-center justify-center text-xs font-semibold rounded-sm border border-gray-500 relative"
                                            :class="{
                                                'bg-yellow-400 text-black shadow-lg': selectedSlot === (rack + '-' + String(level).padStart(2,'0') + '-' + String(slot).padStart(2,'0')),
                                                'bg-gradient-to-b from-gray-200 to-gray-400 shadow-md hover:shadow-lg 
                                                 dark:from-gray-600 dark:to-gray-800 text-gray-800 dark:text-gray-200': 
                                                 selectedSlot !== (rack + '-' + String(level).padStart(2,'0') + '-' + String(slot).padStart(2,'0'))
                                            }"
                                            @mouseenter="showPopup(rack, level, slot, $event.target)"
                                            x-text="slot">
                                        </div>
                                    </template>
                                </div>
                                <div class="h-2 dark:bg-gray-200 bg-gray-700"></div> <!-- Balok alas level -->
                            </div>
                        </template>
                    </div>

                    <div class="w-4 dark:bg-gray-200 bg-gray-700"></div> <!-- Tiang kanan -->
                </div>
            </div>
        </template>
    </div>

    <!-- Popup Detail -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 w-56 text-sm pointer-events-none"
         :style="`top:${popupY}px; left:${popupX}px; transform: translate(-50%, -100%);`">
        <div class="relative p-3 border rounded-lg bg-white dark:bg-gray-800 shadow-lg">
            <h3 class="font-semibold mb-2">Detail Kolom</h3>
            <p><strong>Rak:</strong> <span x-text="selectedRack"></span></p>
            <p><strong>Level:</strong> <span x-text="selectedLevel"></span></p>
            <p><strong>Kode Barang:</strong> <span x-text="selectedSlot"></span></p>

            <!-- Arrow -->
            <div class="absolute left-1/2 bottom-[-6px] -translate-x-1/2 
                        w-0 h-0 border-l-6 border-r-6 border-t-6 border-l-transparent border-r-transparent border-t-white
                        dark:border-t-gray-800"></div>
        </div>
    </div>
</div>
@endsection
