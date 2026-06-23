<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Route Modul: Master Data
| F-05: Data Suplayer
| F-06: Data Kategori Obat
| F-07: Data Golongan Obat
| F-08: Data Satuan Obat
| F-09: Data Obat
| Akses: Admin only
| Dikerjakan oleh: Developer B (feature/master-data)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\InventoryController;

Route::middleware(['auth', 'role:admin'])->group(function () {

    // F-05: Suplayer
    Route::get('/supplier', function () {
        return redirect()->route('suppliers.index');
    })->name('supplier');
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

    // F-06: Kategori Obat
    Route::get('/kategori-obat',    fn() => redirect()->route('dashboard.admin'))->name('kategori-obat');

    // F-07: Golongan Obat
    Route::get('/golongan-obat',    fn() => redirect()->route('dashboard.admin'))->name('golongan-obat');

    // F-08: Satuan Obat
    Route::get('/satuan-obat',      fn() => redirect()->route('dashboard.admin'))->name('satuan-obat');

    // F-09: Data Obat
    Route::get('/data-obat', [InventoryController::class, 'medicineIndex'])->name('data-obat');
    Route::post('/data-obat', [InventoryController::class, 'storeMedicine'])->name('medicines.store');
    Route::put('/data-obat/{id}', [InventoryController::class, 'updateMedicine'])->name('medicines.update');
    Route::delete('/data-obat/{id}', [InventoryController::class, 'destroyMedicine'])->name('medicines.destroy');
    Route::post('/data-obat/kategori', [InventoryController::class, 'storeCategory'])->name('categories.store');

});
