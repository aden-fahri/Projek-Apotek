-- ============================================================
-- DATABASE: APLIKASI WEB APOTEK
-- Framework: Laravel 12 | PHP 8.2 | MySQL
-- Created: 2026-06-18
-- ============================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS `apotek_db` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `apotek_db`;

-- ============================================================
-- 1. TABEL USERS (Pengguna: Admin & Kasir)
-- ============================================================
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama lengkap pengguna',
    `username` VARCHAR(255) NULL COMMENT 'Username untuk login',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email untuk login',
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL COMMENT 'Password terenkripsi (bcrypt)',
    `role` ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir' COMMENT 'Peran pengguna',
    `phone` VARCHAR(20) NULL COMMENT 'Nomor telepon',
    `address` TEXT NULL COMMENT 'Alamat',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif: 1=Aktif, 0=Nonaktif',
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data pengguna aplikasi (Admin & Kasir)';

-- ============================================================
-- 2. TABEL SUPPLIERS (Data Suplayer/Pemasok)
-- ============================================================
CREATE TABLE `suppliers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama suplayer/pemasok',
    `contact_person` VARCHAR(255) NULL COMMENT 'Nama kontak person',
    `phone` VARCHAR(20) NULL COMMENT 'Nomor telepon',
    `email` VARCHAR(255) NULL COMMENT 'Email suplayer',
    `address` TEXT NULL COMMENT 'Alamat lengkap',
    `city` VARCHAR(100) NULL COMMENT 'Kota',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data suplayer/pemasok obat';

-- ============================================================
-- 3. TABEL CATEGORIES (Kategori Obat)
-- ============================================================
CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama kategori (Antibiotik, Vitamin, dll)',
    `description` TEXT NULL COMMENT 'Deskripsi kategori',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kategori obat (Antibiotik, Analgesik, Vitamin, dll)';

-- ============================================================
-- 4. TABEL MEDICINE_GROUPS (Golongan Obat)
-- ============================================================
CREATE TABLE `medicine_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama golongan (Obat Bebas, Obat Keras, dll)',
    `code` VARCHAR(10) NULL COMMENT 'Kode golongan (OB, OBT, OK, dll)',
    `description` TEXT NULL COMMENT 'Deskripsi golongan',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `medicine_groups_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Golongan obat (Obat Bebas, Obat Bebas Terbatas, Obat Keras, Narkotika)';

-- ============================================================
-- 5. TABEL UNITS (Satuan Obat)
-- ============================================================
CREATE TABLE `units` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL COMMENT 'Nama satuan (Tablet, Kapsul, dll)',
    `abbreviation` VARCHAR(20) NULL COMMENT 'Singkatan (Tab, Kap, Btl, dll)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `units_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Satuan obat (Tablet, Kapsul, Botol, Tube, Strip, Box)';

-- ============================================================
-- 6. TABEL MEDICINES (Data Master Obat)
-- ============================================================
CREATE TABLE `medicines` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL COMMENT 'Kode obat (auto-generate atau manual)',
    `name` VARCHAR(255) NOT NULL COMMENT 'Nama obat',
    `category_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel categories',
    `group_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel medicine_groups',
    `unit_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel units',
    `purchase_price` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Harga beli (HPP)',
    `selling_price` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Harga jual',
    `min_stock` INT NOT NULL DEFAULT 10 COMMENT 'Stok minimum (reorder level)',
    `description` TEXT NULL COMMENT 'Deskripsi/keterangan obat',
    `requires_prescription` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Butuh resep dokter: 1=Ya, 0=Tidak',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `medicines_code_unique` (`code`),
    KEY `medicines_category_id_index` (`category_id`),
    KEY `medicines_group_id_index` (`group_id`),
    KEY `medicines_unit_id_index` (`unit_id`),
    CONSTRAINT `medicines_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `medicines_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `medicine_groups` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `medicines_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data master obat';

-- ============================================================
-- 7. TABEL MEDICINE_STOCKS (Stok Obat per Batch)
-- ============================================================
CREATE TABLE `medicine_stocks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `medicine_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel medicines',
    `purchase_order_id` BIGINT UNSIGNED NULL COMMENT 'FK ke tabel purchase_orders (bisa NULL jika stok awal)',
    `batch_number` VARCHAR(100) NULL COMMENT 'Nomor batch dari produsen',
    `quantity` INT NOT NULL DEFAULT 0 COMMENT 'Jumlah stok saat ini',
    `initial_quantity` INT NOT NULL DEFAULT 0 COMMENT 'Jumlah stok awal saat masuk',
    `expiry_date` DATE NOT NULL COMMENT 'Tanggal kadaluwarsa',
    `status` ENUM('available', 'expired', 'returned') NOT NULL DEFAULT 'available' COMMENT 'Status stok',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `medicine_stocks_medicine_id_index` (`medicine_id`),
    KEY `medicine_stocks_purchase_order_id_index` (`purchase_order_id`),
    KEY `medicine_stocks_expiry_date_index` (`expiry_date`),
    KEY `medicine_stocks_status_index` (`status`),
    CONSTRAINT `medicine_stocks_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
    -- FK ke purchase_orders ditambahkan setelah tabel purchase_orders dibuat
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stok obat per batch dengan tanggal kadaluwarsa';

-- ============================================================
-- 8. TABEL PURCHASE_ORDERS (Pembelian/Pengadaan Obat dari Supplier)
-- ============================================================
CREATE TABLE `purchase_orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_number` VARCHAR(50) NOT NULL COMMENT 'Nomor invoice/faktur pembelian',
    `supplier_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel suppliers',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel users (yang membuat PO)',
    `order_date` DATE NOT NULL COMMENT 'Tanggal pembelian',
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total nilai pembelian',
    `notes` TEXT NULL COMMENT 'Catatan pembelian',
    `status` ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'completed' COMMENT 'Status pembelian',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `purchase_orders_invoice_number_unique` (`invoice_number`),
    KEY `purchase_orders_supplier_id_index` (`supplier_id`),
    KEY `purchase_orders_user_id_index` (`user_id`),
    KEY `purchase_orders_order_date_index` (`order_date`),
    CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `purchase_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data pembelian obat dari supplier';

-- Tambahkan FK medicine_stocks -> purchase_orders
ALTER TABLE `medicine_stocks`
    ADD CONSTRAINT `medicine_stocks_purchase_order_id_foreign` 
    FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) 
    ON UPDATE CASCADE ON DELETE SET NULL;

-- ============================================================
-- 9. TABEL PURCHASE_ORDER_DETAILS (Detail Pembelian)
-- ============================================================
CREATE TABLE `purchase_order_details` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `purchase_order_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel purchase_orders',
    `medicine_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel medicines',
    `quantity` INT NOT NULL COMMENT 'Jumlah yang dibeli',
    `purchase_price` DECIMAL(15,2) NOT NULL COMMENT 'Harga beli per satuan',
    `subtotal` DECIMAL(15,2) NOT NULL COMMENT 'Subtotal (quantity * purchase_price)',
    `batch_number` VARCHAR(100) NULL COMMENT 'Nomor batch',
    `expiry_date` DATE NULL COMMENT 'Tanggal kadaluwarsa',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `po_details_purchase_order_id_index` (`purchase_order_id`),
    KEY `po_details_medicine_id_index` (`medicine_id`),
    CONSTRAINT `po_details_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `po_details_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detail item pembelian obat';

-- ============================================================
-- 10. TABEL TRANSACTIONS (Transaksi Penjualan / POS Kasir)
-- ============================================================
CREATE TABLE `transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_number` VARCHAR(50) NOT NULL COMMENT 'Nomor invoice/struk',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel users (kasir)',
    `transaction_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal & waktu transaksi',
    `total` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total belanja',
    `tax` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Pajak (jika ada)',
    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total yang harus dibayar (total + tax)',
    `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Jumlah uang yang dibayarkan',
    `change_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Jumlah kembalian',
    `payment_method` ENUM('tunai', 'qris', 'transfer') NOT NULL DEFAULT 'tunai' COMMENT 'Metode pembayaran',
    `customer_name` VARCHAR(255) NULL COMMENT 'Nama pelanggan (opsional)',
    `notes` TEXT NULL COMMENT 'Catatan transaksi',
    `status` ENUM('completed', 'cancelled') NOT NULL DEFAULT 'completed' COMMENT 'Status transaksi',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `transactions_invoice_number_unique` (`invoice_number`),
    KEY `transactions_user_id_index` (`user_id`),
    KEY `transactions_transaction_date_index` (`transaction_date`),
    KEY `transactions_status_index` (`status`),
    CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data transaksi penjualan (POS kasir)';

-- ============================================================
-- 11. TABEL TRANSACTION_DETAILS (Detail Transaksi Penjualan)
-- ============================================================
CREATE TABLE `transaction_details` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaction_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel transactions',
    `medicine_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel medicines',
    `medicine_stock_id` BIGINT UNSIGNED NULL COMMENT 'FK ke tabel medicine_stocks (batch mana yang diambil)',
    `quantity` INT NOT NULL COMMENT 'Jumlah yang dijual',
    `price` DECIMAL(15,2) NOT NULL COMMENT 'Harga jual per satuan',
    `subtotal` DECIMAL(15,2) NOT NULL COMMENT 'Subtotal (quantity * price)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `transaction_details_transaction_id_index` (`transaction_id`),
    KEY `transaction_details_medicine_id_index` (`medicine_id`),
    KEY `transaction_details_medicine_stock_id_index` (`medicine_stock_id`),
    CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `transaction_details_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `transaction_details_medicine_stock_id_foreign` FOREIGN KEY (`medicine_stock_id`) REFERENCES `medicine_stocks` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detail item transaksi penjualan';

-- ============================================================
-- 12. TABEL STOCK_RETURNS (Return/Pengembalian Obat ke Supplier)
-- ============================================================
CREATE TABLE `stock_returns` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `return_number` VARCHAR(50) NOT NULL COMMENT 'Nomor return',
    `supplier_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel suppliers',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel users (yang membuat return)',
    `return_date` DATE NOT NULL COMMENT 'Tanggal return',
    `reason` ENUM('expired', 'damaged', 'wrong_item', 'other') NOT NULL DEFAULT 'expired' COMMENT 'Alasan return',
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Total nilai return',
    `notes` TEXT NULL COMMENT 'Catatan return',
    `status` ENUM('pending', 'completed', 'rejected') NOT NULL DEFAULT 'pending' COMMENT 'Status return',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `stock_returns_return_number_unique` (`return_number`),
    KEY `stock_returns_supplier_id_index` (`supplier_id`),
    KEY `stock_returns_user_id_index` (`user_id`),
    KEY `stock_returns_return_date_index` (`return_date`),
    CONSTRAINT `stock_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `stock_returns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data return/pengembalian obat ke supplier';

-- ============================================================
-- 13. TABEL STOCK_RETURN_DETAILS (Detail Return Obat)
-- ============================================================
CREATE TABLE `stock_return_details` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `stock_return_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel stock_returns',
    `medicine_id` BIGINT UNSIGNED NOT NULL COMMENT 'FK ke tabel medicines',
    `medicine_stock_id` BIGINT UNSIGNED NULL COMMENT 'FK ke tabel medicine_stocks (batch mana yang di-return)',
    `quantity` INT NOT NULL COMMENT 'Jumlah yang di-return',
    `purchase_price` DECIMAL(15,2) NOT NULL COMMENT 'Harga beli per satuan (nilai return)',
    `subtotal` DECIMAL(15,2) NOT NULL COMMENT 'Subtotal (quantity * purchase_price)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `stock_return_details_stock_return_id_index` (`stock_return_id`),
    KEY `stock_return_details_medicine_id_index` (`medicine_id`),
    KEY `stock_return_details_medicine_stock_id_index` (`medicine_stock_id`),
    CONSTRAINT `stock_return_details_stock_return_id_foreign` FOREIGN KEY (`stock_return_id`) REFERENCES `stock_returns` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `stock_return_details_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `stock_return_details_medicine_stock_id_foreign` FOREIGN KEY (`medicine_stock_id`) REFERENCES `medicine_stocks` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detail item return obat';

-- ============================================================
-- 14. TABEL PHARMACY_SETTINGS (Pengaturan Apotek)
-- ============================================================
CREATE TABLE `pharmacy_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pharmacy_name` VARCHAR(255) NOT NULL DEFAULT 'Apotek Sehat' COMMENT 'Nama apotek',
    `address` TEXT NULL COMMENT 'Alamat apotek',
    `phone` VARCHAR(20) NULL COMMENT 'Nomor telepon apotek',
    `email` VARCHAR(255) NULL COMMENT 'Email apotek',
    `logo` VARCHAR(255) NULL COMMENT 'Path file logo',
    `license_number` VARCHAR(100) NULL COMMENT 'Nomor izin apotek (SIA)',
    `pharmacist_name` VARCHAR(255) NULL COMMENT 'Nama apoteker penanggung jawab',
    `pharmacist_license` VARCHAR(100) NULL COMMENT 'Nomor SIPA apoteker',
    `footer_note` TEXT NULL COMMENT 'Catatan kaki untuk struk',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pengaturan informasi apotek (untuk struk & laporan)';

-- ============================================================
-- 15. TABEL ACTIVITY_LOGS (Log Aktivitas / Audit Trail)
-- ============================================================
CREATE TABLE `activity_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL COMMENT 'FK ke tabel users (pelaku)',
    `activity` VARCHAR(255) NOT NULL COMMENT 'Deskripsi aktivitas',
    `module` VARCHAR(100) NULL COMMENT 'Modul yang diakses (medicines, transactions, dll)',
    `action` ENUM('create', 'update', 'delete', 'login', 'logout', 'other') NOT NULL COMMENT 'Jenis aksi',
    `detail` JSON NULL COMMENT 'Detail perubahan dalam format JSON',
    `ip_address` VARCHAR(45) NULL COMMENT 'IP address pengguna',
    `user_agent` TEXT NULL COMMENT 'Browser/device info',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `activity_logs_user_id_index` (`user_id`),
    KEY `activity_logs_module_index` (`module`),
    KEY `activity_logs_created_at_index` (`created_at`),
    CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log aktivitas pengguna untuk audit trail';

-- ============================================================
-- 16. TABEL SESSIONS (Laravel Default)
-- ============================================================
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesi login Laravel';

-- ============================================================
-- 17. TABEL CACHE (Laravel Default)
-- ============================================================
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cache Laravel';

-- ============================================================
-- 18. TABEL CACHE_LOCKS (Laravel Default)
-- ============================================================
CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cache locks Laravel';


-- ============================================================
-- ╔══════════════════════════════════════════════════════════╗
-- ║                   DATA SEEDER (SAMPLE)                  ║
-- ╚══════════════════════════════════════════════════════════╝
-- ============================================================

-- ============================================================
-- SEED: Users (Password: "password" -> bcrypt hash)
-- ============================================================
INSERT INTO `users` (`name`, `username`, `email`, `password`, `role`, `phone`, `is_active`) VALUES
('Administrator', 'admin', 'admin@apotek.com', '$2y$12$YJwfjKl3XkF5MxP0T8Qj0eKlXb3F3kx6GxFz7qR5VhN8wA2dS4.mO', 'admin', '081234567890', 1),
('Siti Kasir', 'kasir', 'kasir@apotek.com', '$2y$12$YJwfjKl3XkF5MxP0T8Qj0eKlXb3F3kx6GxFz7qR5VhN8wA2dS4.mO', 'kasir', '081234567891', 1);

-- ============================================================
-- SEED: Suppliers (Suplayer)
-- ============================================================
INSERT INTO `suppliers` (`name`, `contact_person`, `phone`, `email`, `address`, `city`) VALUES
('PT. Kimia Farma', 'Budi Santoso', '021-4287777', 'order@kimiafarma.co.id', 'Jl. Veteran No.9, Jakarta Pusat', 'Jakarta'),
('PT. Kalbe Farma', 'Dewi Lestari', '021-4242808', 'order@kalbefarma.co.id', 'Jl. Let. Jend. Suprapto Kav.4, Jakarta', 'Jakarta'),
('PT. Sanbe Farma', 'Ahmad Hidayat', '022-7312222', 'sales@sanbe.co.id', 'Jl. Tamansari No.10, Bandung', 'Bandung'),
('PT. Dexa Medica', 'Rina Wulandari', '0361-720909', 'order@dexa-medica.com', 'Jl. Palembang No.25, Palembang', 'Palembang'),
('PT. Phapros', 'Eko Prasetyo', '024-7614041', 'sales@phapros.co.id', 'Jl. Simongan No.131, Semarang', 'Semarang');

-- ============================================================
-- SEED: Categories (Kategori Obat)
-- ============================================================
INSERT INTO `categories` (`name`, `description`) VALUES
('Antibiotik', 'Obat untuk mengatasi infeksi bakteri'),
('Analgesik & Antipiretik', 'Obat pereda nyeri dan penurun demam'),
('Vitamin & Suplemen', 'Suplemen kesehatan dan vitamin'),
('Antasida & Antiulkus', 'Obat untuk gangguan lambung dan asam lambung'),
('Antialergi & Antihistamin', 'Obat untuk mengatasi alergi'),
('Obat Batuk & Flu', 'Obat untuk batuk, pilek, dan flu'),
('Antiseptik & Desinfektan', 'Obat luar untuk antiseptik dan desinfektan'),
('Obat Mata & Telinga', 'Obat tetes mata dan telinga'),
('Obat Kulit', 'Obat untuk penyakit dan keluhan kulit'),
('Obat Kardiovaskular', 'Obat untuk jantung dan pembuluh darah');

-- ============================================================
-- SEED: Medicine Groups (Golongan Obat)
-- ============================================================
INSERT INTO `medicine_groups` (`name`, `code`, `description`) VALUES
('Obat Bebas', 'OB', 'Obat yang dapat dibeli tanpa resep dokter. Ditandai lingkaran hijau dengan garis tepi hitam.'),
('Obat Bebas Terbatas', 'OBT', 'Obat yang dapat dibeli tanpa resep tetapi dengan peringatan. Ditandai lingkaran biru dengan garis tepi hitam.'),
('Obat Keras', 'OK', 'Obat yang hanya dapat diperoleh dengan resep dokter. Ditandai lingkaran merah dengan huruf K.'),
('Narkotika', 'N', 'Obat yang mengandung zat narkotika. Memerlukan resep dokter dan pengawasan ketat. Ditandai palang (+) merah.'),
('Obat Herbal Terstandar', 'OHT', 'Obat herbal yang telah distandarisasi dan diuji praklinis.');

-- ============================================================
-- SEED: Units (Satuan Obat)
-- ============================================================
INSERT INTO `units` (`name`, `abbreviation`) VALUES
('Tablet', 'Tab'),
('Kapsul', 'Kap'),
('Kaplet', 'Kpl'),
('Botol', 'Btl'),
('Tube', 'Tb'),
('Sachet', 'Sct'),
('Strip', 'Str'),
('Box', 'Box'),
('Ampul', 'Amp'),
('Vial', 'Vl'),
('Salep', 'Slp'),
('Sirup', 'Srp'),
('Suppositoria', 'Sup'),
('Patch', 'Ptc');

-- ============================================================
-- SEED: Medicines (Data Obat)
-- ============================================================
INSERT INTO `medicines` (`code`, `name`, `category_id`, `group_id`, `unit_id`, `purchase_price`, `selling_price`, `min_stock`, `requires_prescription`) VALUES
('OBT-001', 'Paracetamol 500mg', 2, 1, 1, 250.00, 500.00, 100, 0),
('OBT-002', 'Amoxicillin 500mg', 1, 3, 2, 800.00, 1500.00, 50, 1),
('OBT-003', 'Vitamin C 1000mg', 3, 1, 1, 500.00, 1000.00, 80, 0),
('OBT-004', 'Antasida DOEN', 4, 1, 1, 200.00, 400.00, 60, 0),
('OBT-005', 'Cetirizine 10mg', 5, 2, 1, 350.00, 700.00, 40, 0),
('OBT-006', 'Ambroxol Sirup 60ml', 6, 2, 4, 8000.00, 15000.00, 30, 0),
('OBT-007', 'Betadine 30ml', 7, 1, 4, 12000.00, 22000.00, 25, 0),
('OBT-008', 'Cendo Xitrol Tetes Mata', 8, 3, 4, 25000.00, 45000.00, 15, 1),
('OBT-009', 'Hydrocortisone Cream 2.5%', 9, 3, 5, 10000.00, 18000.00, 20, 1),
('OBT-010', 'Amlodipine 5mg', 10, 3, 1, 600.00, 1200.00, 30, 1),
('OBT-011', 'Ibuprofen 400mg', 2, 2, 1, 400.00, 800.00, 50, 0),
('OBT-012', 'Omeprazole 20mg', 4, 3, 2, 1200.00, 2500.00, 30, 1),
('OBT-013', 'Dexamethasone 0.5mg', 5, 3, 1, 300.00, 600.00, 40, 1),
('OBT-014', 'OBH Combi Sirup 100ml', 6, 1, 4, 15000.00, 28000.00, 25, 0),
('OBT-015', 'Salep 2-4 (Antiseptik)', 7, 1, 5, 5000.00, 10000.00, 20, 0);

-- ============================================================
-- SEED: Medicine Stocks (Stok Awal)
-- ============================================================
INSERT INTO `medicine_stocks` (`medicine_id`, `batch_number`, `quantity`, `initial_quantity`, `expiry_date`, `status`) VALUES
(1, 'BATCH-PCM-2026A', 500, 500, '2028-06-30', 'available'),
(2, 'BATCH-AMX-2026A', 200, 200, '2028-03-15', 'available'),
(3, 'BATCH-VTC-2026A', 300, 300, '2027-12-31', 'available'),
(4, 'BATCH-ANT-2026A', 150, 150, '2028-09-30', 'available'),
(5, 'BATCH-CTZ-2026A', 100, 100, '2028-01-15', 'available'),
(6, 'BATCH-ABX-2026A', 80, 80, '2027-06-30', 'available'),
(7, 'BATCH-BTD-2026A', 60, 60, '2028-12-31', 'available'),
(8, 'BATCH-CXT-2026A', 40, 40, '2027-09-30', 'available'),
(9, 'BATCH-HCC-2026A', 50, 50, '2028-06-15', 'available'),
(10, 'BATCH-AML-2026A', 120, 120, '2028-08-31', 'available'),
(11, 'BATCH-IBU-2026A', 200, 200, '2028-04-30', 'available'),
(12, 'BATCH-OMP-2026A', 100, 100, '2027-11-30', 'available'),
(13, 'BATCH-DXM-2026A', 150, 150, '2028-02-28', 'available'),
(14, 'BATCH-OBH-2026A', 70, 70, '2027-10-31', 'available'),
(15, 'BATCH-S24-2026A', 90, 90, '2028-07-31', 'available'),
-- Stok kedua dengan tanggal kadaluwarsa berbeda (mendekati expired untuk testing notifikasi)
(1, 'BATCH-PCM-2025B', 20, 100, '2026-07-15', 'available'),
(3, 'BATCH-VTC-2025B', 10, 50, '2026-07-01', 'available');

-- ============================================================
-- SEED: Pharmacy Settings (Pengaturan Apotek)
-- ============================================================
INSERT INTO `pharmacy_settings` (`pharmacy_name`, `address`, `phone`, `email`, `license_number`, `pharmacist_name`, `pharmacist_license`, `footer_note`) VALUES
('Apotek Sehat Farma', 'Jl. Kesehatan No.123, Kota Bandung, Jawa Barat 40123', '022-1234567', 'info@apoteksehat.com', 'SIA-1234/2024/DINKES', 'Apt. Dr. Farida Rahmawati, S.Farm', 'SIPA-5678/2024', 'Terima kasih telah berbelanja di Apotek Sehat Farma. Semoga lekas sembuh!');

-- ============================================================
-- VIEWS: Query Bantuan untuk Laporan
-- ============================================================

-- View: Stok obat dengan total quantity per obat
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
        ELSE 'Tersedia'
    END AS stock_status
FROM `medicines` m
LEFT JOIN `categories` c ON m.category_id = c.id
LEFT JOIN `medicine_groups` mg ON m.group_id = mg.id
LEFT JOIN `units` u ON m.unit_id = u.id
LEFT JOIN `medicine_stocks` ms ON m.id = ms.medicine_id
WHERE m.is_active = 1
GROUP BY m.id, m.code, m.name, c.name, mg.name, u.name, m.purchase_price, m.selling_price, m.min_stock;

-- View: Obat yang mendekati kadaluwarsa (30 hari ke depan)
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

-- View: Ringkasan transaksi harian
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

-- ============================================================
-- SELESAI
-- ============================================================
-- Total tabel: 18 (15 tabel aplikasi + 3 tabel Laravel system)
-- Total views: 3 (untuk membantu query laporan)
-- ============================================================
