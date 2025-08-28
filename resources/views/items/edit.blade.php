@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Edit Barang') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

            <h2 class="text-2xl font-bold mb-4 text-center">Edit Barang</h2>

            {{-- Notifikasi sukses --}}
            @if(session('success'))
                <div class="bg-green-200 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- {{-- Notifikasi error global --}}
            @if ($errors->any())
                <div class="bg-red-200 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif -->

            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')


{{-- Nama Barang (dscription) --}}
<div>
    <x-input-label for="dscription" :value="__('Nama')" />
    <x-text-input id="dscription" class="block mt-1 w-full"
                  type="text" name="dscription" value="{{ old('dscription', $item->dscription) }}"
                  required autofocus />
    <x-input-error :messages="$errors->get('dscription')" class="mt-2" />
</div>

{{-- Kode Item (itemCode) --}}
<div class="mt-4">
    <x-input-label for="itemCode" :value="__('Kode Item')" />
    <x-text-input id="itemCode" class="block mt-1 w-full"
                  type="text" name="itemCode" value="{{ old('itemCode', $item->itemCode) }}"
                  required />
    <x-input-error :messages="$errors->get('itemCode')" class="mt-2" />
</div>

{{-- Barcode (codeBars) --}}
<div class="mt-4">
    <x-input-label for="codeBars" :value="__('Barcode')" />
    <div class="flex gap-2">
        <x-text-input id="codeBars" class="block w-full"
                      type="text" name="codeBars" value="{{ old('codeBars', $item->codeBars) }}" 
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
                    <select id="category_id" name="category_id"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                {{-- Lokasi Rak --}}
                <div class="mt-4">
                    <x-input-label for="rack_location" :value="__('Lokasi Rak (Opsional)')" />
                    <x-text-input id="rack_location" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  type="text" name="rack_location" value="{{ old('rack_location', $item->rack_location) }}"
                                  placeholder="Contoh: P01-01-02-01" />
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Jika kosong, otomatis akan menjadi <strong>ZIP</strong></p>
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
                        {{ __('Update') }}
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>
</div>

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
        }
    });
</script>
@endsection