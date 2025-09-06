<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Get category examples: default category and one other
        $defaultCategory = Category::where('is_default', true)->first();
        $otherCategory = Category::where('is_default', false)->first();
        
        // Hanya 2 baris contoh data
        return [
            [
                '(contoh) Laptop ASUS ROG Strix', 
                'LPT001', 
                '1234567890123', 
                'P01010203', 
                $defaultCategory ? $defaultCategory->name : 'Belum Dikategorikan'
            ],
            [
                '(contoh) Air Mineral', 
                'AM001', 
                '', // Barcode boleh kosong
                'P01010204', 
                $otherCategory ? $otherCategory->name : 'Kosmetik dan Skincare'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'dscription (Nama Barang)',
            'itemCode (Kode Item)',
            'codeBars (Barcode)', 
            'rackNo (Nomor Rak)',
            'category (Kategori)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'E3F2FD']
                ],
                'alignment' => [
                    'horizontal' => 'center',
                ],
            ],
            // Style for data rows - normal styling
            '2:3' => [
                'font' => [
                    'size' => 11,
                    'color' => ['rgb' => '000000'] // Normal black text
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35, // dscription (Nama Barang)
            'B' => 20, // itemCode (Kode Item)
            'C' => 20, // codeBars (Barcode)
            'D' => 20, // rackNo (Nomor Rak)
            'E' => 25, // category (Kategori)
        ];
    }
}
