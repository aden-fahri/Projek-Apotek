<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Inventaris & Stok
| F-10: Purchase Order (Admin)
| F-11: Lihat Stok Obat (Admin + Kasir)
| F-14: Return Obat (Admin)
| Dikerjakan oleh: Developer B (feature/inventory)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // F-11: Stok Obat — bisa dilihat Admin & Kasir
    Route::get('/stok-obat',        fn() => redirect()->route('dashboard'))->name('stok-obat');

    // F-10: Purchase Order — Admin only
    Route::get('/purchase-order',   fn() => redirect()->route('dashboard.admin'))
        ->middleware('role:admin')
        ->name('purchase-order');

    // F-14: Return Obat — Admin only
    Route::get('/return-obat',      fn() => redirect()->route('dashboard.admin'))
        ->middleware('role:admin')
        ->name('return-obat');

});
