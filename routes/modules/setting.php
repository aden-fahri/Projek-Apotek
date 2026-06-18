<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Pengaturan
| F-20: Pengaturan Apotek (Admin only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/pengaturan', fn() => redirect()->route('dashboard.admin'))->name('pengaturan');
});
