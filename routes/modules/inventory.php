<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Inventaris & Stok
| F-10: Purchase Order (Admin)
| F-11: Lihat Stok Obat (Admin + Kasir)
| F-14: Return Obat (Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // F-11: Stok Obat — bisa dilihat Admin & Kasir
    Route::get('/stok-obat', [InventoryController::class, 'stockIndex'])->name('stok-obat');

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // F-10: Purchase Order (PO)
        Route::get('/purchase-order', [InventoryController::class, 'poIndex'])->name('purchase-order');
        Route::get('/purchase-order/create', [InventoryController::class, 'poCreate'])->name('purchase-order.create');
        Route::post('/purchase-order', [InventoryController::class, 'poStore'])->name('purchase-order.store');
        Route::get('/purchase-order/{id}/edit', [InventoryController::class, 'poEdit'])->name('purchase-order.edit');
        Route::put('/purchase-order/{id}', [InventoryController::class, 'poUpdate'])->name('purchase-order.update');
        Route::post('/purchase-order/{id}/cancel', [InventoryController::class, 'poCancel'])->name('purchase-order.cancel');
        Route::delete('/purchase-order/{id}', [InventoryController::class, 'poDestroy'])->name('purchase-order.destroy');

        // F-14: Return Obat
        Route::get('/return-obat', [InventoryController::class, 'returnIndex'])->name('return-obat');
        Route::get('/return-obat/create', [InventoryController::class, 'returnCreate'])->name('return-obat.create');
        Route::post('/return-obat', [InventoryController::class, 'returnStore'])->name('return-obat.store');

    });

    // API helper for dynamic JavaScript dropdown
    Route::get('/api/available-stocks', [InventoryController::class, 'getAvailableStocks'])->name('api.available-stocks');

});
