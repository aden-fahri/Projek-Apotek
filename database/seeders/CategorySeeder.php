<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Antibiotik', 'description' => 'Obat untuk mengatasi infeksi bakteri'],
            ['name' => 'Analgesik & Antipiretik', 'description' => 'Obat pereda nyeri dan penurun demam'],
            ['name' => 'Vitamin & Suplemen', 'description' => 'Suplemen kesehatan dan vitamin'],
            ['name' => 'Antasida & Antiulkus', 'description' => 'Obat untuk gangguan lambung dan asam lambung'],
            ['name' => 'Antialergi & Antihistamin', 'description' => 'Obat untuk mengatasi alergi'],
            ['name' => 'Obat Batuk & Flu', 'description' => 'Obat untuk batuk, pilek, dan flu'],
            ['name' => 'Antiseptik & Desinfektan', 'description' => 'Obat luar untuk antiseptik dan desinfektan'],
            ['name' => 'Obat Mata & Telinga', 'description' => 'Obat tetes mata dan telinga'],
            ['name' => 'Obat Kulit', 'description' => 'Obat untuk penyakit dan keluhan kulit'],
            ['name' => 'Obat Kardiovaskular', 'description' => 'Obat untuk jantung dan pembuluh darah'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
