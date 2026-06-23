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
                'pharmacy_name'   => 'Apotek MediFlow',
                'address'         => 'Jl. Kesehatan No. 1, Kota',
                'phone'           => '021-0000000',
                'email'           => 'apotek@mediflow.id',
                'logo'            => null,
                'license_number'      => 'SIA-000/000/00',
                'pharmacist_name'     => 'Apt. Nama Apoteker, S.Farm.',
                'pharmacist_license'  => 'SIPA-000/000/00',
                'footer_note'     => 'Terima kasih telah mempercayai pelayanan kami.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
