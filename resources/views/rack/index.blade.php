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

<div class="py-6" x-data="{ category: 'pieces' }">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Pilih Jenis Rak --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="rackType" class="font-semibold">Pilih Jenis Rak:</label>
                <select id="rackType" x-model="category"
                    class="border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200">
                    <option value="pieces">Pieces (P)</option>
                    <option value="bulky">Bulky (B)</option>
                    <option value="lower">Lower (L)</option>
                </select>
            </div>

            {{-- Legend --}}
            <div class="flex gap-4 text-sm items-center flex-wrap">
                <span class="flex items-center"><span class="w-3 h-3 bg-blue-500 inline-block mr-1 rounded"></span>Pieces</span>
                <span class="flex items-center"><span class="w-3 h-3 bg-orange-500 inline-block mr-1 rounded"></span>Bulky</span>
                <span class="flex items-center"><span class="w-3 h-3 bg-green-500 inline-block mr-1 rounded"></span>Lower</span>
                <span class="flex items-center"><span class="w-3 h-3 bg-gray-400 inline-block mr-1 rounded"></span>Kosong</span>
            </div>
        </div>

        {{-- Gambar Rak Contoh --}}
        <div class="flex justify-center">
            <template x-if="category === 'pieces'">
                <img src="/images/rack-pieces.png" alt="Rack Pieces" class="max-w-full h-auto">
            </template>
            <template x-if="category === 'bulky'">
                <img src="/images/rack-bulky.png" alt="Rack Bulky" class="max-w-full h-auto">
            </template>
            <template x-if="category === 'lower'">
                <img src="/images/rack-lower.png" alt="Rack Lower" class="max-w-full h-auto">
            </template>
        </div>

        {{-- Grid Rak --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @for($col=1; $col <= $columns; $col++)
                @php $colCode = $pad2($col); @endphp
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-800">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                        <span>Kolom</span>
                        <span class="font-mono">{{ $selectedArea }}-{{ $colCode }}</span>
                    </div>

                    <div class="flex flex-col-reverse gap-2">
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
                                                'pieces' => 'bg-blue-500 text-white',
                                                'bulky'  => 'bg-orange-500 text-black',
                                                'lower'  => 'bg-green-500 text-black',
                                                default  => 'bg-gray-200 dark:bg-gray-700 text-gray-500'
                                            };
                                        @endphp
                                        <div x-show="category === '{{ $role ?? 'none' }}'"
                                             class="relative rounded-md text-[10px] flex items-center justify-center h-8 border border-gray-300 dark:border-gray-600 {{ $bgColor }}">
                                            <span class="absolute top-0 left-1 font-mono">{{ $posCode }}</span>
                                            @if($slot)
                                                {{ strtoupper($role) }}
                                            @else
                                                &ndash;
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                                <div class="h-1 bg-gray-400 dark:bg-gray-600 mt-1"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection