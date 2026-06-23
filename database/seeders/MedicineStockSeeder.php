<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineStock;
use App\Models\Medicine;

class MedicineStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = Medicine::pluck('id', 'code')->toArray();

        $stocks = [
            [
                'medicine_code' => 'OBT-001',
                'batch_number' => 'BATCH-PCM-2026A',
                'quantity' => 500,
                'initial_quantity' => 500,
                'expiry_date' => '2028-06-30',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-002',
                'batch_number' => 'BATCH-AMX-2026A',
                'quantity' => 200,
                'initial_quantity' => 200,
                'expiry_date' => '2028-03-15',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-003',
                'batch_number' => 'BATCH-VTC-2026A',
                'quantity' => 300,
                'initial_quantity' => 300,
                'expiry_date' => '2027-12-31',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-004',
                'batch_number' => 'BATCH-ANT-2026A',
                'quantity' => 150,
                'initial_quantity' => 150,
                'expiry_date' => '2028-09-30',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-005',
                'batch_number' => 'BATCH-CTZ-2026A',
                'quantity' => 100,
                'initial_quantity' => 100,
                'expiry_date' => '2028-01-15',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-006',
                'batch_number' => 'BATCH-ABX-2026A',
                'quantity' => 80,
                'initial_quantity' => 80,
                'expiry_date' => '2027-06-30',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-007',
                'batch_number' => 'BATCH-BTD-2026A',
                'quantity' => 60,
                'initial_quantity' => 60,
                'expiry_date' => '2028-12-31',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-008',
                'batch_number' => 'BATCH-CXT-2026A',
                'quantity' => 40,
                'initial_quantity' => 40,
                'expiry_date' => '2027-09-30',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-009',
                'batch_number' => 'BATCH-HCC-2026A',
                'quantity' => 50,
                'initial_quantity' => 50,
                'expiry_date' => '2028-06-15',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-010',
                'batch_number' => 'BATCH-AML-2026A',
                'quantity' => 120,
                'initial_quantity' => 120,
                'expiry_date' => '2028-08-31',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-011',
                'batch_number' => 'BATCH-IBU-2026A',
                'quantity' => 200,
                'initial_quantity' => 200,
                'expiry_date' => '2028-04-30',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-012',
                'batch_number' => 'BATCH-OMP-2026A',
                'quantity' => 100,
                'initial_quantity' => 100,
                'expiry_date' => '2027-11-30',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-013',
                'batch_number' => 'BATCH-DXM-2026A',
                'quantity' => 150,
                'initial_quantity' => 150,
                'expiry_date' => '2028-02-28',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-014',
                'batch_number' => 'BATCH-OBH-2026A',
                'quantity' => 70,
                'initial_quantity' => 70,
                'expiry_date' => '2027-10-31',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-015',
                'batch_number' => 'BATCH-S24-2026A',
                'quantity' => 90,
                'initial_quantity' => 90,
                'expiry_date' => '2028-07-31',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-001',
                'batch_number' => 'BATCH-PCM-2025B',
                'quantity' => 20,
                'initial_quantity' => 100,
                'expiry_date' => '2026-07-15',
                'status' => 'available',
            ],
            [
                'medicine_code' => 'OBT-003',
                'batch_number' => 'BATCH-VTC-2025B',
                'quantity' => 10,
                'initial_quantity' => 50,
                'expiry_date' => '2026-07-01',
                'status' => 'available',
            ],
        ];

        foreach ($stocks as $stock) {
            $medicineId = $medicines[$stock['medicine_code']] ?? null;
            if ($medicineId) {
                MedicineStock::updateOrCreate(
                    [
                        'medicine_id' => $medicineId,
                        'batch_number' => $stock['batch_number'],
                    ],
                    [
                        'quantity' => $stock['quantity'],
                        'initial_quantity' => $stock['initial_quantity'],
                        'expiry_date' => $stock['expiry_date'],
                        'status' => $stock['status'],
                    ]
                );
            }
        }
    }
}
