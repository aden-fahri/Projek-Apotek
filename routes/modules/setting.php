<?php

use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Modul: Pengaturan
| F-20: Pengaturan Apotek (Admin only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/pengaturan',  [SettingController::class, 'edit'])  ->name('pengaturan');
    Route::put('/pengaturan',  [SettingController::class, 'update'])->name('pengaturan.update');
});
