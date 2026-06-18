<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes — MediFlow Pro
| File ini harus MINIMAL. Tambahkan route ke file modules/ masing-masing.
|--------------------------------------------------------------------------
|
*/

// Root → Login (sesuai AGENTS.md §7)
Route::get('/', fn() => redirect()->route('login'));

// Auth (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Load route per modul (sesuai AGENTS.md §7)
require __DIR__.'/modules/dashboard.php';
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/master-data.php';
require __DIR__.'/modules/inventory.php';
require __DIR__.'/modules/transaction.php';
require __DIR__.'/modules/report.php';
require __DIR__.'/modules/setting.php';
