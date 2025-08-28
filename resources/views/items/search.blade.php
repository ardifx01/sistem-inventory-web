<form method="GET" action="{{ route('items.index') }}" id="searchForm"
      class="bg-white/5 backdrop-blur rounded-xl border border-white/10 p-4 sm:p-5 relative">
    <div class="flex flex-col md:flex-row md:items-stretch md:justify-between gap-4">

        {{-- Search & Scan Input --}}
        <div class="flex flex-1 items-stretch h-full">
            <input type="text" name="search" id="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama, kode item, atau barcode…"
                   class="flex-1 px-4 h-10 rounded-l-lg border border-r-0 border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-200" />
            
            {{-- Tombol Cari --}}
            <button type="submit" id="searchBtn"
                    class="px-3 md:px-4 h-10 border border-gray-300 border-l-0 border-r-0 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                </svg>
            </button>
            
            {{-- Tombol Scan --}}
            <button type="button" id="openScannerBtn"
                class="px-3 md:px-4 h-10 rounded-r-lg border border-l-0 border-gray-300 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                </svg>
            </button>
        </div>

        {{-- Filter Kategori, ZIP, Reset --}}
        <div class="flex flex-wrap md:flex-nowrap items-center md:items-stretch gap-3 md:gap-4 h-full">
            {{-- Filter + Gear (wrapper relative supaya popover nempel) --}}
            <div class="relative flex items-stretch rounded-lg border border-gray-300 dark:bg-gray-900 dark:text-gray-200">
                {{-- Tombol untuk membuka popover/panel kategori --}}
                <button type="button" id="openCategoryFilter"
                        class="px-3 py-2.5 h-10 flex items-center gap-2 rounded-l-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    <span class="hidden sm:inline">Kategori</span>
                </button>

                <span class="w-px bg-gray-300 self-stretch"></span>

                {{-- Tombol Gear (buka modal CRUD) --}}
                @if(in_array(Auth::user()->role, ['admin','superadmin']))
                <button type="button" id="openCrudModalBtn"
                class="flex items-center justify-center p-2 rounded-r-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                </button>
                @endif
                {{-- POPOVER UNTUK MEMILIH CHIPS. Ini diatur oleh JS (toggle hidden class) --}}
                <div id="chipPickerPanel"
                class="hidden absolute top-full left-0 mt-2 md:left-auto md:right-0 w-full sm:w-80 md:w-[28rem] max-w-[calc(100vw-2rem)] max-h-[60vh] overflow-auto z-20">
                    <div class="mt-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 shadow-lg">
                        {{-- Daftar kategori untuk dipilih. Dibuat dinamis oleh PHP. --}}
                        <div class="flex flex-wrap gap-2 mb-3" id="chipPickerList">
                            @foreach($categories as $cat)
                                <button type="button"
                                        class="chipPickerItem px-3 py-1.5 rounded-full text-sm border transition-colors whitespace-nowrap
                                               {{ in_array($cat->id, $selectedCategoryIds) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700' }}"
                                        data-id="{{ $cat->id }}"
                                        data-name="{{ $cat->name }}">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" id="applyCategoryFilter"
                                    class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                                Terapkan
                            </button>
                            <button type="button" id="closeChipPicker"
                                    class="px-3 py-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-sm">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ZIP Checkbox --}}
            <label class="inline-flex items-center gap-2 text-sm select-none h-10">
                <input type="checkbox" name="zip_only" value="1"
                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                       {{ request()->boolean('zip_only') ? 'checked' : '' }}
                       onchange="document.getElementById('searchForm').submit()">
                Barang ZIP
            </label>
            {{-- Reset Button --}}
            <a href="{{ route('items.index') }}"
               class="px-3 sm:px-4 py-2.5 h-10 rounded-lg bg-gray-600 text-white hover:bg-gray-700 shadow flex items-center">
                Reset
            </a>
        </div>
    </div>
    
    {{-- CHIPS TERPILIH --}}
    <div id="selectedChipsContainer" class="col-span-12 mt-4 {{ empty($selectedCategoryIds) ? 'hidden' : '' }}">
        <div id="selectedChips" class="flex flex-wrap gap-2">
            @foreach($categories->whereIn('id', $selectedCategoryIds) as $sel)
                <span class="selectedChip px-3 py-1.5 rounded-full bg-indigo-100 text-indigo-700 text-sm flex items-center gap-2"
                      data-id="{{ $sel->id }}">
                    {{ $sel->name }}
                    <button type="button" class="removeChip -mr-1" aria-label="hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            @endforeach
        </div>
    </div>
    {{-- Hidden inputs untuk kategori terpilih --}}
    <div id="categoriesHiddenInputs">
        @foreach($selectedCategoryIds as $id)
            <input type="hidden" name="categories[]" value="{{ $id }}">
        @endforeach
    </div>
</form>