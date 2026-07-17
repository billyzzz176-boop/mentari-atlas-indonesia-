<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class BarangImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if Kode Barang or Nama Barang is empty
        if (empty($row['kode_barang']) || empty($row['nama_barang'])) {
            return null;
        }

        // Cek apakah barang sudah ada berdasarkan kode_barang
        $barang = Barang::where('kode_barang', $row['kode_barang'])->first();
        if ($barang) {
            // Update existing barang
            $barang->update([
                'nama_barang' => $row['nama_barang'],
                'spesifikasi' => $row['spesifikasi'] ?? null,
                'kategori'    => $row['kategori'] ?? null,
                'merek'       => $row['merek'] ?? null,
                'satuan'      => $row['satuan'] ?? null,
                'stok_awal'   => $row['stok_awal'] ?? 0,
                'stok_akhir'  => $row['stok_awal'] ?? 0, // Set stok akhir = stok awal saat awal import
                'harga_beli'  => $row['harga_beli_hpp'] ?? 0,
                'harga_jual'  => $row['harga_jual'] ?? 0,
                'lokasi_rak'  => $row['lokasi_rak'] ?? null,
                'supplier'    => $row['supplier'] ?? null,
                'tanggal_update' => Carbon::now()->toDateString(),
            ]);
            return null; // Return null because we manually updated
        }

        return new Barang([
            'kode_barang' => $row['kode_barang'],
            'nama_barang' => $row['nama_barang'],
            'spesifikasi' => $row['spesifikasi'] ?? null,
            'kategori'    => $row['kategori'] ?? null,
            'merek'       => $row['merek'] ?? null,
            'satuan'      => $row['satuan'] ?? null,
            'stok_awal'   => $row['stok_awal'] ?? 0,
            'stok_akhir'  => $row['stok_awal'] ?? 0,
            'barang_masuk' => 0,
            'barang_keluar' => 0,
            'stok_rusak'  => 0,
            'harga_beli'  => $row['harga_beli_hpp'] ?? 0,
            'harga_jual'  => $row['harga_jual'] ?? 0,
            'lokasi_rak'  => $row['lokasi_rak'] ?? null,
            'supplier'    => $row['supplier'] ?? null,
            'tanggal_update' => Carbon::now()->toDateString(),
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }
}
