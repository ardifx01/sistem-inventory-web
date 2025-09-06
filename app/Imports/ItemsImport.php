<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection
{
    public $successCount = 0;
    public $failedCount = 0;
    public $failedItems = [];
    public $errorDetails = []; // Store detailed error info

    public function collection(Collection $rows)
    {
        $rowCounter = 1; // Counter untuk nomor urut
        $rows->skip(1)->each(function ($row, $index) use (&$rowCounter) {
            $rowNumber = $index + 2; // Excel row number (header + 0-based index)
            $dscription   = trim($row[0] ?? '');
            $itemCode     = trim($row[1] ?? '');
            $codeBars     = trim($row[2] ?? '');
            $rackNo       = trim($row[3] ?? '');
            $categoryName = trim($row[4] ?? ''); // Tambah kolom kategori
            
            $rackLocation = $this->formatRack($rackNo);
            
            // Set default values jika kosong
            if (empty($dscription)) {
                $dscription = 'Item Tanpa Deskripsi';
            }
            
            $codeBars = $codeBars === '' ? null : $codeBars;
            
            // Validasi dan cari kategori
            $categoryId = $this->validateCategory($categoryName);
            $displayCategory = empty($categoryName) ? 'Belum Dikategorikan' : $categoryName;

            try {
                // Validasi kategori jika tidak kosong
                if (!empty($categoryName) && $categoryId === null) {
                    $this->failedCount++;
                    $this->failedItems[] = $itemCode;
                    
                    $this->errorDetails[] = [
                        'no' => $rowCounter++,
                        'dscription' => $dscription,
                        'itemCode' => $itemCode,
                        'codeBars' => $codeBars ?: '-',
                        'rackNo' => $rackLocation,
                        'category' => $categoryName,
                        'penyebab_error' => 'Kategori tidak ditemukan dalam database',
                        'waktu_error' => now()->format('Y-m-d H:i:s')
                    ];
                    return;
                }

                // Check for barcode duplication first
                if ($codeBars && Item::where('codeBars', $codeBars)->exists()) {
                    $this->failedCount++;
                    $this->failedItems[] = $itemCode;
                    
                    // Store detailed error info
                    $this->errorDetails[] = [
                        'no' => $rowCounter++,
                        'dscription' => $dscription,
                        'itemCode' => $itemCode,
                        'codeBars' => $codeBars,
                        'rackNo' => $rackLocation, // Use formatted rack location
                        'category' => $displayCategory,
                        'penyebab_error' => 'Barcode sudah terdaftar',
                        'waktu_error' => now()->format('Y-m-d H:i:s')
                    ];
                    return;
                }

                // Check for rack location duplication (except ZIP)
                if ($rackLocation !== 'ZIP' && Item::where('rack_location', $rackLocation)->exists()) {
                    $this->failedCount++;
                    $this->failedItems[] = $itemCode;
                    
                    // Store detailed error info
                    $this->errorDetails[] = [
                        'no' => $rowCounter++,
                        'dscription' => $dscription,
                        'itemCode' => $itemCode,
                        'codeBars' => $codeBars ?: '-',
                        'rackNo' => $rackLocation,
                        'category' => $displayCategory,
                        'penyebab_error' => 'Lokasi rak sudah diisi',
                        'waktu_error' => now()->format('Y-m-d H:i:s')
                    ];
                    return;
                }

                // Check for required fields
                if (empty($itemCode)) {
                    $this->failedCount++;
                    $this->failedItems[] = $itemCode ?: 'Item tanpa kode';
                    
                    $this->errorDetails[] = [
                        'no' => $rowCounter++,
                        'dscription' => $dscription,
                        'itemCode' => $itemCode,
                        'codeBars' => $codeBars ?: '-',
                        'rackNo' => $rackLocation,
                        'category' => $displayCategory,
                        'penyebab_error' => 'Kode item wajib diisi',
                        'waktu_error' => now()->format('Y-m-d H:i:s')
                    ];
                    return;
                }

                $existingItem = Item::where('dscription', $dscription)
                    ->where('itemCode', $itemCode)
                    ->first();

                if ($existingItem) {
                    if (!$existingItem->codeBars && $codeBars) {
                        $existingItem->update(['codeBars' => $codeBars]);
                        $this->successCount++;
                    } else {
                        $this->failedCount++;
                        $this->failedItems[] = $itemCode;
                        
                        // Store detailed error info
                        $this->errorDetails[] = [
                            'no' => $rowCounter++,
                            'dscription' => $dscription,
                            'itemCode' => $itemCode,
                            'codeBars' => $codeBars ?: '-',
                            'rackNo' => $rackLocation,
                            'category' => $displayCategory,
                            'penyebab_error' => 'Barang dengan kode dan nama yang sama sudah terdaftar',
                            'waktu_error' => now()->format('Y-m-d H:i:s')
                        ];
                    }
                } else {
                    Item::create([
                        'dscription'    => $dscription,
                        'itemCode'      => $itemCode,
                        'codeBars'      => $codeBars,
                        'rack_location' => $rackLocation,
                        'category_id'   => $categoryId,
                    ]);
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->failedCount++;
                $this->failedItems[] = $itemCode ?: 'Item tanpa kode';
                
                // Parse error message to make it user-friendly
                $userFriendlyError = $this->parseErrorMessage($e->getMessage());
                
                // Store detailed error info
                $this->errorDetails[] = [
                    'no' => $rowCounter++,
                    'dscription' => $dscription,
                    'itemCode' => $itemCode,
                    'codeBars' => $codeBars ?: '-',
                    'rackNo' => $rackLocation,
                    'category' => $displayCategory,
                    'penyebab_error' => $userFriendlyError,
                    'waktu_error' => now()->format('Y-m-d H:i:s')
                ];
            }
        });
    }

    private function parseErrorMessage($errorMessage)
    {
        // Convert technical SQL errors to user-friendly messages
        if (strpos($errorMessage, 'Duplicate entry') !== false) {
            if (strpos($errorMessage, 'items_itemcode_unique') !== false) {
                return 'Kode item sudah terdaftar';
            }
            if (strpos($errorMessage, 'items_codebars_unique') !== false) {
                return 'Barcode sudah terdaftar';
            }
            if (strpos($errorMessage, 'rack_location') !== false) {
                return 'Lokasi rak sudah diisi';
            }
            return 'Data duplikat terdeteksi';
        }
        
        if (strpos($errorMessage, 'cannot be null') !== false || strpos($errorMessage, 'required') !== false) {
            return 'Ada field wajib yang kosong';
        }
        
        if (strpos($errorMessage, 'too long') !== false || strpos($errorMessage, 'Data too long') !== false) {
            return 'Data terlalu panjang';
        }
        
        if (strpos($errorMessage, 'foreign key') !== false) {
            return 'Kategori tidak valid';
        }
        
        // Default fallback for unknown errors
        return 'Format data tidak sesuai';
    }

    private function validateCategory($categoryName)
    {
        // Jika kosong, gunakan kategori default
        if (empty($categoryName)) {
            $defaultCategory = Category::where('is_default', true)->first();
            return $defaultCategory ? $defaultCategory->id : 1;
        }

        // Cari kategori berdasarkan nama (case insensitive)
        $category = Category::whereRaw('LOWER(name) = LOWER(?)', [$categoryName])->first();
        
        if ($category) {
            return $category->id;
        }

        // Jika tidak ditemukan, return null untuk menandakan error
        return null;
    }

    private function formatRack($rack)
    {
        $rack = trim($rack ?? '');
        if ($rack === '') {
            return 'ZIP';
        }

        // Ambil huruf depan (P, B, L, dst)
        $prefixChar = substr($rack, 0, 1);

        // Ambil hanya digit setelah huruf
        $digits = preg_replace('/\D/', '', $rack);

        if (strlen($digits) < 2) {
            return 'ZIP';
        }

        // Ambil 2 digit pertama setelah huruf
        $prefixNum = substr($digits, 0, 2);
        $rest      = substr($digits, 2);

        // Pecah sisanya per 2 digit
        $parts = str_split($rest, 2);

        // Gabungkan
        return $prefixChar . $prefixNum . '-' . implode('-', $parts);
    }
}
