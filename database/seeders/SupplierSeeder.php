<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT. Kimia Farma',
                'contact_person' => 'Budi Santoso',
                'phone' => '021-4287777',
                'email' => 'order@kimiafarma.co.id',
                'address' => 'Jl. Veteran No.9, Jakarta Pusat',
                'city' => 'Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Kalbe Farma',
                'contact_person' => 'Dewi Lestari',
                'phone' => '021-4242808',
                'email' => 'order@kalbefarma.co.id',
                'address' => 'Jl. Let. Jend. Suprapto Kav.4, Jakarta',
                'city' => 'Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Sanbe Farma',
                'contact_person' => 'Ahmad Hidayat',
                'phone' => '022-7312222',
                'email' => 'sales@sanbe.co.id',
                'address' => 'Jl. Tamansari No.10, Bandung',
                'city' => 'Bandung',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Dexa Medica',
                'contact_person' => 'Rina Wulandari',
                'phone' => '0361-720909',
                'email' => 'order@dexa-medica.com',
                'address' => 'Jl. Palembang No.25, Palembang',
                'city' => 'Palembang',
                'is_active' => true,
            ],
            [
                'name' => 'PT. Phapros',
                'contact_person' => 'Eko Prasetyo',
                'phone' => '024-7614041',
                'email' => 'sales@phapros.co.id',
                'address' => 'Jl. Simongan No.131, Semarang',
                'city' => 'Semarang',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
