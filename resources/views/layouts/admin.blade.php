<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediFlow Pro - Pharmacy Management System">
    <title>@yield('title', 'Dashboard') — MediFlow Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
    @stack('styles')
</head>
<body>

<div class="layout-wrapper">

    {{-- ===== SIDEBAR ADMIN ===== --}}
    <aside class="sidebar-light" id="sidebar-admin">
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
            @php
                $adminMenus = [
                    ['route' => 'dashboard.admin',    'icon' => 'fa-gauge-high',        'label' => 'Dashboard'],
                    ['route' => 'data-obat',          'icon' => 'fa-capsules',          'label' => 'Data Obat'],
                    ['route' => 'supplier',           'icon' => 'fa-truck',             'label' => 'Supplier'],
                    ['route' => 'kategori-obat',      'icon' => 'fa-tag',               'label' => 'Kategori Obat'],
                    ['route' => 'golongan-obat',      'icon' => 'fa-layer-group',       'label' => 'Golongan Obat'],
                    ['route' => 'stok-obat',          'icon' => 'fa-boxes-stacked',     'label' => 'Stok Obat'],
                    ['route' => 'transaksi',          'icon' => 'fa-cart-shopping',     'label' => 'Transaksi'],
                    ['route' => 'riwayat-transaksi',  'icon' => 'fa-clock-rotate-left', 'label' => 'Riwayat Transaksi'],
                    ['route' => 'laporan',            'icon' => 'fa-chart-bar',         'label' => 'Laporan'],
                    ['route' => 'pengguna',           'icon' => 'fa-users',             'label' => 'Pengguna'],
                ];
            @endphp

            @foreach($adminMenus as $menu)
                @php
                    $isActive = request()->routeIs($menu['route']) ||
                                ($menu['route'] === 'supplier' && request()->routeIs('suppliers.*')) ||
                                ($menu['route'] === 'pengguna' && request()->routeIs('employees.*'));
                @endphp
                <a href="{{ route($menu['route']) }}" class="{{ $isActive ? 'active' : '' }}">
                    <i class="fa-solid {{ $menu['icon'] }} nav-icon"></i>
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Bottom: Pengaturan & Keluar --}}
        <div class="sidebar-bottom">
            <a href="{{ route('pengaturan') }}">
                <i class="fa-solid fa-gear nav-icon"></i>
                Pengaturan
            </a>
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
                    <input type="text" id="search-admin" placeholder="Search...">
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
