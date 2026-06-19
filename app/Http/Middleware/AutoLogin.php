<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AutoLogin
{
    /**
     * Handle an incoming request.
     * Automatically signs in the correct user role depending on the URL path.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya jalankan AutoLogin jika diaktifkan di .env (AUTO_LOGIN=true)
        if (!env('AUTO_LOGIN', false)) {
            return $next($request);
        }

        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        $path = $request->path();
        \Log::info('AutoLogin middleware called for path: ' . $path);
        
        // Tentukan role target berdasarkan path URL
        $targetRole = 'admin';
        if (str_contains($path, 'kasir') || str_contains($path, 'transaksi')) {
            $targetRole = 'kasir';
        }
        
        // Jika belum login, otomatis login sebagai user yang benar berdasarkan path.
        // Jika sudah login secara manual, pertahankan session yang ada agar tidak tertimpa.
        if (!Auth::check()) {
            $user = User::where('role', $targetRole)->first();
            if ($user) {
                \Log::info('AutoLogin logging in user: ' . $user->email);
                Auth::login($user);
            }
        }
        
        return $next($request);
    }
}
