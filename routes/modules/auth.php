<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// Karyawan / Employee Management Routes (Protected by auth middleware)
Route::middleware('auth')->prefix('employees')->name('employees.')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    // Here we will add more routes for create, store, edit, update, toggle later
});
