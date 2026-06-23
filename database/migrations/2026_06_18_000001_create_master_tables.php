<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Medicine Groups
        Schema::create('medicine_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 10)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('abbreviation', 20)->nullable();
            $table->timestamps();
        });

        // 5. Medicines
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('group_id')->constrained('medicine_groups')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('unit_id')->constrained('units')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('min_stock')->default(10);
            $table->text('description')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Pharmacy Settings
        Schema::create('pharmacy_settings', function (Blueprint $table) {
            $table->id();
            $table->string('pharmacy_name')->default('Apotek Sehat');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('license_number', 100)->nullable();
            $table->string('pharmacist_name')->nullable();
            $table->string('pharmacist_license')->nullable();
            $table->text('footer_note')->nullable();
            $table->timestamps();
        });

        // 7. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('activity');
            $table->string('module', 100)->nullable();
            $table->enum('action', ['create', 'update', 'delete', 'login', 'logout', 'other']);
            $table->json('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('pharmacy_settings');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('units');
        Schema::dropIfExists('medicine_groups');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('suppliers');
    }
};
