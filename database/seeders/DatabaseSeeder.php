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
        // Admin test user
        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'admin@mediflow.com',
            'role' => 'admin',
            'telepon' => '0812-3456-7890',
            'alamat' => 'Jl. Kebon Jeruk No. 12, Jakarta',
            'is_active' => true,
        ]);

        // Kasir test user
        User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'kasir@mediflow.com',
            'role' => 'kasir',
            'telepon' => '0856-7890-1234',
            'alamat' => 'Jl. Mawar Indah No. 45, Bandung',
            'is_active' => true,
        ]);

        // 40 random employees
        User::factory(40)->create();
    }
}
