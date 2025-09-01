<?php

namespace App\Imports;

use App\Models\Item;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ItemsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $dscription   = trim($row['dscription'] ?? $row[0] ?? '');
                $itemCode     = trim($row['item_code'] ?? $row[1] ?? '');
                $codeBars     = trim($row['code_bars'] ?? $row[2] ?? '');
                $rackLocation = trim($row['rack_location'] ?? $row[3] ?? '') ?: 'ZIP';
                $categoryId   = 1; // default "Belum Dikategorikan"

                // Skip jika data penting kosong
                if (empty($dscription) && empty($itemCode)) {
                    continue;
                }

                // kalau barcode kosong → jadikan NULL
                $codeBars = $codeBars === '' ? null : $codeBars;

                // kalau barcode ada & sudah ada di DB → skip
                if ($codeBars && Item::where('codeBars', $codeBars)->exists()) {
                    continue;
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
            } catch (\Exception $e) {
                Log::error('Error importing row: ' . $e->getMessage(), [
                    'row' => $row->toArray()
                ]);
                // Continue processing other rows
                continue;
            }
        }
    }
}
