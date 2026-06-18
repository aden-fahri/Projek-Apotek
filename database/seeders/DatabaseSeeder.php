<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Akun Admin & Kasir untuk Uji Coba kelompok
        User::updateOrCreate(
            ['email' => 'admin@apotek.com'],
            [
                'name' => 'Administrator',
                'password' => \Hash::make('password'),
                'role' => 'admin',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Admin No. 1',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@apotek.com'],
            [
                'name' => 'Siti Kasir',
                'password' => \Hash::make('password'),
                'role' => 'kasir',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Kasir No. 2',
                'is_active' => true,
            ]
        );

        // 40 random employees
        User::factory(40)->create();
    }
}
