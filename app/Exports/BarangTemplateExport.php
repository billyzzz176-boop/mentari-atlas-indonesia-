<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Spesifikasi',
            'Kategori',
            'Merek',
            'Satuan',
            'Stok Awal',
            'Harga Beli (HPP)',
            'Harga Jual',
            'Lokasi Rak',
            'Supplier'
        ];
    }

    public function array(): array
    {
        return [
            [
                'BRG001',
                'Busi Racing Iridium',
                'Untuk Motor Sport',
                'Sparepart',
                'NGK',
                'Pcs',
                '100',
                '25000',
                '35000',
                'Rak A1',
                'PT Sumber Teknik'
            ],
            [
                'BRG002',
                'Oli Gardan',
                '120ml',
                'Oli',
                'Yamalube',
                'Botol',
                '50',
                '12000',
                '17000',
                'Rak B2',
                ''
            ]
        ];
    }
}
