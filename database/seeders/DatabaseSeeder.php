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
                'username' => 'admin',
                'password' => \Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1',
                'is_active' => true,
            ]
        );
 
        User::updateOrCreate(
            ['email' => 'kasir@apotek.com'],
            [
                'name' => 'Siti Kasir',
                'username' => 'kasir',
                'password' => \Hash::make('password'),
                'role' => 'kasir',
                'phone' => '081234567891',
                'address' => 'Jl. Kasir No. 2',
                'is_active' => true,
            ]
        );

        // Seed 40 employees/users manually without using Faker to support --no-dev environments
        for ($i = 1; $i <= 40; $i++) {
            $role = $i % 2 === 0 ? 'kasir' : 'admin';
            User::updateOrCreate(
                ['email' => "user{$i}@apotek.com"],
                [
                    'name' => "Karyawan Apotek {$i}",
                    'username' => "karyawan{$i}",
                    'password' => \Hash::make('password'),
                    'role' => $role,
                    'phone' => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                    'address' => "Alamat Karyawan No. {$i}",
                    'is_active' => true,
                ]
            );
        }

        // Seed data master & inventory dari apotek_db.sql
        $this->call([
            SupplierSeeder::class,
            CategorySeeder::class,
            MedicineGroupSeeder::class,
            UnitSeeder::class,
            MedicineSeeder::class,
            MedicineStockSeeder::class,
            PharmacySettingSeeder::class,
        ]);
    }
}
