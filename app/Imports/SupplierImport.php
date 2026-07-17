<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SupplierImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    public function model(array $row)
    {
        // Skip if Kode Supplier or Nama is empty
        if (empty($row['kode_supplier']) || empty($row['nama_supplier'])) {
            return null;
        }

        // Cek apakah supplier sudah ada
        $supplier = Supplier::where('kode_supplier', $row['kode_supplier'])->first();
        if ($supplier) {
            $supplier->update([
                'nama_supplier'    => $row['nama_supplier'],
                'ktp'              => $row['ktp'] ?? null,
                'npwp'             => $row['npwp'] ?? null,
                'telepon'          => $row['telepon'] ?? null,
                'alamat'           => $row['alamat'] ?? null,
                'jatuh_tempo_hari' => $row['jatuh_tempo_pembayaran_hari'] ?? 0,
            ]);
            return null;
        }

        return new Supplier([
            'kode_supplier'    => $row['kode_supplier'],
            'nama_supplier'    => $row['nama_supplier'],
            'ktp'              => $row['ktp'] ?? null,
            'npwp'             => $row['npwp'] ?? null,
            'telepon'          => $row['telepon'] ?? null,
            'alamat'           => $row['alamat'] ?? null,
            'jatuh_tempo_hari' => $row['jatuh_tempo_pembayaran_hari'] ?? 0,
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }
}
