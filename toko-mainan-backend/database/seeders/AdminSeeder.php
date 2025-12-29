<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        User::firstOrCreate(
            ['email' => 'admin@tokomainan.com'],
            [
                'name' => 'Admin Toko Mainan',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Kantor Toko Mainan',
            ]
        );

        // Create customer user for testing
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer Test',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '081234567891',
                'address' => 'Jakarta, Indonesia',
            ]
        );

        // Create customer iwak@gmail.com
        User::firstOrCreate(
            ['email' => 'iwak@gmail.com'],
            [
                'name' => 'Iwak Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '081234567892',
                'address' => 'Bandung, Indonesia',
            ]
        );
    }
}
