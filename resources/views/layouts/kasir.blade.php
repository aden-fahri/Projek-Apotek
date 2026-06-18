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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f4f6f8] font-sans">

{{-- Layout: Sidebar + Main --}}
<div class="flex min-h-screen">

    {{-- ===== SIDEBAR KASIR (Light) ===== --}}
    <aside class="sidebar-light flex-shrink-0" id="sidebar">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="w-8 h-8 bg-[#009688] rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-pills text-white text-sm"></i>
            </div>
            <div>
                <p class="text-[13px] font-bold text-gray-800 leading-tight">MediFlow Pro</p>
                <p class="text-[10px] text-gray-500 leading-tight">Pharmacy Management</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-3 space-y-0.5">
            <a href="{{ route('dashboard.kasir') }}"
               class="{{ request()->routeIs('dashboard.kasir') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-gauge-high w-4 text-center {{ request()->routeIs('dashboard.kasir') ? 'text-white' : 'text-gray-400' }}"></i>
                Dashboard
            </a>
            <a href="{{ route('transaksi') }}"
               class="{{ request()->routeIs('transaksi') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-cart-shopping w-4 text-center {{ request()->routeIs('transaksi') ? 'text-white' : 'text-gray-400' }}"></i>
                Transaksi
            </a>
            <a href="{{ route('riwayat-transaksi') }}"
               class="{{ request()->routeIs('riwayat-transaksi') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('riwayat-transaksi') ? 'text-white' : 'text-gray-400' }}"></i>
                Riwayat Transaksi
            </a>
            <a href="{{ route('stok-obat') }}"
               class="{{ request()->routeIs('stok-obat') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-boxes-stacked w-4 text-center {{ request()->routeIs('stok-obat') ? 'text-white' : 'text-gray-400' }}"></i>
                Stok Obat
            </a>
        </nav>

        {{-- Bottom: Pengaturan --}}
        <div class="px-3 py-3 border-t border-gray-100">
            <a href="{{ route('pengaturan') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium text-gray-600 hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-gear w-4 text-center text-gray-400"></i>
                Pengaturan
            </a>
        </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="main-with-sidebar flex-1 flex flex-col">

        {{-- Top Navbar --}}
        <header class="bg-white border-b border-gray-200 h-14 flex items-center px-6 sticky top-0 z-30">
            {{-- Left spacer --}}
            <div class="flex-1"></div>

            {{-- Center: Page title --}}
            <h1 class="text-[16px] font-semibold text-gray-800 absolute left-1/2 -translate-x-1/2">
                @yield('page-title', 'Dashboard')
            </h1>

            {{-- Right: Search + Bell + Avatar --}}
            <div class="flex items-center gap-3 ml-auto">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text"
                           id="search-navbar"
                           placeholder="Cari transaksi atau obat..."
                           class="pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-[12px] text-gray-600 bg-gray-50 focus:outline-none focus:border-[#009688] w-48 focus:w-56 transition-all duration-200">
                </div>
                <button class="relative w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700">
                    <i class="fa-regular fa-bell text-[15px]"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="w-8 h-8 rounded-full bg-[#009688] flex items-center justify-center text-white text-[11px] font-bold">
                    KSR
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>{{-- /main-with-sidebar --}}
</div>{{-- /flex --}}

@stack('scripts')
</body>
</html>
