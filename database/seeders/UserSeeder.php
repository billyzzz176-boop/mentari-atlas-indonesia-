<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Akun Superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin'
        ]);

        User::create([
            'name' => 'Akun Direktur',
            'email' => 'direktur@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'direktur'
        ]);

        User::create([
            'name' => 'Akun Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Akun Sales',
            'email' => 'sales@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'sales'
        ]);

        User::create([
            'name' => 'Akun Manager',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager'
        ]);
    }
}
