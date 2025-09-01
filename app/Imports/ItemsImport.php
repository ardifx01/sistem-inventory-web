<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection
{
    public $successCount = 0;
    public $failedCount = 0;
    public $failedItems = [];

    public function collection(Collection $rows)
    {
        $rows->skip(1)->each(function ($row) {
            $dscription   = trim($row[0] ?? '');
            $itemCode     = trim($row[1] ?? '');
            $codeBars     = trim($row[2] ?? '');
            $rackNo       = trim($row[3] ?? '');
            $rackLocation = $this->formatRack($rackNo);
            $categoryId   = 1;

            $codeBars = $codeBars === '' ? null : $codeBars;

            try {
                if ($codeBars && Item::where('codeBars', $codeBars)->exists()) {
                    $this->failedCount++;
                    $this->failedItems[] = $itemCode;
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
                $this->failedItems[] = $itemCode;
            }
        });
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
