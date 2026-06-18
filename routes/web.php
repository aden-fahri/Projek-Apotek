<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// =====================================================================
// Modul-modul aplikasi (tiap anggota kelompok punya file sendiri)
// Tambahkan require sesuai modul yang sudah dibuat
// =====================================================================
require __DIR__.'/modules/report.php';  // Branch: feature/reports

