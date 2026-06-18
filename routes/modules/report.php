<?php

use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul Laporan (F-17, F-18, F-19)
| Hanya bisa diakses oleh Admin
| Branch: feature/reports
|--------------------------------------------------------------------------
*/

Route::prefix('admin/laporan')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.laporan.')
    ->group(function () {

        // F-17: Laporan Masuk (Uang Masuk / Penjualan)
        Route::get('/masuk', [ReportController::class, 'masuk'])
            ->name('masuk');
        Route::get('/masuk/export', [ReportController::class, 'exportMasuk'])
            ->name('masuk.export');

        // F-18: Laporan Keluar (Uang Keluar / Pembelian)
        Route::get('/keluar', [ReportController::class, 'keluar'])
            ->name('keluar');
        Route::get('/keluar/export', [ReportController::class, 'exportKeluar'])
            ->name('keluar.export');

        // F-19: Laporan Laba (Kotor & Bersih)
        Route::get('/laba', [ReportController::class, 'laba'])
            ->name('laba');
        Route::get('/laba/export', [ReportController::class, 'exportLaba'])
            ->name('laba.export');
    });
