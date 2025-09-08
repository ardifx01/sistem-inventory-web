@extends('layouts.app')

@section('content')
@php
    $columns   = $columns ?? 8;   // jumlah blok rak (P01, P02, dst)
    $positions = $positions ?? 6; // jumlah slot per level
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
        columns: {{ $columns }},
        slots: {{ $positions }},
        currentSlide: 0,

        get levels() {
            if (this.category === 'lower') return 2;
            if (this.category === 'bulky') return 3;
            return 5; // default pieces
        },
        get categoryCode() {
            if (this.category === 'pieces') return 'P';
            if (this.category === 'bulky') return 'B';
            if (this.category === 'lower') return 'L';
            return 'P';
        },
        get racks() {
            let arr = [];
            for (let i = 1; i <= this.columns; i++) {
                let blok = this.categoryCode + String(i).padStart(2, '0'); // P01, P02, dst
                let rakBlok = [];
                for (let j = 1; j <= 6; j++) { // 6 rak per blok
                    rakBlok.push(blok + '-' + String(j).padStart(2, '0'));
                }
                arr.push(rakBlok);
            }
            return arr;
        },
        get totalSlides() {
            return this.racks.length; // tiap blok jadi 1 slide
        },
        get chunks() {
            return this.racks; // langsung array of array
        },

        showPopup(rack, level, slot, el) {
            clearTimeout(this.timeout);
            this.selectedRack  = rack;
            this.selectedLevel = level;
            this.selectedSlot  = rack + '-' + String(level).padStart(2, '0') + '-' + String(slot).padStart(2, '0');

            let rect = el.getBoundingClientRect();
            this.popupX = rect.left + window.scrollX + rect.width / 2;
            this.popupY = rect.top + window.scrollY - 8;
            this.show = true;
        },
        hidePopup() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.show = false, 150);
        }
     }">

    <!-- Header -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold">Daftar Rak Gudang</h2>
        <p class="text-gray-500">Arahkan kursor ke kolom untuk melihat detail</p>
    </div>

    <!-- Dropdown Jenis Rak -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex items-center gap-2">
            <label for="rackType" class="font-semibold">Pilih Kategori Rak</label>
            <select id="rackType" x-model="category"
                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                       dark:bg-gray-700 dark:text-gray-200">
                <option value="pieces">Pieces (P)</option>
                <option value="bulky">Bulky (B)</option>
                <option value="lower">Lower (L)</option>
            </select>
        </div>
    </div>

    <!-- Slider Rak Gudang -->
    <div class="relative">
        <div class="overflow-hidden w-full">
            <div class="flex transition-transform duration-500"
                 :style="`transform: translateX(-${currentSlide * (100 / totalSlides)}%); width: ${totalSlides * 100}%;`">

                <!-- Loop tiap slide -->
                <template x-for="(chunk, slideIndex) in chunks" :key="slideIndex">
                    <div class="flex gap-10 justify-center flex-wrap shrink-0"
                         :style="`width: ${100 / totalSlides}%;`">

                        <template x-for="rack in chunk" :key="rack">
                            <div class="flex flex-col items-center" @mouseleave="hidePopup()">
                                <!-- Label Rak -->
                                <div class="mb-2 font-bold text-lg dark:text-gray-200 text-gray-700" x-text="rack"></div>

                                <!-- Rak dengan slot -->
                                <div class="flex">
                                    <div class="w-4 dark:bg-gray-200 bg-gray-700"></div>
                                    <div class="flex flex-col-reverse">
                                        <template x-for="level in levels" :key="level">
                                            <div class="flex flex-col">
                                                <div class="flex gap-1 p-1">
                                                    <template x-for="slot in slots" :key="slot">
                                                        <div class="w-7 h-16 cursor-pointer transition transform hover:-translate-y-1
                                                                    flex items-center justify-center text-xs font-semibold rounded-sm
                                                                    border border-gray-500"
                                                             :class="{
                                                                'bg-yellow-400 text-black shadow-lg':
                                                                    selectedSlot === (rack + '-' + String(level).padStart(2,'0') + '-' + String(slot).padStart(2,'0')),
                                                                'bg-gradient-to-b from-gray-200 to-gray-400 shadow-md hover:shadow-lg 
                                                                 dark:from-gray-600 dark:to-gray-800 text-gray-800 dark:text-gray-200':
                                                                    selectedSlot !== (rack + '-' + String(level).padStart(2,'0') + '-' + String(slot).padStart(2,'0'))
                                                             }"
                                                             @mouseenter="showPopup(rack, level, slot, $event.target)"
                                                             x-text="slot">
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="h-2 dark:bg-gray-200 bg-gray-700"></div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="w-4 dark:bg-gray-200 bg-gray-700"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Tombol Navigasi -->
        <div class="flex justify-center mt-4 gap-8">
            <button @click="if(currentSlide > 0) currentSlide--"
                class="p-2 transition-colors duration-200"
                :class="currentSlide === 0 
                        ? 'text-gray-300 cursor-not-allowed' 
                        : 'text-gray-400 hover:text-gray-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button @click="if(currentSlide < totalSlides-1) currentSlide++"
                class="p-2 transition-colors duration-200"
                :class="currentSlide === totalSlides-1 
                        ? 'text-gray-300 cursor-not-allowed' 
                        : 'text-gray-400 hover:text-gray-600'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
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
            <h3 class="font-semibold mb-2">Detail Barang</h3>
            <p><strong>Rak:</strong> <span x-text="selectedRack"></span></p>
            <p><strong>Level:</strong> <span x-text="selectedLevel"></span></p>
            <p><strong>Kolom:</strong> <span x-text="selectedSlot"></span></p>

            <!-- Arrow -->
            <div class="absolute left-1/2 bottom-[-6px] -translate-x-1/2 
                        w-0 h-0 border-l-6 border-r-6 border-t-6
                        border-l-transparent border-r-transparent
                        border-t-white dark:border-t-gray-800"></div>
        </div>
    </div>
</div>
@endsection
