<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PharmacySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah sudah ada data, jika belum baru insert
        if (DB::table('pharmacy_settings')->count() === 0) {
            DB::table('pharmacy_settings')->insert([
                'pharmacy_name'   => 'Apotek Sehat Farma',
                'address'         => 'Jl. Kesehatan No.123, Kota Bandung, Jawa Barat 40123',
                'phone'           => '022-1234567',
                'email'           => 'info@apoteksehat.com',
                'logo'            => null,
                'license_number'  => 'SIA-1234/2024/DINKES',
                'tax_rate'        => 0.00,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
