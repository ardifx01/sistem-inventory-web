<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportErrorReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $errorDetails;

    public function __construct($errorDetails)
    {
        $this->errorDetails = $errorDetails;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->errorDetails as $error) {
            $data[] = [
                $error['no'] ?? '',
                $error['dscription'] ?? '',
                $error['itemCode'] ?? '',
                $error['codeBars'] ?? '',
                $error['rackNo'] ?? '',
                $error['category'] ?? '',
                $error['penyebab_error'] ?? '',
                $error['waktu_error'] ?? ''
            ];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Dscription (Nama Barang)',
            'Item Code (Kode Item)',
            'CodeBars (Barcode)',
            'RackNo (Lokasi Rak)',
            'Category (Kategori)',
            'Penyebab Error',
            'Waktu Error'
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 5,  // No
            'B' => 25, // Dscription
            'C' => 15, // Item Code
            'D' => 15, // CodeBars
            'E' => 15, // RackNo
            'F' => 12, // Category
            'G' => 30, // Penyebab Error
            'H' => 20, // Waktu Error
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Style the header row
            'A1:H1' => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
            
            // Style all data rows to black text
            'A2:H1000' => [
                'font' => [
                    'color' => ['rgb' => '000000'] // Black color
                ]
            ],
        ];
    }
}
