<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laporan') — Apotek</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Gunakan Vite untuk build CSS/JS --}}
    @vite(['resources/css/laporan.css'])
    @stack('styles')
</head>
<body>
<div class="app-wrapper">

    {{-- ========== SIDEBAR ========== --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">💊</div>
            <div class="brand-name">{{ config('app.name', 'Apotek') }}</div>
            <div class="brand-sub">Sistem Farmasi</div>
        </div>

        <nav class="sidebar-nav">
            <a href="#" class="nav-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                <span class="nav-icon">⊞</span> Dashboard
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('admin.inventaris*') ? 'active' : '' }}">
                <span class="nav-icon">📦</span> Inventori
            </a>
            <a href="#" class="nav-item {{ request()->routeIs('admin.transaksi*') ? 'active' : '' }}">
                <span class="nav-icon">🧾</span> Transaksi
            </a>
            <a href="{{ route('admin.laporan.masuk') }}" class="nav-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Laporan
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="#" class="nav-item">
                <span class="nav-icon">⚙️</span> Pengaturan
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:4px;">
                @csrf
                <button type="submit" class="nav-item" style="width:100%; border:none; cursor:pointer; background:transparent; text-align:left;">
                     <span class="nav-icon">↩</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ========== MAIN AREA ========== --}}
    <div class="main-content">

        {{-- NAVBAR --}}
        <header class="navbar">
            <div class="navbar-search">
                <div class="search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Cari laporan...">
                </div>
            </div>

            <div class="navbar-actions">
                <button class="btn-icon" title="Notifikasi">
                    🔔
                    <span class="badge-dot"></span>
                </button>
                <button class="btn-icon" title="Bantuan">❓</button>
                <div class="user-avatar" title="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="page-content">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
