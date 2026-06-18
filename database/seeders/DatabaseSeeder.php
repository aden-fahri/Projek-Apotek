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
        User::updateOrCreate([
            'email' => 'admin@apotek.com'
        ], [
            'name' => 'Admin Apotek',
            'password' => \Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::updateOrCreate([
            'email' => 'kasir@apotek.com'
        ], [
            'name' => 'Kasir Apotek',
            'password' => \Hash::make('password'),
            'role' => 'kasir',
            'is_active' => true,
        ]);
    }
}
