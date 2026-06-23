<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class ClearDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // Truncate all transaction & master tables
        DB::table('transaction_details')->truncate();
        DB::table('transactions')->truncate();
        DB::table('stock_return_details')->truncate();
        DB::table('stock_returns')->truncate();
        DB::table('medicine_stocks')->truncate();
        DB::table('purchase_order_details')->truncate();
        DB::table('purchase_orders')->truncate();
        DB::table('medicines')->truncate();
        DB::table('suppliers')->truncate();
        DB::table('categories')->truncate();
        DB::table('medicine_groups')->truncate();
        DB::table('units')->truncate();
        DB::table('activity_logs')->truncate();
        DB::table('pharmacy_settings')->truncate();
        DB::table('users')->truncate();

        Schema::enableForeignKeyConstraints();

        // Seed Akun Admin 1
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@apotek.com',
            'password' => \Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1',
            'is_active' => true,
        ]);

        // Seed Akun Kasir 1
        User::create([
            'name' => 'Siti Kasir',
            'username' => 'kasir',
            'email' => 'kasir@apotek.com',
            'password' => \Hash::make('password'),
            'role' => 'kasir',
            'phone' => '081234567891',
            'address' => 'Jl. Kasir No. 2',
            'is_active' => true,
        ]);

        // Seed default pharmacy settings, categories, groups, and units
        $this->call([
            PharmacySettingSeeder::class,
            CategorySeeder::class,
            MedicineGroupSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
