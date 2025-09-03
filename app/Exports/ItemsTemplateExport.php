<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // Contoh baris, bisa dihapus kalau mau kosong
        return [
            ['Contoh Barang', 'MT0001', '1234567890123', 'P05010203'],
        ];
    }

    public function headings(): array
    {
        return [
            'dscription',
            'itemCode',
            'codeBars',
            'rackNo',
        ];
    }
}
