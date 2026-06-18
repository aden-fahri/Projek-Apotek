# 📋 PRD — Product Requirements Document
## Aplikasi Web Apotek (Projek-Apotek)

| Informasi | Detail |
|-----------|--------|
| **Nama Proyek** | Projek-Apotek |
| **Versi PRD** | 1.0 |
| **Tanggal** | 18 Juni 2026 |
| **Tech Stack** | PHP 8.2, Laravel 12, MySQL, Vite |
| **Kolaborasi** | GitHub (branching strategy — lihat `agent.md`) |
| **Status** | 🟡 Dalam Pengembangan |

---

## 1. Ringkasan Proyek

Aplikasi web berbasis Laravel untuk **manajemen apotek** yang mencakup: pengelolaan data obat, stok & inventaris, pembelian dari supplier, transaksi penjualan (POS kasir), return obat, dan pelaporan keuangan dengan export Excel.

### Tujuan
- Mempermudah operasional harian apotek
- Mencatat seluruh transaksi masuk (pembelian) dan keluar (penjualan)
- Memantau stok obat, kadaluwarsa, dan stok minimum
- Menghasilkan laporan keuangan yang akurat dan bisa di-export ke Excel

---

## 2. Pengguna & Hak Akses

| Role | Deskripsi | Akses |
|------|-----------|-------|
| **Admin** | Pemilik/Manager apotek | Semua fitur |
| **Kasir** | Petugas kasir | Dashboard, Stok Obat (lihat), Transaksi, Riwayat Transaksi, Notifikasi Kadaluwarsa |

### Matriks Akses Fitur

| Fitur | Admin | Kasir |
|-------|:-----:|:-----:|
| Login | ✅ | ✅ |
| Dashboard | ✅ | ✅ |
| Buat Akun | ✅ | ❌ |
| Manajemen Karyawan | ✅ | ❌ |
| Data Suplayer | ✅ | ❌ |
| Data Kategori Obat | ✅ | ❌ |
| Data Golongan Obat | ✅ | ❌ |
| Data Satuan Obat | ✅ | ❌ |
| Data Obat | ✅ | ❌ |
| Lihat Stok Obat | ✅ | ✅ |
| Tambah Stok (Purchase Order) | ✅ | ❌ |
| Notifikasi Kadaluwarsa | ✅ | ✅ |
| Notifikasi Stok Minimum | ✅ | ✅ |
| Return Obat | ✅ | ❌ |
| Sistem Kasir (POS) | ❌ | ✅ |
| Riwayat Transaksi | ✅ | ✅ |
| Laporan Masuk | ✅ | ❌ |
| Laporan Keluar | ✅ | ❌ |
| Laporan Laba | ✅ | ❌ |
| Export Excel | ✅ | ❌ |
| Pengaturan Apotek | ✅ | ❌ |

---

## 3. Fitur & Spesifikasi Detail

### 3.1 Autentikasi & Pengguna

#### F-01: Login
- Form login: nama + password
- Redirect berdasarkan role (Admin → Dashboard Admin, Kasir → Dashboard Kasir)
- Session-based authentication (Laravel default)
- Tombol "Ingat Saya" (remember me)

#### F-02: Buat Akun (Khusus Admin)
- Admin dapat membuat akun baru untuk karyawan
- Field: nama, email, password, role (admin/kasir), telepon, alamat
- Validasi email unik
- Password minimal 8 karakter

#### F-03: Manajemen Karyawan (Khusus Admin)
- CRUD data karyawan (tabel `users`)
- Aktifkan / nonaktifkan akun karyawan
- Tidak bisa menghapus akun sendiri
- Filter berdasarkan role dan status aktif

---

### 3.2 Dashboard

#### F-04: Dashboard Ringkasan
**Admin melihat:**
- Total obat terdaftar
- Total stok obat (semua item)
- Jumlah obat mendekati kadaluwarsa (≤ 90 hari)
- Jumlah obat stok rendah (≤ min_stock)
- Total penjualan hari ini
- Total pembelian bulan ini
- Grafik penjualan 7 hari terakhir
- Obat terlaris (top 5)

**Kasir melihat:**
- Total penjualan hari ini (miliknya)
- Jumlah transaksi hari ini
- Obat mendekati kadaluwarsa (alert)
- Obat stok rendah (alert)

---

### 3.3 Master Data

#### F-05: Data Suplayer
- CRUD data suplayer/pemasok
- Field: nama, kontak person, telepon, email, alamat, kota
- Aktifkan / nonaktifkan suplayer
- Pencarian dan filter

#### F-06: Data Kategori Obat
- CRUD kategori obat
- Field: nama kategori, deskripsi
- Contoh: Antibiotik, Analgesik, Vitamin, Antasida, dll.
- Nama kategori harus unik

#### F-07: Data Golongan Obat
- CRUD golongan obat
- Field: nama golongan, kode, deskripsi
- Contoh: Obat Bebas (OB), Obat Bebas Terbatas (OBT), Obat Keras (OK), Narkotika (N)
- Nama golongan harus unik

#### F-08: Data Satuan Obat
- CRUD satuan obat
- Field: nama satuan, singkatan
- Contoh: Tablet (Tab), Kapsul (Kap), Botol (Btl), Tube (Tb), Strip (Str)
- Nama satuan harus unik

#### F-09: Data Obat
- CRUD data master obat
- Field: kode obat (unik), nama obat, kategori, golongan, satuan, harga beli, harga jual, stok minimum, deskripsi, butuh resep (ya/tidak)
- Kode obat auto-generate atau manual
- Pencarian berdasarkan nama, kode, kategori, golongan
- Filter berdasarkan kategori, golongan, status aktif

---

### 3.4 Inventaris & Stok

#### F-10: Pembelian dari Supplier (Purchase Order)
- Buat transaksi pembelian obat dari suplayer
- Header: nomor invoice, suplayer, tanggal pembelian, catatan
- Detail: obat, jumlah, harga beli, nomor batch, tanggal kadaluwarsa
- Stok otomatis bertambah setelah pembelian disimpan
- Status: pending, selesai, dibatalkan
- Nomor invoice auto-generate: `PO-YYYYMMDD-XXX`

#### F-11: Lihat Stok Obat
- Tabel semua obat dengan total stok, harga beli, harga jual
- Indikator status: Tersedia (hijau), Stok Rendah (kuning), Habis (merah)
- Detail stok per batch (nomor batch, jumlah, kadaluwarsa)
- Pencarian dan filter

#### F-12: Notifikasi Kadaluwarsa
- Alert obat yang kadaluwarsa ≤ 30 hari (warna merah)
- Alert obat yang kadaluwarsa ≤ 90 hari (warna kuning)
- Tampil di dashboard dan halaman stok
- Badge/counter di sidebar menu

#### F-13: Notifikasi Stok Minimum
- Alert obat yang stoknya ≤ min_stock
- Tampil di dashboard
- Badge/counter di sidebar menu

#### F-14: Return Obat / Pengembalian
- Buat return obat ke suplayer
- Header: nomor return, suplayer, tanggal, alasan (kadaluwarsa/rusak/salah kirim/lainnya), catatan
- Detail: obat, batch, jumlah, harga beli
- Stok otomatis berkurang setelah return disimpan
- Status: pending, selesai, ditolak
- Nomor return auto-generate: `RTN-YYYYMMDD-XXX`

---

### 3.5 Transaksi (Point of Sale)

#### F-15: Sistem Kasir (POS)
- Interface khusus kasir yang cepat dan responsif
- Pencarian obat cepat (by nama/kode)
- Tambah obat ke keranjang
- Ubah jumlah item di keranjang
- Hapus item dari keranjang
- Tampilkan total otomatis
- Input jumlah bayar, hitung kembalian otomatis
- Pilih metode pembayaran (Tunai, QRIS, Transfer)
- Simpan transaksi → stok otomatis berkurang (FIFO berdasarkan kadaluwarsa terdekat)
- Nomor invoice auto-generate: `INV-YYYYMMDD-XXX`
- Validasi: stok harus cukup, obat resep ditandai peringatan

#### F-16: Riwayat Transaksi
- Daftar semua transaksi yang sudah dilakukan
- Filter: tanggal, kasir, metode pembayaran, status
- Detail transaksi: item, jumlah, harga, total
- Admin bisa melihat semua transaksi
- Kasir hanya melihat transaksinya sendiri
- Admin bisa membatalkan transaksi (status → cancelled, stok dikembalikan)

---

### 3.6 Laporan & Export Excel

#### F-17: Laporan Masuk (Pembelian)
- Laporan semua pembelian dari suplayer
- Filter: periode tanggal, suplayer
- Tampilkan: tanggal, nomor invoice, suplayer, total pembelian
- Summary: total pembelian dalam periode
- **Export ke Excel (.xlsx)**

#### F-18: Laporan Keluar (Penjualan)
- Laporan semua penjualan/transaksi
- Filter: periode tanggal, kasir, metode pembayaran
- Tampilkan: tanggal, nomor invoice, kasir, total penjualan
- Summary: total penjualan dalam periode
- **Export ke Excel (.xlsx)**

#### F-19: Laporan Laba (Kotor & Bersih)
- **Laba Kotor** = Total Penjualan − Total HPP (Harga Beli)
- **Laba Bersih** = Laba Kotor − Total Return
- Filter: periode tanggal
- Tampilkan breakdown per obat: nama obat, jumlah terjual, harga jual, HPP, laba
- Summary: total laba kotor dan bersih
- **Export ke Excel (.xlsx)**

---

### 3.7 Fitur Pendukung

#### F-20: Pengaturan Apotek (Khusus Admin)
- Nama apotek
- Alamat, telepon, email
- Logo apotek (upload gambar)
- Nomor izin apotek (SIA)
- Nama apoteker penanggung jawab
- Nomor SIPA apoteker
- Catatan kaki (untuk laporan)

#### F-21: Activity Log (Audit Trail)
- Catat semua aktivitas: login, logout, CRUD data
- Informasi: siapa, kapan, apa yang dilakukan, modul, detail perubahan
- Hanya Admin yang bisa melihat log
- Filter: pengguna, modul, tanggal, jenis aksi

---

## 4. Database Schema

### Daftar Tabel (18 tabel)

| No | Tabel | Tipe | Deskripsi |
|----|-------|------|-----------|
| 1 | `users` | Aplikasi | Data pengguna (Admin & Kasir) |
| 2 | `suppliers` | Aplikasi | Data suplayer/pemasok |
| 3 | `categories` | Aplikasi | Kategori obat |
| 4 | `medicine_groups` | Aplikasi | Golongan obat |
| 5 | `units` | Aplikasi | Satuan obat |
| 6 | `medicines` | Aplikasi | Data master obat |
| 7 | `medicine_stocks` | Aplikasi | Stok obat per batch |
| 8 | `purchase_orders` | Aplikasi | Header pembelian |
| 9 | `purchase_order_details` | Aplikasi | Detail pembelian |
| 10 | `transactions` | Aplikasi | Header transaksi penjualan |
| 11 | `transaction_details` | Aplikasi | Detail transaksi penjualan |
| 12 | `stock_returns` | Aplikasi | Header return obat |
| 13 | `stock_return_details` | Aplikasi | Detail return obat |
| 14 | `pharmacy_settings` | Aplikasi | Pengaturan apotek |
| 15 | `activity_logs` | Aplikasi | Log aktivitas |
| 16 | `sessions` | Laravel | Sesi login |
| 17 | `cache` | Laravel | Cache |
| 18 | `cache_locks` | Laravel | Cache locks |

> **File SQL lengkap:** `database/apotek_db.sql`

---

## 5. UI/UX Guidelines

### Warna
| Token | Warna | Hex | Penggunaan |
|-------|-------|-----|------------|
| Primary | Teal | `#0D9488` | Tombol utama, sidebar aktif, link, aksen |
| Primary Dark | Teal Dark | `#0F766E` | Hover state, header |
| Primary Light | Teal Light | `#14B8A6` | Badge, highlight |
| Background | Warm Sand | `#F5F0E8` | Background halaman utama |
| Surface | White Sand | `#FAF8F4` | Card, panel, tabel |
| Text Primary | Dark | `#1E293B` | Teks utama |
| Text Secondary | Gray | `#64748B` | Teks sekunder, label |
| Success | Green | `#22C55E` | Status tersedia, berhasil |
| Warning | Amber | `#F59E0B` | Stok rendah, mendekati kadaluwarsa |
| Danger | Red | `#EF4444` | Habis, kadaluwarsa, error |

### Font
- **Font Family:** `Quicksand` (Google Fonts)
- **Heading:** Quicksand Bold (700)
- **Body:** Quicksand Regular (400) / Medium (500)
- **Size Scale:** 12px, 14px, 16px, 20px, 24px, 32px

### Layout
- **Sidebar:** Fixed di kiri, lebar 260px, background Teal Dark
- **Navbar:** Fixed di atas, lebar penuh (minus sidebar), background White
- **Content:** Padding 24px, max-width sesuai container
- **Template:** Custom dari nol (tanpa AdminLTE atau template lain)

### Bahasa
- **Full Bahasa Indonesia** untuk seluruh UI
- **Pengecualian:** Nama obat, istilah medis/farmasi, dan istilah teknis yang perlu nama aslinya

---

## 6. Tech Stack & Dependencies

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.2, Laravel 12 |
| Database | MySQL (via XAMPP) |
| Frontend | Blade Template, Vanilla CSS, JavaScript |
| Build Tool | Vite |
| Font | Google Fonts (Quicksand) |
| Excel Export | `maatwebsite/excel` |
| Version Control | Git + GitHub |

---

## 7. Non-Functional Requirements

| Kategori | Requirement |
|----------|-------------|
| **Performa** | Halaman load < 2 detik |
| **Responsif** | Minimal mendukung desktop (1280px+) |
| **Keamanan** | Password bcrypt, CSRF protection, middleware role |
| **Browser** | Chrome, Firefox, Edge (versi terbaru) |
| **Backup** | File `.sql` tersedia untuk import manual |

---

## 8. Tahapan Pengembangan

| Fase | Fokus | Estimasi |
|------|-------|----------|
| **Fase 1** | Fondasi & Autentikasi | Minggu 1 |
| **Fase 2** | Master Data (CRUD) | Minggu 1-2 |
| **Fase 3** | Inventaris & Stok | Minggu 2-3 |
| **Fase 4** | Transaksi (POS) | Minggu 3-4 |
| **Fase 5** | Laporan & Export Excel | Minggu 4 |
| **Fase 6** | Dashboard & Fitur Pendukung | Minggu 5 |

> **Panduan kolaborasi GitHub:** Lihat file `agent.md` di root project.
