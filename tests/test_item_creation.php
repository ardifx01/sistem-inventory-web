<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Item;
use App\Models\Category;

try {
    echo "Testing Item creation with new column names...\n";
    
    // Cek apakah kategori ada
    $category = Category::first();
    if (!$category) {
        echo "ERROR: No category found\n";
        exit;
    }
    
    echo "Found category: " . $category->name . "\n";
    
    // Test create item
    $item = Item::create([
        'dscription' => 'Test Item ' . time(),
        'itemCode' => 'TEST' . time(),
        'codeBars' => '1234567890128',
        'category_id' => $category->id,
        'rack_location' => 'A01'
    ]);
    
    echo "SUCCESS: Item created with ID " . $item->id . "\n";
    echo "Description: " . $item->dscription . "\n";
    echo "Item Code: " . $item->itemCode . "\n";
    echo "Barcode: " . $item->codeBars . "\n";
    
    // Test validasi barcode
    echo "\nTesting barcode validation...\n";
    
    $validBarcode = '1234567890128'; // Valid EAN-13
    $invalidBarcode = '1234567890123'; // Invalid EAN-13
    
    $validator = new App\Rules\BarcodeFormat();
    
    echo "Valid barcode test: ";
    $isValid = true;
    $validator->validate('codeBars', $validBarcode, function($message) use (&$isValid) {
        $isValid = false;
        echo "FAILED - " . $message . "\n";
    });
    if ($isValid) echo "PASSED\n";
    
    echo "Invalid barcode test: ";
    $isValid = true;
    $validator->validate('codeBars', $invalidBarcode, function($message) use (&$isValid) {
        $isValid = false;
        echo "PASSED - " . $message . "\n";
    });
    if ($isValid) echo "FAILED - should have been invalid\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
