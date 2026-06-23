<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineGroup;

class MedicineGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['name' => 'Obat Bebas', 'code' => 'OB', 'description' => 'Obat yang dapat dibeli tanpa resep dokter. Ditandai lingkaran hijau dengan garis tepi hitam.'],
            ['name' => 'Obat Bebas Terbatas', 'code' => 'OBT', 'description' => 'Obat yang dapat dibeli tanpa resep tetapi dengan peringatan. Ditandai lingkaran biru dengan garis tepi hitam.'],
            ['name' => 'Obat Keras', 'code' => 'OK', 'description' => 'Obat yang hanya dapat diperoleh dengan resep dokter. Ditandai lingkaran merah dengan huruf K.'],
            ['name' => 'Narkotika', 'code' => 'N', 'description' => 'Obat yang mengandung zat narkotika. Memerlukan resep dokter dan pengawasan ketat. Ditandai palang (+) merah.'],
            ['name' => 'Obat Herbal Terstandar', 'code' => 'OHT', 'description' => 'Obat herbal yang telah distandarisasi dan diuji praklinis.'],
        ];

        foreach ($groups as $group) {
            MedicineGroup::updateOrCreate(['name' => $group['name']], $group);
        }
    }
}
