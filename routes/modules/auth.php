<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Auth — Manajemen Karyawan
| Dikerjakan oleh: Developer A
| Akses: Admin only
|--------------------------------------------------------------------------
*/

// Manajemen Karyawan (F-02, F-03)
Route::prefix('employees')
    ->middleware(['auth', 'role:admin'])
    ->name('employees.')
    ->group(function () {
        Route::get('/',                   [EmployeeController::class, 'index'])->name('index');
        Route::get('/create',             [EmployeeController::class, 'create'])->name('create');
        Route::post('/',                  [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit',    [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}',         [EmployeeController::class, 'update'])->name('update');
    });

// Alias "pengguna" ke employees
Route::middleware('auth')->get('/pengguna', fn() => redirect()->route('employees.index'))->name('pengguna');
