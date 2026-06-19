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
            <a href="{{ route('stok-obat') }}" class="{{ request()->routeIs('stok-obat') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked nav-icon"></i>
                Stok Obat
            </a>
        </nav>

        {{-- Bottom: Keluar Only --}}
        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="margin: 0;">
                @csrf
                <button type="button" onclick="confirmLogout()" style="width: 100%; border: none; cursor: pointer; background: transparent; text-align: left; padding: 10px 12px; display: flex; align-items: center; gap: 12px; border-radius: 8px; font-size: 13px; font-weight: 500; font-family: 'Inter', sans-serif;">
                    <i class="fa-solid fa-right-from-bracket nav-icon"></i>
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

{{-- ===== LOGOUT CONFIRMATION MODAL ===== --}}
<div id="logoutModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-black/40 backdrop-blur-sm">
    {{-- Modal Card --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-sm w-full m-4 relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="logoutCard">
        {{-- Body --}}
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-right-from-bracket text-red-500 text-[20px]"></i>
            </div>
            <h3 class="font-bold text-[16px] text-gray-800">Konfirmasi Keluar</h3>
            <p class="text-[13px] text-gray-500 mt-2">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
        </div>
        
        {{-- Footer --}}
        <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end gap-2">
            <button onclick="closeLogoutModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors cursor-pointer">
                Batal
            </button>
            <button onclick="submitLogout()" class="bg-red-500 hover:bg-red-600 text-white font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors cursor-pointer">
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

<script>
    function confirmLogout() {
        const modal = document.getElementById('logoutModal');
        const card = document.getElementById('logoutCard');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const card = document.getElementById('logoutCard');
        
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 200);
    }

    function submitLogout() {
        document.getElementById('logout-form').submit();
    }
</script>

@stack('scripts')
</body>
</html>
