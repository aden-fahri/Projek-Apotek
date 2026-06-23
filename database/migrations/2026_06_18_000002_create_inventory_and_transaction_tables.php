<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->date('order_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });

        // 2. Medicine Stocks
        Schema::create('medicine_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onUpdate('cascade')->onDelete('set null');
            $table->string('batch_number', 100)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('initial_quantity')->default(0);
            $table->date('expiry_date');
            $table->enum('status', ['available', 'expired', 'returned'])->default('available');
            $table->timestamps();
        });

        // 3. Purchase Order Details
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('batch_number', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        // 4. Transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->dateTime('transaction_date')->useCurrent();
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['tunai', 'qris', 'transfer'])->default('tunai');
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });

        // 5. Transaction Details
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('medicine_stock_id')->nullable()->constrained('medicine_stocks')->onUpdate('cascade')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // 6. Stock Returns
        Schema::create('stock_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->date('return_date');
            $table->enum('reason', ['expired', 'damaged', 'wrong_item', 'other'])->default('expired');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // 7. Stock Return Details
        Schema::create('stock_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_return_id')->constrained('stock_returns')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('medicine_stock_id')->nullable()->constrained('medicine_stocks')->onUpdate('cascade')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // Create database views
        DB::statement("
            CREATE OR REPLACE VIEW `v_medicine_stock_summary` AS
            SELECT 
                m.id AS medicine_id,
                m.code AS medicine_code,
                m.name AS medicine_name,
                c.name AS category_name,
                mg.name AS group_name,
                u.name AS unit_name,
                m.purchase_price,
                m.selling_price,
                m.min_stock,
                COALESCE(SUM(CASE WHEN ms.status = 'available' THEN ms.quantity ELSE 0 END), 0) AS total_stock,
                MIN(CASE WHEN ms.status = 'available' THEN ms.expiry_date END) AS nearest_expiry,
                CASE 
                    WHEN COALESCE(SUM(CASE WHEN ms.status = 'available' THEN ms.quantity ELSE 0 END), 0) <= 0 THEN 'Habis'
                    WHEN COALESCE(SUM(CASE WHEN ms.status = 'available' THEN ms.quantity ELSE 0 END), 0) <= m.min_stock THEN 'Stok Rendah'
                    ELSE 'Optimal'
                END AS stock_status
            FROM `medicines` m
            LEFT JOIN `categories` c ON m.category_id = c.id
            LEFT JOIN `medicine_groups` mg ON m.group_id = mg.id
            LEFT JOIN `units` u ON m.unit_id = u.id
            LEFT JOIN `medicine_stocks` ms ON m.id = ms.medicine_id
            WHERE m.is_active = 1
            GROUP BY m.id, m.code, m.name, c.name, mg.name, u.name, m.purchase_price, m.selling_price, m.min_stock;
        ");

        DB::statement("
            CREATE OR REPLACE VIEW `v_expiring_medicines` AS
            SELECT 
                ms.id AS stock_id,
                m.code AS medicine_code,
                m.name AS medicine_name,
                ms.batch_number,
                ms.quantity,
                ms.expiry_date,
                DATEDIFF(ms.expiry_date, CURDATE()) AS days_until_expiry,
                CASE 
                    WHEN ms.expiry_date <= CURDATE() THEN 'Kadaluwarsa'
                    WHEN DATEDIFF(ms.expiry_date, CURDATE()) <= 30 THEN 'Segera Kadaluwarsa'
                    WHEN DATEDIFF(ms.expiry_date, CURDATE()) <= 90 THEN 'Mendekati Kadaluwarsa'
                    ELSE 'Aman'
                END AS expiry_status
            FROM `medicine_stocks` ms
            JOIN `medicines` m ON ms.medicine_id = m.id
            WHERE ms.status = 'available' 
              AND ms.quantity > 0
              AND DATEDIFF(ms.expiry_date, CURDATE()) <= 90
            ORDER BY ms.expiry_date ASC;
        ");

        DB::statement("
            CREATE OR REPLACE VIEW `v_daily_sales_summary` AS
            SELECT 
                DATE(t.transaction_date) AS sale_date,
                COUNT(t.id) AS total_transactions,
                SUM(t.grand_total) AS total_revenue,
                SUM(td.quantity) AS total_items_sold
            FROM `transactions` t
            LEFT JOIN `transaction_details` td ON t.id = td.transaction_id
            WHERE t.status = 'completed'
            GROUP BY DATE(t.transaction_date)
            ORDER BY sale_date DESC;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop views
        DB::statement("DROP VIEW IF EXISTS `v_daily_sales_summary`");
        DB::statement("DROP VIEW IF EXISTS `v_expiring_medicines`");
        DB::statement("DROP VIEW IF EXISTS `v_medicine_stock_summary`");

        Schema::dropIfExists('stock_return_details');
        Schema::dropIfExists('stock_returns');
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('purchase_order_details');
        Schema::dropIfExists('medicine_stocks');
        Schema::dropIfExists('purchase_orders');
    }
};
