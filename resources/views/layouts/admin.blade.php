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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f4f6f8] font-sans">

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR ADMIN (Light) ===== --}}
    <aside class="sidebar-light flex-shrink-0" id="sidebar-admin">
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
        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
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
                <a href="{{ route($menu['route']) }}"
                   class="{{ request()->routeIs($menu['route']) ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                    <i class="fa-solid {{ $menu['icon'] }} w-4 text-center {{ request()->routeIs($menu['route']) ? 'text-white' : 'text-gray-400' }} text-sm"></i>
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Bottom: Pengaturan --}}
        <div class="px-3 py-3 border-t border-gray-100">
            <a href="{{ route('pengaturan') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium text-gray-600 hover:bg-gray-100 transition-all">
                <i class="fa-solid fa-gear w-4 text-center text-gray-400 text-sm"></i>
                Pengaturan
            </a>
        </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="main-with-sidebar flex-1 flex flex-col">

        {{-- Top Search Bar --}}
        <header class="bg-white border-b border-gray-200 h-12 flex items-center px-6 sticky top-0 z-30">
            <div class="flex-1 flex justify-center">
                <div class="relative w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text"
                           id="search-admin"
                           placeholder="Search..."
                           class="w-full pl-8 pr-3 py-1.5 border border-gray-200 rounded-lg text-[13px] text-gray-600 bg-gray-50 focus:outline-none focus:border-[#009688]">
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-5">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
