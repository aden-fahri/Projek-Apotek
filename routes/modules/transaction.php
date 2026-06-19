<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Transaksi (POS)
| F-15: Sistem Kasir/POS (Kasir only)
| F-16: Riwayat Transaksi (Admin + Kasir)
| Dikerjakan oleh: Developer C (feature/pos)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\TransactionController;

Route::middleware('auth')->group(function () {

    // F-15: Kasir POS — Kasir only
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir', [TransactionController::class, 'pos'])->name('kasir');
        Route::get('/kasir/search-medicine', [TransactionController::class, 'searchMedicine'])->name('kasir.search');
        Route::post('/kasir/store', [TransactionController::class, 'store'])->name('kasir.store');
    });

    // F-16: Riwayat Transaksi — Admin + Kasir
    Route::get('/riwayat-transaksi', [TransactionController::class, 'history'])->name('riwayat-transaksi');
    
    // F-16: Batal Transaksi — Admin only
    Route::post('/riwayat-transaksi/{id}/cancel', [TransactionController::class, 'cancelTransaction'])
        ->middleware('role:admin')
        ->name('riwayat-transaksi.cancel');

});
