<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediFlow Pro - Pharmacy Management System">
    <title>@yield('title', 'Dashboard') — MediFlow Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body { font-family: 'Quicksand', sans-serif !important; }
    </style>
</head>
<body>

<div class="layout-wrapper">

    {{-- ===== SIDEBAR KASIR ===== --}}
    <aside class="sidebar-light" id="sidebar-kasir">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-pills"></i>
            </div>
            <div class="sidebar-logo-text">
                <p class="sidebar-logo-title">MediFlow Pro</p>
                <p class="sidebar-logo-subtitle">Pharmacy Management</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard.kasir') }}" class="{{ request()->routeIs('dashboard.kasir') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high nav-icon"></i>
                Dashboard
            </a>
        </nav>

        {{-- Bottom: Keluar Only --}}
        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 4px;">
                @csrf
                <button type="submit" style="width: 100%; border: none; cursor: pointer; background: transparent; text-align: left; padding: 10px 12px; display: flex; align-items: center; gap: 12px; border-radius: 8px; font-size: 13px; font-weight: 500; color: #6b7280; font-family: 'Inter', sans-serif;">
                    <i class="fa-solid fa-right-from-bracket nav-icon" style="color: #9ca3af;"></i>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="main-area">

        {{-- Top Header --}}
        <header class="admin-header">
            {{-- Center: Page title --}}
            <h2 style="font-size: 15px; font-weight: 600; color: #1f2937; margin: 0; position: absolute; left: 50%; transform: translateX(-50%);">
                @yield('page-title', 'Dashboard')
            </h2>

            {{-- Right Items --}}
            <div class="header-right">
                <div class="header-search" style="width: 200px;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="search-kasir" placeholder="Search...">
                </div>
                <button class="icon-btn" title="Notifikasi">
                    <i class="fa-regular fa-bell" style="font-size: 15px;"></i>
                    <span class="badge-dot"></span>
                </button>
                <div class="avatar-circle" title="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
