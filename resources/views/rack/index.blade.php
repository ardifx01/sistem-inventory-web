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

<div class="py-6" 
     x-data="{
        category: 'pieces',
        selected: null,
    }">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-lg font-semibold">Lokasi Rak</h2>
            <p class="text-xl font-mono text-indigo-600" 
               x-text="selected ? selected : 'Pilih rak terlebih dahulu'"></p>
        </div>

        {{-- Dropdown Jenis Rak --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="rackType" class="font-semibold">Pilih Jenis Rak:</label>
                <select id="rackType" x-model="category"
                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200">
                    <option value="pieces">Pieces (P)</option>
                    <option value="bulky">Bulky (B)</option>
                    <option value="lower">Lower (L)</option>
                </select>
            </div>
        </div>

       {{-- Mockup Gambar Rak --}}
        <div class="flex justify-center">
            <template x-if="category === 'pieces'">
                <img src="/images/tatanan-rack.png" class="w-full max-w-md h-auto" alt="Rack Pieces">
            </template>
            <template x-if="category === 'bulky'">
                <img src="/images/tatanan-rack.png" class="w-full max-w-md h-auto" alt="Rack Bulky">
            </template>
            <template x-if="category === 'lower'">
                <img src="/images/tatanan-rack.png" class="w-full max-w-md h-auto" alt="Rack Lower">
            </template>
        </div>

        {{-- Grid Rak --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @for($col=1; $col <= $columns; $col++)
                @php $colCode = $pad2($col); @endphp
                <div class="rounded-xl border border-gray-300 dark:border-gray-600 shadow-lg bg-white dark:bg-gray-800 overflow-hidden">
                    <div class="px-3 py-2 bg-gray-100 dark:bg-gray-700 flex justify-between text-xs text-gray-600 dark:text-gray-300 font-semibold">
                        <span>Kolom</span>
                        <span class="font-mono">{{ $selectedArea }}-{{ $colCode }}</span>
                    </div>

                    <div class="p-3 flex flex-col-reverse gap-2">
                        @for($lvl=1; $lvl <= $levels; $lvl++)
                            @php $lvlCode = $pad2($lvl); @endphp
                            <div>
                                <div class="grid grid-cols-{{ $positions }} gap-1">
                                    @for($pos=1; $pos <= $positions; $pos++)
                                        @php
                                            $posCode = $pad2($pos);
                                            $code = "{$selectedArea}-{$colCode}-{$lvlCode}-{$posCode}";
                                            $slot = $slots[$code] ?? null;
                                            $role = $slot['role'] ?? null;
                                            $bgColor = match($role) {
                                                'pieces' => 'bg-blue-500 text-white hover:bg-blue-600',
                                                'bulky'  => 'bg-orange-500 text-black hover:bg-orange-600',
                                                'lower'  => 'bg-green-500 text-black hover:bg-green-600',
                                                default  => 'bg-gray-200 dark:bg-gray-700 text-gray-500'
                                            };
                                        @endphp
                                        <div 
                                            x-show="category === '{{ $role ?? 'none' }}'"
                                            class="relative group rounded-md text-[10px] flex items-center justify-center h-8 border border-gray-300 dark:border-gray-600 transition {{ $bgColor }}">
                                            <span class="absolute top-0 left-1 font-mono">{{ $posCode }}</span>
                                            
                                            @if($slot)
                                                {{ strtoupper($role[0]) }}
                                                {{-- Tooltip --}}
                                                <div class="absolute z-10 hidden group-hover:flex flex-col text-xs bg-black text-white rounded p-2 -top-12 left-1/2 -translate-x-1/2">
                                                    <span><strong>SKU:</strong> {{ $slot['sku'] }}</span>
                                                    <span><strong>Qty:</strong> {{ $slot['qty'] }}</span>
                                                </div>
                                            @else
                                                &ndash;
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                                <div class="h-1 bg-gray-400 dark:bg-gray-600 mt-1 rounded"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection


@push('styles')
<link rel="stylesheet" href="{{ asset('css/rack.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/rack.js') }}"></script>
@endpush
