<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Direktur
        User::create([
            'name' => 'Direktur Utama',
            'email' => 'direktur@gmail.com',
            'password' => Hash::make('direktur123'),
            'role' => 'direktur',
        ]);

        // 2. Akun Sales
        User::create([
            'name' => 'Sales User',
            'email' => 'sales@gmail.com',
            'password' => Hash::make('sales123'),
            'role' => 'sales',
        ]);

        // 3. Akun Warehouse (Gudang)
        User::create([
            'name' => 'Warehouse User',
            'email' => 'gudang@gmail.com',
            'password' => Hash::make('gudang123'),
            'role' => 'admin_warehouse',
        ]);

        // 4. Akun Keuangan
        User::create([
            'name' => 'Keuangan User',
            'email' => 'keuangan@gmail.com',
            'password' => Hash::make('keuangan123'),
            'role' => 'admin_keuangan',
        ]);

        // 5. Akun Manager
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
        ]);
    }
}