<?php

use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (which redirects to login if guest)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Load modular routes
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/dashboard.php';
