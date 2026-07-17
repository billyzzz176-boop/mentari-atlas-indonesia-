<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'ID Customer',
            'Nama Toko/Customer',
            'Tingkat (Bronze/Silver/Gold/Platinum)',
            'NPWP',
            'KTP',
            'No Telepon',
            'Alamat Lengkap',
            'Plafon Kredit (Rp)',
            'Jatuh Tempo (Hari)'
        ];
    }

    public function array(): array
    {
        return [
            [
                'CUST-001',
                'Toko Sinar Motor',
                'Bronze',
                '12.345.678.9-012.000',
                '3271234567890001',
                '081234567890',
                'Jl. Raya Utama No.12, Jakarta',
                '10000000',
                '30'
            ],
            [
                'CUST-002',
                'Bengkel Jaya Makmur',
                'Gold',
                '',
                '3271234567890002',
                '081987654321',
                'Jl. Pahlawan No. 45, Bandung',
                '50000000',
                '45'
            ]
        ];
    }
}
