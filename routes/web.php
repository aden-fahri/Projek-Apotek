<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (which redirects to login if guest)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth & Dashboard Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Supplier Management Routes (Restricted to Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('suppliers', SupplierController::class);
    Route::post('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
});

// Load route per modul (contains employee routes from main branch)
require __DIR__.'/modules/auth.php';
