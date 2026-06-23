<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MedicineGroupController;
use App\Http\Controllers\UnitController;

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
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    Route::get('/kategori-obat', fn() => redirect()->route('categories.index'))->name('kategori-obat');
    Route::get('categories/{category}/medicines', [CategoryController::class, 'medicines'])->name('categories.medicines');

    // F-07: Golongan Obat
    Route::resource('medicine-groups', MedicineGroupController::class)->except(['show', 'create', 'edit']);
    Route::get('/golongan-obat', fn() => redirect()->route('medicine-groups.index'))->name('golongan-obat');
    Route::get('medicine-groups/{medicineGroup}/medicines', [MedicineGroupController::class, 'medicines'])->name('medicine-groups.medicines');

    // F-08: Satuan Obat
    Route::resource('units', UnitController::class)->except(['show', 'create', 'edit']);
    Route::get('/satuan-obat', fn() => redirect()->route('units.index'))->name('satuan-obat');
    Route::get('units/{unit}/medicines', [UnitController::class, 'medicines'])->name('units.medicines');

    Route::get('/data-obat', [InventoryController::class, 'medicineIndex'])->name('data-obat');
    Route::post('/data-obat', [InventoryController::class, 'storeMedicine'])->name('medicines.store');
    Route::put('/data-obat/{id}', [InventoryController::class, 'updateMedicine'])->name('medicines.update');
    Route::delete('/data-obat/{id}', [InventoryController::class, 'destroyMedicine'])->name('medicines.destroy');
    Route::post('/data-obat/kategori', [InventoryController::class, 'storeCategory'])->name('categories.store');

});
