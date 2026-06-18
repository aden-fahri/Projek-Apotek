<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Redirect to employees page for now so we can test the new UI easily
    return redirect()->route('employees.index');
});

// Load route per modul
require __DIR__.'/modules/auth.php';
