<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Lewati header (baris pertama)
        $rows->skip(1)->each(function ($row) {
            $dscription   = trim($row[0] ?? '');
            $itemCode     = trim($row[1] ?? '');
            $codeBars     = trim($row[2] ?? '');
            $rackLocation = trim($row[3] ?? '') ?: 'ZIP';
            $categoryId   = 1; // default "Belum Dikategorikan"

            // kalau barcode kosong → jadikan NULL
            $codeBars = $codeBars === '' ? null : $codeBars;

            // kalau barcode ada & sudah ada di DB → skip
            if ($codeBars && Item::where('codeBars', $codeBars)->exists()) {
                return;
            }

            // cek apakah sudah ada item dengan nama + kode
            $existingItem = Item::where('dscription', $dscription)
                ->where('itemCode', $itemCode)
                ->first();

            if ($existingItem) {
                // kalau sudah ada, update barcode jika kosong
                if (!$existingItem->codeBars && $codeBars) {
                    $existingItem->update(['codeBars' => $codeBars]);
                }
            } else {
                // kalau belum ada, buat baru
                Item::create([
                    'dscription'   => $dscription,
                    'itemCode'     => $itemCode,
                    'codeBars'     => $codeBars,
                    'rack_location'=> $rackLocation,
                    'category_id'  => $categoryId,
                ]);
            }
        });
    }
}


