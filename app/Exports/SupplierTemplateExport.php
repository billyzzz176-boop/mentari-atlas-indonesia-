<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Kode Supplier',
            'Nama Supplier',
            'KTP',
            'NPWP',
            'Telepon',
            'Alamat',
            'Jatuh Tempo Pembayaran (Hari)'
        ];
    }

    public function array(): array
    {
        return [
            [
                'SUP-001',
                'PT Sumber Teknik Otomotif',
                '',
                '98.765.432.1-012.000',
                '021-12345678',
                'Kawasan Industri Pulo Gadung, Jakarta Timur',
                '45'
            ],
            [
                'SUP-002',
                'CV Abadi Jaya',
                '3271234567890005',
                '',
                '081223344556',
                'Jl. Gatot Subroto No 10, Jakarta Selatan',
                '30'
            ]
        ];
    }
}
