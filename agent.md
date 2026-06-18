# 🤝 Agent.md — Panduan Kolaborasi GitHub
## Projek-Apotek | Tim Development

> **TUJUAN UTAMA:** Mencegah tabrakan kode (merge conflict) antar anggota tim saat kolaborasi di GitHub.

---

## 1. Branch Strategy (Git Flow Sederhana)

### Struktur Branch

```
main                          ← Kode production (JANGAN push langsung!)
  └── develop                 ← Branch utama development (integrasi)
        ├── feature/auth      ← Fitur autentikasi
        ├── feature/master-data
        ├── feature/inventory
        ├── feature/pos
        ├── feature/reports
        └── fix/nama-bug      ← Perbaikan bug
```

### Aturan Branch

| Branch | Fungsi | Siapa yang merge? |
|--------|--------|-------------------|
| `main` | Production-ready code | **Hanya Lead/Admin** via Pull Request |
| `develop` | Integrasi semua fitur | **Semua anggota** via Pull Request |
| `feature/*` | Pengembangan fitur baru | Masing-masing developer |
| `fix/*` | Perbaikan bug | Masing-masing developer |

### ⚠️ ATURAN WAJIB
```
❌ DILARANG push langsung ke `main`
❌ DILARANG push langsung ke `develop`
✅ SELALU buat branch feature/fix dari `develop`
✅ SELALU merge via Pull Request
✅ SELALU pull terbaru dari `develop` sebelum mulai kerja
```

---

## 2. Pembagian Modul per Developer

> **KUNCI ANTI-TABRAKAN:** Setiap developer bertanggung jawab atas modul/file yang BERBEDA. Jangan mengerjakan file yang sama secara bersamaan.

### Contoh Pembagian (2 Developer)

#### 👤 Developer A — Backend & Data
| Modul | Branch | File yang Dikerjakan |
|-------|--------|---------------------|
| Database & Migration | `feature/database` | `database/migrations/*`, `database/seeders/*` |
| Autentikasi | `feature/auth` | `app/Http/Controllers/Auth/*`, `resources/views/auth/*` |
| Master Data | `feature/master-data` | Controllers: `Supplier`, `Category`, `MedicineGroup`, `Unit`, `Medicine` + Views masing-masing |
| Inventaris & Stok | `feature/inventory` | Controllers: `PurchaseOrder`, `MedicineStock`, `StockReturn` + Views masing-masing |

#### 👤 Developer B — Frontend & Transaksi
| Modul | Branch | File yang Dikerjakan |
|-------|--------|---------------------|
| Layout & UI | `feature/layout` | `resources/views/layouts/*`, `resources/css/*`, `resources/js/*` |
| Dashboard | `feature/dashboard` | `DashboardController`, `resources/views/dashboard/*` |
| Transaksi (POS) | `feature/pos` | `TransactionController`, `resources/views/transactions/*` |
| Laporan & Export | `feature/reports` | `ReportController`, `app/Exports/*`, `resources/views/reports/*` |

### Contoh Pembagian (3+ Developer)

#### 👤 Developer A — Fondasi
- Branch: `feature/auth`, `feature/database`, `feature/layout`
- File: Migrations, Seeders, Login, Layout, Middleware

#### 👤 Developer B — Master Data & Inventaris
- Branch: `feature/master-data`, `feature/inventory`
- File: Supplier, Category, MedicineGroup, Unit, Medicine, PurchaseOrder, Stock, Return

#### 👤 Developer C — Transaksi & Laporan
- Branch: `feature/pos`, `feature/reports`, `feature/dashboard`
- File: Transaction (POS), Report, Export Excel, Dashboard

---

## 3. File yang Rawan Tabrakan (HATI-HATI!)

> File-file ini sering diubah oleh banyak orang. **Komunikasikan** sebelum mengubahnya.

| File | Alasan Rawan | Solusi |
|------|-------------|--------|
| `routes/web.php` | Semua fitur menambah route | **Pisahkan route per modul** (lihat Bagian 7) |
| `resources/views/layouts/app.blade.php` | Sidebar & navbar diubah saat fitur baru | **1 orang** yang handle layout |
| `database/migrations/*` | Urutan migration penting | **Koordinasi** sebelum buat migration baru |
| `config/app.php` | Konfigurasi global | Jarang diubah, tapi **hati-hati** |
| `composer.json` | Setiap install package | **Komunikasikan** sebelum install package |

---

## 4. Workflow Harian

### 🌅 Mulai Kerja (Pagi)

```bash
# 1. Pindah ke branch develop & ambil update terbaru
git checkout develop
git pull origin develop

# 2. Pindah ke branch fitur kamu (atau buat baru)
git checkout feature/nama-fitur
# Atau buat baru:
git checkout -b feature/nama-fitur

# 3. Merge update terbaru dari develop ke branch kamu
git merge develop

# 4. Selesaikan conflict (jika ada), lalu mulai coding
```

### 💻 Selama Kerja (Commit Berkala)

```bash
# Commit sesering mungkin (minimal setiap 1 fitur kecil selesai)
git add .
git commit -m "feat(master-data): tambah CRUD kategori obat"

# Push ke GitHub
git push origin feature/nama-fitur
```

### 🌙 Selesai Kerja (Sore/Malam)

```bash
# 1. Commit semua perubahan
git add .
git commit -m "feat(pos): progress halaman kasir - keranjang belanja"

# 2. Push ke GitHub
git push origin feature/nama-fitur

# 3. Jika fitur sudah selesai → buat Pull Request ke develop
# (Lakukan di GitHub website)
```

---

## 5. Konvensi Commit Message

### Format

```
<tipe>(<modul>): <deskripsi singkat>
```

### Tipe Commit

| Tipe | Kapan Dipakai | Contoh |
|------|--------------|--------|
| `feat` | Fitur baru | `feat(auth): tambah halaman login` |
| `fix` | Perbaikan bug | `fix(pos): perbaiki kalkulasi total` |
| `style` | Perubahan tampilan/CSS | `style(layout): update warna sidebar` |
| `refactor` | Refactoring kode | `refactor(medicine): pisahkan logic stok` |
| `docs` | Dokumentasi | `docs: update README.md` |
| `db` | Database/migration | `db: tambah tabel purchase_orders` |
| `chore` | Maintenance | `chore: install package maatwebsite/excel` |

### Contoh Commit Messages yang Baik ✅

```bash
git commit -m "feat(auth): tambah halaman login dengan validasi"
git commit -m "feat(master-data): CRUD data suplayer"
git commit -m "feat(pos): implementasi keranjang belanja kasir"
git commit -m "fix(inventory): perbaiki perhitungan stok setelah return"
git commit -m "style(dashboard): tambah grafik penjualan 7 hari"
git commit -m "db: buat migration tabel medicines dan seeders"
git commit -m "feat(reports): export laporan masuk ke Excel"
```

### Contoh Commit Messages yang Buruk ❌

```bash
git commit -m "update"
git commit -m "fix bug"
git commit -m "perubahan"
git commit -m "asdfg"
git commit -m "WIP"
```

---

## 6. Pull Request (PR) Workflow

### Membuat Pull Request

1. Push branch fitur ke GitHub
2. Buka GitHub → klik **"Compare & pull request"**
3. Isi detail PR:

```markdown
## Deskripsi
- Menambahkan fitur CRUD data suplayer

## Perubahan
- [x] Model Supplier.php
- [x] SupplierController.php (index, create, store, edit, update, destroy)
- [x] Views: suppliers/index, create, edit
- [x] Route di routes/web.php

## Screenshot (jika ada perubahan UI)
[Lampirkan screenshot]

## Checklist
- [ ] Sudah test di local
- [ ] Tidak ada error/warning
- [ ] Sudah merge develop terbaru
```

4. **Target branch:** `develop` (BUKAN `main`)
5. Assign reviewer (anggota tim lain)

### Review & Merge

```
Developer A buat PR → Developer B review → Approve → Merge ke develop
```

| Langkah | Siapa | Apa yang Dilakukan |
|---------|-------|-------------------|
| 1. Buat PR | Developer | Push branch, buat PR ke `develop` |
| 2. Review | Reviewer | Cek kode, beri komentar jika perlu |
| 3. Fix (jika ada) | Developer | Perbaiki sesuai feedback, push lagi |
| 4. Approve | Reviewer | Klik "Approve" |
| 5. Merge | Developer/Lead | Merge PR ke `develop` |
| 6. Cleanup | Developer | Hapus branch fitur yang sudah di-merge |

### Setelah PR di-Merge

```bash
# Semua anggota tim harus pull develop terbaru
git checkout develop
git pull origin develop

# Merge ke branch fitur yang sedang dikerjakan
git checkout feature/fitur-lain
git merge develop
```

---

## 7. Strategi Anti-Tabrakan Route

### ❌ JANGAN: Semua route di 1 file

```php
// routes/web.php → RAWAN CONFLICT!
Route::get('/suppliers', ...);
Route::get('/categories', ...);
Route::get('/medicines', ...);
// ... 50+ route di satu file
```

### ✅ LAKUKAN: Pisahkan route per modul

```php
// routes/web.php — file utama (minimal, jarang diubah)
<?php

use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn() => redirect()->route('login'));

// Load route per modul
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/dashboard.php';
require __DIR__.'/modules/master-data.php';
require __DIR__.'/modules/inventory.php';
require __DIR__.'/modules/transaction.php';
require __DIR__.'/modules/report.php';
require __DIR__.'/modules/setting.php';
```

```php
// routes/modules/master-data.php — Developer B saja yang mengerjakan
<?php

use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
// ...

Route::middleware(['auth', 'admin'])->prefix('master')->group(function () {
    Route::resource('suplayer', SupplierController::class);
    Route::resource('kategori', CategoryController::class);
    Route::resource('golongan', MedicineGroupController::class);
    Route::resource('satuan', UnitController::class);
    Route::resource('obat', MedicineController::class);
});
```

```php
// routes/modules/transaction.php — Developer C saja yang mengerjakan
<?php

use App\Http\Controllers\TransactionController;

Route::middleware(['auth'])->group(function () {
    Route::get('/kasir', [TransactionController::class, 'pos'])->name('kasir');
    Route::post('/kasir/simpan', [TransactionController::class, 'store'])->name('kasir.simpan');
    Route::get('/riwayat-transaksi', [TransactionController::class, 'history'])->name('transaksi.riwayat');
});
```

> **Hasilnya:** Setiap developer hanya mengubah file route miliknya sendiri → **ZERO CONFLICT** di route!

---

## 8. Strategi Anti-Tabrakan Migration

### Aturan Migration

1. **Koordinasi** sebelum membuat migration baru
2. **Prefix timestamp** sudah otomatis dari Laravel → jarang conflict
3. **Jangan edit** migration yang sudah di-push dan di-migrate oleh orang lain
4. Jika perlu mengubah tabel yang sudah ada → buat migration **baru** (ALTER TABLE)

```bash
# ✅ BENAR: Buat migration baru untuk perubahan
php artisan make:migration add_phone_to_suppliers_table

# ❌ SALAH: Edit migration lama yang sudah di-migrate orang lain
# Jangan edit file 2026_06_18_create_suppliers_table.php
```

### Urutan Migration (Fase 1 — Satu Orang)

> **PENTING:** Migration awal (semua tabel) sebaiknya dikerjakan oleh **1 orang saja** untuk menghindari conflict urutan foreign key.

---

## 9. Checklist Sebelum Push / PR

```
✅ Checklist Sebelum Push
├── [ ] Kode berjalan tanpa error di local
├── [ ] Sudah test fitur yang dikerjakan
├── [ ] Tidak mengubah file milik developer lain
├── [ ] Commit message mengikuti konvensi
├── [ ] Sudah merge develop terbaru ke branch fitur
└── [ ] Tidak ada file yang seharusnya di .gitignore
```

```
✅ Checklist Sebelum Pull Request
├── [ ] Semua checklist "Sebelum Push" terpenuhi
├── [ ] Deskripsi PR jelas dan lengkap
├── [ ] Target branch: develop (bukan main)
├── [ ] Assign reviewer
├── [ ] Screenshot UI (jika ada perubahan tampilan)
└── [ ] Tidak ada conflict dengan develop
```

---

## 10. Menangani Merge Conflict

### Kapan Conflict Terjadi?
- Dua orang mengubah **baris yang sama** di **file yang sama**
- Biasanya di: `routes/web.php`, `layout`, `config`

### Cara Menyelesaikan

```bash
# 1. Merge develop ke branch kamu
git checkout feature/fitur-kamu
git merge develop

# 2. Jika ada conflict, buka file yang conflict
# Cari tanda:
# <<<<<<< HEAD
# (kode kamu)
# =======
# (kode dari develop)
# >>>>>>> develop

# 3. Edit file: pilih kode yang benar (atau gabungkan keduanya)
# Hapus tanda <<<<<<< ======= >>>>>>>

# 4. Tandai conflict sudah diselesaikan
git add .
git commit -m "fix: resolve merge conflict di routes/web.php"
git push origin feature/fitur-kamu
```

### Tips Mencegah Conflict

| No | Tips | Detail |
|----|------|--------|
| 1 | **Pull develop SERING** | Minimal 1x sehari di awal kerja |
| 2 | **Commit KECIL & SERING** | Jangan menumpuk banyak perubahan |
| 3 | **Jangan ubah file orang lain** | Sesuai pembagian modul |
| 4 | **Pisahkan route** | Gunakan file route per modul |
| 5 | **Komunikasi** | Chat tim sebelum ubah file bersama |

---

## 11. Perintah Git yang Sering Dipakai

```bash
# === SETUP AWAL (1x saja) ===
git clone <url-repo>                     # Clone repo
git checkout -b develop                  # Buat branch develop
git push -u origin develop              # Push develop ke GitHub

# === SEHARI-HARI ===
git checkout develop                    # Pindah ke develop
git pull origin develop                 # Ambil update terbaru
git checkout -b feature/nama-fitur      # Buat branch fitur baru
git add .                               # Stage semua perubahan
git commit -m "feat(modul): deskripsi"  # Commit
git push origin feature/nama-fitur      # Push ke GitHub

# === MERGE & UPDATE ===
git merge develop                       # Merge develop ke branch saat ini
git checkout develop                    # Pindah ke develop
git pull origin develop                 # Pull terbaru setelah PR di-merge

# === CEK STATUS ===
git status                              # Lihat perubahan
git log --oneline -10                   # Lihat 10 commit terakhir
git branch                              # Lihat semua branch local
git branch -a                           # Lihat semua branch (termasuk remote)

# === CLEANUP ===
git branch -d feature/nama-fitur        # Hapus branch local setelah merge
git push origin --delete feature/nama-fitur  # Hapus branch remote
```

---

## 12. Diagram Alur Kerja

```
┌─────────────┐    ┌──────────────┐    ┌──────────────┐
│  Developer   │    │   GitHub     │    │  Developer   │
│      A       │    │   (Remote)   │    │      B       │
└──────┬───────┘    └──────┬───────┘    └──────┬───────┘
       │                   │                   │
       │  git pull develop │                   │
       │◄──────────────────│                   │
       │                   │  git pull develop │
       │                   │──────────────────►│
       │                   │                   │
       │  checkout         │                   │ checkout
       │  feature/auth     │                   │ feature/pos
       │                   │                   │
       │  ... coding ...   │                   │ ... coding ...
       │                   │                   │
       │  push feature/auth│                   │
       │──────────────────►│                   │
       │                   │                   │ push feature/pos
       │  buat PR ─────────│                   │──────────────────►
       │                   │                   │
       │                   │◄── review PR ─────│
       │                   │                   │
       │                   │── approve ───────►│
       │                   │                   │
       │  merge ke develop │                   │
       │──────────────────►│                   │
       │                   │                   │
       │                   │  notif: pull      │
       │                   │  develop terbaru  │
       │                   │──────────────────►│
       │                   │                   │
       │                   │  git pull develop │
       │                   │──────────────────►│
       │                   │                   │
       │                   │  merge develop    │
       │                   │  ke feature/pos   │
       │                   │──────────────────►│
       │                   │                   │
```

---

## Quick Reference Card

```
╔══════════════════════════════════════════════════════╗
║              QUICK REFERENCE - GIT WORKFLOW          ║
╠══════════════════════════════════════════════════════╣
║                                                      ║
║  🟢 MULAI KERJA:                                    ║
║     git checkout develop                             ║
║     git pull origin develop                          ║
║     git checkout -b feature/nama-fitur               ║
║                                                      ║
║  🔵 SELAMA KERJA:                                   ║
║     git add .                                        ║
║     git commit -m "feat(modul): deskripsi"           ║
║     git push origin feature/nama-fitur               ║
║                                                      ║
║  🟡 SELESAI FITUR:                                  ║
║     Push → Buat PR di GitHub → Minta review          ║
║                                                      ║
║  🔴 JANGAN LUPA:                                    ║
║     ❌ Jangan push ke main / develop langsung        ║
║     ✅ Selalu pull develop terbaru                   ║
║     ✅ Selalu buat PR untuk merge                    ║
║     ✅ Komunikasi sebelum ubah file bersama          ║
║                                                      ║
╚══════════════════════════════════════════════════════╝
```
