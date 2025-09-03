@extends('layouts.app')

@section('styles')
<style>
    select#category_id {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 1em;
    }

    /* Styling untuk dropdown popup */
    select#category_id option {
        padding: 8px 12px;
    }

    /* Khusus untuk Chrome/Safari/Modern Browsers */
    select#category_id::-webkit-listbox {
        max-height: 280px !important;
    }

    @media screen and (-webkit-min-device-pixel-ratio: 0) {
        select#category_id {
            overflow: -moz-hidden-unscrollable;
            overflow: hidden;
        }
    }

    /* Untuk Firefox */
    @-moz-document url-prefix() {
        select#category_id {
            overflow: -moz-hidden-unscrollable;
            overflow: hidden;
        }
        
        select#category_id option {
            max-height: 280px;
        }
    }
</style>
@endsection

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Tambah Barang') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <div class="max-w-2xl mx-auto">
                {{-- Notifikasi sukses --}}
                @if(session('success'))
                    <div class="bg-green-200 text-green-800 p-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

            {{-- Notifikasi error global --}}
            @if ($errors->any())
                <div class="bg-red-200 text-red-800 p-4 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="text-2xl font-bold mb-4 text-center">Tambah Barang</h2>

            <form method="POST" action="{{ route('items.store') }}">
                @csrf

                {{-- Nama Barang --}}
{{-- Nama Barang (dscription) --}}
<div>
    <x-input-label for="dscription" :value="__('Nama')" />
    <x-text-input id="dscription" class="block mt-1 w-full"
                  type="text" name="dscription" value="{{ old('dscription') }}"
                  required autofocus />
    <x-input-error :messages="$errors->get('dscription')" class="mt-2" />
</div>

{{-- Kode Item (itemCode) --}}
<div class="mt-4">
    <x-input-label for="itemCode" :value="__('Kode Item')" />
    <x-text-input id="itemCode" class="block mt-1 w-full"
                  type="text" name="itemCode" value="{{ old('itemCode') }}"
                  required />
    <x-input-error :messages="$errors->get('itemCode')" class="mt-2" />
</div>

{{-- Barcode (codeBars) --}}
<div class="mt-4">
    <x-input-label for="codeBars" :value="__('Barcode (Opsional)')" />
    <div class="flex gap-2">
        <x-text-input id="codeBars" class="block w-full"
                      type="text" name="codeBars" value="{{ old('codeBars') }}" 
                      placeholder="Contoh: 1234567890123 (EAN-13)" />
        <button type="button" id="start-scan"
            class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md
                   font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
            Scan
        </button>
    </div>
    <div id="scanner-container" class="mt-3 hidden">
        <video id="preview" class="border rounded-lg w-full"></video>
    </div>
    <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
        Format yang didukung: EAN-13 (13 digit), UPC-A (12 digit), EAN-8 (8 digit), atau Code 128 (alphanumerik)
    </div>
    <x-input-error :messages="$errors->get('codeBars')" class="mt-2" />
</div>


                {{-- Kategori --}}
                <div class="mt-4">
                    <x-input-label for="category_id" :value="__('Kategori')" />
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <select id="category_id" name="category_id"
                                    class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700
                                           dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500
                                           focus:ring-indigo-500"
                                    required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="btn-add-category"
                            class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm whitespace-nowrap mt-1">
                            + Kategori
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                {{-- Lokasi Rak --}}
                <div class="mt-4" x-data="rackLocationInput()">
                    <x-input-label value="Lokasi Rak (Opsional)" />
                    <div class="flex flex-wrap items-center gap-2 mt-1 w-full">
                        <!-- Type -->
                        <select x-model="type" name="rack_type" 
                            class="w-32 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 
                                   focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="P">P (Piece)</option>
                            <option value="B">B (Bulky)</option>
                            <option value="L">L (Lower)</option>
                            <option value="ZIP">ZIP</option>
                        </select>

                        <!-- Section -->
                        <div class="flex items-center flex-1 min-w-[200px]">
                            <input type="text" 
                                id="section" 
                                class="w-20 text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 
                                       dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                                x-model="section"
                                data-model="section"
                                :disabled="type === 'ZIP'"
                                @input="formatInput($event, 'rack')"
                                maxlength="2"
                                placeholder="Section"
                            >

                            <span class="mx-2 text-gray-500 dark:text-gray-400">-</span>

                            <!-- Rack -->
                            <input type="text" 
                                id="rack" 
                                class="w-20 text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 
                                       dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                                x-model="rack"
                                data-model="rack"
                                :disabled="type === 'ZIP'"
                                @input="formatInput($event, 'level')"
                                maxlength="2"
                                placeholder="Rack"
                            >

                            <span class="mx-2 text-gray-500 dark:text-gray-400">-</span>

                            <!-- Level -->
                            <input type="text" 
                                id="level" 
                                class="w-20 text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 
                                       dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                                x-model="level"
                                data-model="level"
                                :disabled="type === 'ZIP'"
                                @input="formatInput($event, 'column')"
                                maxlength="2"
                                placeholder="Level"
                            >

                            <span class="mx-2 text-gray-500 dark:text-gray-400">-</span>

                            <!-- Column -->
                            <input type="text" 
                                id="column" 
                                class="w-20 text-center rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 
                                       dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" 
                                x-model="column"
                                data-model="column"
                                :disabled="type === 'ZIP'"
                                @input="formatInput($event)"
                                maxlength="2"
                                placeholder="Column"
                            >
                        </div>

                        <!-- Preview -->
                        <input type="hidden" name="rack_location" x-model="preview">
                        <div class="w-full mt-2">
                            <span class="text-gray-500 dark:text-gray-400" x-text="preview ? 'Format: ' + preview : 'Preview lokasi rak'"></span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('rack_location')" class="mt-2" />
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end mt-6 space-x-2">
                    <a href="{{ route('items.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md
                              font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600
                              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 mr-3">
                        Kembali
                    </a>
                    <x-primary-button>
                        {{ __('Tambah') }}
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Kategori --}}
<div id="modal-category" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-sm">
        <h3 class="text-lg font-semibold mb-4">Tambah Kategori</h3>
        <input type="text" id="new-category-name"
               class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 mb-4"
               placeholder="Nama kategori baru">
        <div class="flex justify-end gap-2">
            <button id="cancel-add-category" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Batal</button>
            <button id="save-category" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
        </div>
    </div>
</div>

{{-- Script Rack Location --}}
<script>
    function rackLocationInput() {
        return {
            type: 'P',
            section: '',
            rack: '',
            level: '',
            column: '',
            preview: '',
            
            init() {
                this.updatePreview();
                
                // Watch for changes
                this.$watch('type', () => this.updatePreview());
                this.$watch('section', () => this.updatePreview());
                this.$watch('rack', () => this.updatePreview());
                this.$watch('level', () => this.updatePreview());
                this.$watch('column', () => this.updatePreview());
            },

            formatInput(event, nextId) {
                let input = event.target;
                let value = input.value.replace(/\D/g, ''); // Remove non-digits
                
                if (value.length > 2) {
                    value = value.substring(0, 2);
                }
                
                // Update the model
                this[input.dataset.model] = value;
                
                // Move to next input if value is 2 digits
                if (value.length === 2 && nextId) {
                    document.getElementById(nextId).focus();
                }
            },

            updatePreview() {
                if (this.type === 'ZIP') {
                    this.preview = 'ZIP';
                    this.section = '';
                    this.rack = '';
                    this.level = '';
                    this.column = '';
                    return;
                }

                if (this.section && this.rack && this.level && this.column) {
                    this.preview = `${this.type}${this.section}-${this.rack}-${this.level}-${this.column}`;
                } else {
                    this.preview = '';
                }
            }
        }
    }
</script>

{{-- Script Barcode Scanner --}}
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
    const codeReader = new ZXing.BrowserMultiFormatReader();
    const startScanBtn = document.getElementById('start-scan');
    const previewElem = document.getElementById('preview');
    const scannerContainer = document.getElementById('scanner-container');
    const barcodeInput = document.getElementById('codeBars');

    // Barcode validation functions
    function isValidBarcodeFormat(barcode) {
        if (!barcode || barcode.trim() === '') return true; // Allow empty

        barcode = barcode.trim();

        // Numeric barcodes (EAN-13, UPC-A, EAN-8)
        if (/^\d+$/.test(barcode)) {
            const length = barcode.length;
            if (length === 13) return validateEAN13(barcode);
            if (length === 12) return validateUPCA(barcode);
            if (length === 8) return validateEAN8(barcode);
            return false;
        }

        // Code 128 (alphanumeric, 1-48 characters)
        if (barcode.length >= 1 && barcode.length <= 48) {
            return /^[\x20-\x7E]*$/.test(barcode); // ASCII printable characters
        }

        return false;
    }

    function validateEAN13(barcode) {
        if (barcode.length !== 13) return false;
        const checkDigit = parseInt(barcode[12]);
        const calculated = calculateEAN13CheckDigit(barcode.substring(0, 12));
        return checkDigit === calculated;
    }

    function validateUPCA(barcode) {
        if (barcode.length !== 12) return false;
        const checkDigit = parseInt(barcode[11]);
        const calculated = calculateUPCACheckDigit(barcode.substring(0, 11));
        return checkDigit === calculated;
    }

    function validateEAN8(barcode) {
        if (barcode.length !== 8) return false;
        const checkDigit = parseInt(barcode[7]);
        const calculated = calculateEAN8CheckDigit(barcode.substring(0, 7));
        return checkDigit === calculated;
    }

    function calculateEAN13CheckDigit(barcode) {
        let sum = 0;
        for (let i = 0; i < 12; i++) {
            const digit = parseInt(barcode[i]);
            sum += (i % 2 === 0) ? digit : digit * 3;
        }
        const remainder = sum % 10;
        return remainder === 0 ? 0 : 10 - remainder;
    }

    function calculateUPCACheckDigit(barcode) {
        let sum = 0;
        for (let i = 0; i < 11; i++) {
            const digit = parseInt(barcode[i]);
            sum += (i % 2 === 0) ? digit * 3 : digit;
        }
        const remainder = sum % 10;
        return remainder === 0 ? 0 : 10 - remainder;
    }

    function calculateEAN8CheckDigit(barcode) {
        let sum = 0;
        for (let i = 0; i < 7; i++) {
            const digit = parseInt(barcode[i]);
            sum += (i % 2 === 0) ? digit * 3 : digit;
        }
        const remainder = sum % 10;
        return remainder === 0 ? 0 : 10 - remainder;
    }

    // Real-time validation
    barcodeInput.addEventListener('input', function() {
        const value = this.value;
        const isValid = isValidBarcodeFormat(value);
        
        // Remove existing validation feedback
        const existingFeedback = this.parentNode.parentNode.querySelector('.barcode-validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        if (value && !isValid) {
            // Show error feedback
            const feedback = document.createElement('div');
            feedback.className = 'barcode-validation-feedback mt-1 text-sm text-red-600 dark:text-red-400';
            feedback.textContent = 'Format barcode tidak valid. Gunakan EAN-13 (13 digit), UPC-A (12 digit), EAN-8 (8 digit), atau Code 128.';
            this.parentNode.parentNode.appendChild(feedback);
            this.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            this.classList.remove('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
        } else {
            // Remove error styling
            this.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            this.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
        }
    });

    startScanBtn.addEventListener('click', async () => {
        scannerContainer.classList.remove('hidden');
        try {
            const devices = await codeReader.listVideoInputDevices();
            const selectedDeviceId = devices[0].deviceId;
            codeReader.decodeFromVideoDevice(selectedDeviceId, previewElem, (result) => {
                if (result) {
                    barcodeInput.value = result.text;
                    barcodeInput.dispatchEvent(new Event('input')); // Trigger validation
                    codeReader.reset();
                    scannerContainer.classList.add('hidden');
                }
            });
        } catch (error) {
            console.error(error);
        }
    });

    // Modal Tambah Kategori
    const modalCategory = document.getElementById('modal-category');
    const btnAddCategory = document.getElementById('btn-add-category');
    const cancelAddCategory = document.getElementById('cancel-add-category');
    const saveCategory = document.getElementById('save-category');
    const categorySelect = document.getElementById('category_id');

    btnAddCategory.addEventListener('click', () => {
        modalCategory.classList.remove('hidden');
    });

    cancelAddCategory.addEventListener('click', () => {
        modalCategory.classList.add('hidden');
    });

    saveCategory.addEventListener('click', async () => {
        const name = document.getElementById('new-category-name').value.trim();
        if (!name) return alert('Nama kategori tidak boleh kosong');

        try {
            const response = await fetch("{{ route('categories.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name })
            });
            const data = await response.json();
            if (data.success) {
                // Tambahkan kategori baru ke dropdown
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.selected = true;
                categorySelect.appendChild(option);
                modalCategory.classList.add('hidden');
                document.getElementById('new-category-name').value = '';
            } else {
                alert(data.message || 'Gagal menambah kategori');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan');
        }
    });
</script>
@endsection
