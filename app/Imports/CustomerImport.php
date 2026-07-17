<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CustomerImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    public function model(array $row)
    {
        // Skip if ID Customer or Nama is empty
        if (empty($row['id_customer']) || empty($row['nama_tokocustomer'])) {
            return null;
        }

        // Cek apakah customer sudah ada
        $customer = Customer::where('id_cust', $row['id_customer'])->first();
        if ($customer) {
            $customer->update([
                'nama_customer'    => $row['nama_tokocustomer'],
                'tingkat_customer' => $row['tingkat_bronzesilvergoldplatinum'] ?? 'Bronze',
                'npwp'             => $row['npwp'] ?? null,
                'ktp'              => $row['ktp'] ?? null,
                'no_telp'          => $row['no_telepon'] ?? null,
                'alamat'           => $row['alamat_lengkap'] ?? null,
                'plafon'           => $row['plafon_kredit_rp'] ?? 0,
                'tempo_hari'       => $row['jatuh_tempo_hari'] ?? 0,
            ]);
            return null;
        }

        return new Customer([
            'id_cust'          => $row['id_customer'],
            'nama_customer'    => $row['nama_tokocustomer'],
            'tingkat_customer' => $row['tingkat_bronzesilvergoldplatinum'] ?? 'Bronze',
            'npwp'             => $row['npwp'] ?? null,
            'ktp'              => $row['ktp'] ?? null,
            'no_telp'          => $row['no_telepon'] ?? null,
            'alamat'           => $row['alamat_lengkap'] ?? null,
            'plafon'           => $row['plafon_kredit_rp'] ?? 0,
            'tempo_hari'       => $row['jatuh_tempo_hari'] ?? 0,
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }
}
