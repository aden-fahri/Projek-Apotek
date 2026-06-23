<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Tablet', 'abbreviation' => 'Tab'],
            ['name' => 'Kapsul', 'abbreviation' => 'Kap'],
            ['name' => 'Kaplet', 'abbreviation' => 'Kpl'],
            ['name' => 'Botol', 'abbreviation' => 'Btl'],
            ['name' => 'Tube', 'abbreviation' => 'Tb'],
            ['name' => 'Sachet', 'abbreviation' => 'Sct'],
            ['name' => 'Strip', 'abbreviation' => 'Str'],
            ['name' => 'Box', 'abbreviation' => 'Box'],
            ['name' => 'Ampul', 'abbreviation' => 'Amp'],
            ['name' => 'Vial', 'abbreviation' => 'Vl'],
            ['name' => 'Salep', 'abbreviation' => 'Slp'],
            ['name' => 'Sirup', 'abbreviation' => 'Srp'],
            ['name' => 'Suppositoria', 'abbreviation' => 'Sup'],
            ['name' => 'Patch', 'abbreviation' => 'Ptc'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
