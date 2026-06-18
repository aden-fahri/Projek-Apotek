<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes — MediFlow Pro Pharmacy Management
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard admin
Route::get('/', function () {
    return redirect()->route('dashboard.admin');
});

// Auth & Dashboard Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes for different roles (No middleware for now)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/kasir', [DashboardController::class, 'kasir'])->name('dashboard.kasir');
Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

// Placeholder routes for sidebar navigation (Kasir)
Route::get('/transaksi', function () {
    return redirect()->route('dashboard');
})->name('transaksi');

Route::get('/riwayat-transaksi', function () {
    return redirect()->route('dashboard');
})->name('riwayat-transaksi');

Route::get('/stok-obat', function () {
    return redirect()->route('dashboard');
})->name('stok-obat');

// Placeholder routes for sidebar navigation (Admin)
Route::get('/data-obat', function () {
    return redirect()->route('dashboard');
})->name('data-obat');

Route::get('/supplier', function () {
    return redirect()->route('dashboard');
})->name('supplier');

Route::get('/kategori-obat', function () {
    return redirect()->route('dashboard');
})->name('kategori-obat');

Route::get('/golongan-obat', function () {
    return redirect()->route('dashboard');
})->name('golongan-obat');

Route::get('/laporan', function () {
    return redirect()->route('dashboard');
})->name('laporan');

Route::get('/pengguna', function () {
    return redirect()->route('dashboard');
})->name('pengguna');

Route::get('/pengaturan', function () {
    return redirect()->route('dashboard');
})->name('pengaturan');

// Load route per modul
require __DIR__.'/modules/auth.php';
