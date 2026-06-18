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
                            ($menu['route'] === 'pengguna' && request()->routeIs('employees.*')) ||
                            ($menu['route'] === 'laporan' && request()->routeIs('admin.laporan.*'));
            @endphp
            <a href="{{ route($menu['route']) }}" class="{{ $isActive ? 'active' : '' }}">
                <i class="fa-solid {{ $menu['icon'] }} nav-icon"></i>
                {{ $menu['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Bottom: Pengaturan & Keluar --}}
    <div class="sidebar-bottom">
        <a href="{{ route('pengaturan') }}" class="{{ request()->routeIs('pengaturan') ? 'active' : '' }}">
            <i class="fa-solid fa-gear nav-icon"></i>
            Pengaturan
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 4px; margin-bottom: 0;">
            @csrf
            <button type="submit" style="width: 100%; border: none; cursor: pointer; background: transparent; text-align: left; padding: 10px 12px; display: flex; align-items: center; gap: 12px; border-radius: 8px; font-size: 13px; font-weight: 500; color: #6b7280; font-family: 'Inter', sans-serif;">
                <i class="fa-solid fa-right-from-bracket nav-icon" style="color: #9ca3af;"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>
