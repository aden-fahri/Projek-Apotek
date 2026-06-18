<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth', 'role:admin'])->group(function () {

    // F-05: Suplayer
    Route::get('/supplier',         fn() => redirect()->route('dashboard.admin'))->name('supplier');

    // F-06: Kategori Obat
    Route::get('/kategori-obat',    fn() => redirect()->route('dashboard.admin'))->name('kategori-obat');

    // F-07: Golongan Obat
    Route::get('/golongan-obat',    fn() => redirect()->route('dashboard.admin'))->name('golongan-obat');

    // F-08: Satuan Obat
    Route::get('/satuan-obat',      fn() => redirect()->route('dashboard.admin'))->name('satuan-obat');

    // F-09: Data Obat
    Route::get('/data-obat',        fn() => redirect()->route('dashboard.admin'))->name('data-obat');

});
