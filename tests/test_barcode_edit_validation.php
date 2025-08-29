<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Item;
use Illuminate\Validation\Rule;
use App\Rules\BarcodeFormat;

try {
    echo "=== TESTING BARCODE VALIDATION SAAT EDIT ===\n\n";
    
    // 1. Ambil item yang sudah ada
    $item = Item::first();
    if (!$item) {
        echo "ERROR: No items found\n";
        exit;
    }
    
    echo "1. Item yang akan diedit:\n";
    echo "   ID: {$item->id}\n";
    echo "   Nama: {$item->dscription}\n";
    echo "   Kode: {$item->itemCode}\n";
    echo "   Barcode saat ini: {$item->codeBars}\n\n";
    
    // 2. Test validasi dengan berbagai skenario
    echo "2. Testing berbagai skenario edit barcode:\n\n";
    
    $testCases = [
        [
            'name' => 'Barcode valid EAN-13',
            'barcode' => '9780123456786',
            'should_pass' => true
        ],
        [
            'name' => 'Barcode invalid EAN-13 (check digit salah)',
            'barcode' => '9780123456789',
            'should_pass' => false
        ],
        [
            'name' => 'Barcode invalid format (terlalu pendek)',
            'barcode' => '12345',
            'should_pass' => false
        ],
        [
            'name' => 'Barcode kosong (boleh)',
            'barcode' => '',
            'should_pass' => true
        ],
        [
            'name' => 'Barcode null (boleh)',
            'barcode' => null,
            'should_pass' => true
        ],
        [
            'name' => 'Code 128 valid',
            'barcode' => 'ABC123DEF',
            'should_pass' => true
        ],
        [
            'name' => 'Karakter tidak valid',
            'barcode' => 'ABC123@#$',
            'should_pass' => false
        ]
    ];
    
    foreach ($testCases as $test) {
        echo "   • {$test['name']}: '{$test['barcode']}'\n";
        
        // Simulate validation seperti di ItemController->update()
        $validator = validator([
            'dscription' => $item->dscription,
            'itemCode' => $item->itemCode,
            'codeBars' => $test['barcode'],
            'category_id' => $item->category_id,
        ], [
            'dscription' => 'required|string|max:255',
            'itemCode' => [
                'required',
                'string', 
                'max:100',
                Rule::unique('items', 'itemCode')->ignore($item->id),
            ],
            'codeBars' => [
                'nullable',
                'string',
                'max:100', 
                Rule::unique('items', 'codeBars')->ignore($item->id),
                new BarcodeFormat()
            ],
            'category_id' => 'required|exists:categories,id',
        ], [
            'codeBars.unique' => 'Barcode sudah digunakan oleh barang lain.',
            'itemCode.unique' => 'Kode barang sudah digunakan.',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->get('codeBars');
            echo "     RESULT: DITOLAK - " . implode(', ', $errors) . "\n";
            if ($test['should_pass']) {
                echo "     WARNING: Test case ini seharusnya LOLOS!\n";
            }
        } else {
            echo "     RESULT: DITERIMA\n";
            if (!$test['should_pass']) {
                echo "     WARNING: Test case ini seharusnya DITOLAK!\n";
            }
        }
        echo "\n";
    }
    
    // 3. Test update dengan barcode yang sudah dipakai item lain
    echo "3. Testing duplicate barcode:\n";
    $otherItem = Item::where('id', '!=', $item->id)->first();
    if ($otherItem && $otherItem->codeBars) {
        echo "   Mencoba menggunakan barcode dari item lain: '{$otherItem->codeBars}'\n";
        
        $validator = validator([
            'dscription' => $item->dscription,
            'itemCode' => $item->itemCode,
            'codeBars' => $otherItem->codeBars,
            'category_id' => $item->category_id,
        ], [
            'codeBars' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('items', 'codeBars')->ignore($item->id),
                new BarcodeFormat()
            ],
        ], [
            'codeBars.unique' => 'Barcode sudah digunakan oleh barang lain.',
        ]);
        
        if ($validator->fails()) {
            echo "   RESULT: DITOLAK - " . implode(', ', $validator->errors()->get('codeBars')) . "\n";
        } else {
            echo "   RESULT: DITERIMA (ERROR: Seharusnya ditolak!)\n";
        }
    } else {
        echo "   Tidak ada item lain dengan barcode untuk test duplicate\n";
    }
    
    echo "\n=== TEST SELESAI ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
