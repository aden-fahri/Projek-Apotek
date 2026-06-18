<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Dashboard
| Admin → /dashboard/admin
| Kasir → /dashboard/kasir
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Auto-redirect berdasarkan role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard Admin (khusus admin)
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    // Dashboard Kasir (khusus kasir)
    Route::get('/dashboard/kasir', [DashboardController::class, 'kasir'])
        ->middleware('role:kasir')
        ->name('dashboard.kasir');

});
