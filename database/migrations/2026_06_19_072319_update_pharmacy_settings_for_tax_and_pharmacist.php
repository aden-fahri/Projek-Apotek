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
        Schema::table('pharmacy_settings', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('email');
            $table->dropColumn(['pharmacist_name', 'pharmacist_license', 'footer_note']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_settings', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
            $table->string('pharmacist_name')->nullable();
            $table->string('pharmacist_license')->nullable();
            $table->text('footer_note')->nullable();
        });
    }
};
