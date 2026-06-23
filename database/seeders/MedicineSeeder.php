<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\MedicineGroup;
use App\Models\Unit;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category mapping
        $categories = Category::pluck('id', 'name')->toArray();
        // Get group mapping
        $groups = MedicineGroup::pluck('id', 'name')->toArray();
        // Get unit mapping
        $units = Unit::pluck('id', 'name')->toArray();

        $medicines = [
            [
                'code' => 'OBT-001',
                'name' => 'Paracetamol 500mg',
                'category_id' => $categories['Analgesik & Antipiretik'] ?? 2,
                'group_id' => $groups['Obat Bebas'] ?? 1,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 250.00,
                'selling_price' => 500.00,
                'min_stock' => 100,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-002',
                'name' => 'Amoxicillin 500mg',
                'category_id' => $categories['Antibiotik'] ?? 1,
                'group_id' => $groups['Obat Keras'] ?? 3,
                'unit_id' => $units['Kapsul'] ?? 2,
                'purchase_price' => 800.00,
                'selling_price' => 1500.00,
                'min_stock' => 50,
                'requires_prescription' => true,
            ],
            [
                'code' => 'OBT-003',
                'name' => 'Vitamin C 1000mg',
                'category_id' => $categories['Vitamin & Suplemen'] ?? 3,
                'group_id' => $groups['Obat Bebas'] ?? 1,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 500.00,
                'selling_price' => 1000.00,
                'min_stock' => 80,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-004',
                'name' => 'Antasida DOEN',
                'category_id' => $categories['Antasida & Antiulkus'] ?? 4,
                'group_id' => $groups['Obat Bebas'] ?? 1,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 200.00,
                'selling_price' => 400.00,
                'min_stock' => 60,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-005',
                'name' => 'Cetirizine 10mg',
                'category_id' => $categories['Antialergi & Antihistamin'] ?? 5,
                'group_id' => $groups['Obat Bebas Terbatas'] ?? 2,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 350.00,
                'selling_price' => 700.00,
                'min_stock' => 40,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-006',
                'name' => 'Ambroxol Sirup 60ml',
                'category_id' => $categories['Obat Batuk & Flu'] ?? 6,
                'group_id' => $groups['Obat Bebas Terbatas'] ?? 2,
                'unit_id' => $units['Botol'] ?? 4,
                'purchase_price' => 8000.00,
                'selling_price' => 15000.00,
                'min_stock' => 30,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-007',
                'name' => 'Betadine 30ml',
                'category_id' => $categories['Antiseptik & Desinfektan'] ?? 7,
                'group_id' => $groups['Obat Bebas'] ?? 1,
                'unit_id' => $units['Botol'] ?? 4,
                'purchase_price' => 12000.00,
                'selling_price' => 22000.00,
                'min_stock' => 25,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-008',
                'name' => 'Cendo Xitrol Tetes Mata',
                'category_id' => $categories['Obat Mata & Telinga'] ?? 8,
                'group_id' => $groups['Obat Keras'] ?? 3,
                'unit_id' => $units['Botol'] ?? 4,
                'purchase_price' => 25000.00,
                'selling_price' => 45000.00,
                'min_stock' => 15,
                'requires_prescription' => true,
            ],
            [
                'code' => 'OBT-009',
                'name' => 'Hydrocortisone Cream 2.5%',
                'category_id' => $categories['Obat Kulit'] ?? 9,
                'group_id' => $groups['Obat Keras'] ?? 3,
                'unit_id' => $units['Tube'] ?? 5,
                'purchase_price' => 10000.00,
                'selling_price' => 18000.00,
                'min_stock' => 20,
                'requires_prescription' => true,
            ],
            [
                'code' => 'OBT-010',
                'name' => 'Amlodipine 5mg',
                'category_id' => $categories['Obat Kardiovaskular'] ?? 10,
                'group_id' => $groups['Obat Keras'] ?? 3,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 600.00,
                'selling_price' => 1200.00,
                'min_stock' => 30,
                'requires_prescription' => true,
            ],
            [
                'code' => 'OBT-011',
                'name' => 'Ibuprofen 400mg',
                'category_id' => $categories['Analgesik & Antipiretik'] ?? 2,
                'group_id' => $groups['Obat Bebas Terbatas'] ?? 2,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 400.00,
                'selling_price' => 800.00,
                'min_stock' => 50,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-012',
                'name' => 'Omeprazole 20mg',
                'category_id' => $categories['Antasida & Antiulkus'] ?? 4,
                'group_id' => $groups['Obat Keras'] ?? 3,
                'unit_id' => $units['Kapsul'] ?? 2,
                'purchase_price' => 1200.00,
                'selling_price' => 2500.00,
                'min_stock' => 30,
                'requires_prescription' => true,
            ],
            [
                'code' => 'OBT-013',
                'name' => 'Dexamethasone 0.5mg',
                'category_id' => $categories['Antialergi & Antihistamin'] ?? 5,
                'group_id' => $groups['Obat Keras'] ?? 3,
                'unit_id' => $units['Tablet'] ?? 1,
                'purchase_price' => 300.00,
                'selling_price' => 600.00,
                'min_stock' => 40,
                'requires_prescription' => true,
            ],
            [
                'code' => 'OBT-014',
                'name' => 'OBH Combi Sirup 100ml',
                'category_id' => $categories['Obat Batuk & Flu'] ?? 6,
                'group_id' => $groups['Obat Bebas'] ?? 1,
                'unit_id' => $units['Botol'] ?? 4,
                'purchase_price' => 15000.00,
                'selling_price' => 28000.00,
                'min_stock' => 25,
                'requires_prescription' => false,
            ],
            [
                'code' => 'OBT-015',
                'name' => 'Salep 2-4 (Antiseptik)',
                'category_id' => $categories['Antiseptik & Desinfektan'] ?? 7,
                'group_id' => $groups['Obat Bebas'] ?? 1,
                'unit_id' => $units['Tube'] ?? 5,
                'purchase_price' => 5000.00,
                'selling_price' => 10000.00,
                'min_stock' => 20,
                'requires_prescription' => false,
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::updateOrCreate(['code' => $medicine['code']], $medicine);
        }
    }
}
