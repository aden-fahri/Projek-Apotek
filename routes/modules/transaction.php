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

Route::middleware('auth')->group(function () {

    // F-15: Kasir POS — Kasir only
    Route::get('/transaksi',        fn() => redirect()->route('dashboard.kasir'))
        ->middleware('role:kasir')
        ->name('transaksi');

    // F-16: Riwayat Transaksi — Admin + Kasir
    Route::get('/riwayat-transaksi', fn() => redirect()->route('dashboard'))->name('riwayat-transaksi');

});
