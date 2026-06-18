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
                ['route' => 'dashboard.admin',      'icon' => 'fa-gauge-high',        'label' => 'Dashboard'],
                ['route' => 'supplier',             'icon' => 'fa-truck',             'label' => 'Supplier'],
                ['route' => 'admin.laporan.masuk',  'icon' => 'fa-arrow-trend-up',     'label' => 'Laporan Masuk'],
                ['route' => 'admin.laporan.keluar', 'icon' => 'fa-arrow-trend-down',   'label' => 'Laporan Keluar'],
                ['route' => 'admin.laporan.laba',   'icon' => 'fa-scale-balanced',     'label' => 'Laporan Laba'],
                ['route' => 'pengguna',             'icon' => 'fa-users',             'label' => 'Pengguna'],
            ];
        @endphp

        @foreach($adminMenus as $menu)
            @php
                $isActive = false;
                if ($menu['route'] === 'dashboard.admin') {
                    $isActive = request()->routeIs('dashboard.admin');
                } elseif ($menu['route'] === 'supplier') {
                    $isActive = request()->routeIs('supplier') || request()->routeIs('suppliers.*');
                } elseif ($menu['route'] === 'admin.laporan.masuk') {
                    $isActive = request()->routeIs('admin.laporan.masuk') || request()->routeIs('admin.laporan.masuk.*');
                } elseif ($menu['route'] === 'admin.laporan.keluar') {
                    $isActive = request()->routeIs('admin.laporan.keluar') || request()->routeIs('admin.laporan.keluar.*');
                } elseif ($menu['route'] === 'admin.laporan.laba') {
                    $isActive = request()->routeIs('admin.laporan.laba') || request()->routeIs('admin.laporan.laba.*');
                } elseif ($menu['route'] === 'pengguna') {
                    $isActive = request()->routeIs('pengguna') || request()->routeIs('employees.*');
                }
            @endphp
            <a href="{{ route($menu['route']) }}" class="{{ $isActive ? 'active' : '' }}">
                <i class="fa-solid {{ $menu['icon'] }} nav-icon"></i>
                {{ $menu['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Bottom: Keluar Only --}}
    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 4px; margin-bottom: 0;">
            @csrf
            <button type="submit" style="width: 100%; border: none; cursor: pointer; background: transparent; text-align: left; padding: 10px 12px; display: flex; align-items: center; gap: 12px; border-radius: 8px; font-size: 13px; font-weight: 500; color: #6b7280; font-family: 'Inter', sans-serif;">
                <i class="fa-solid fa-right-from-bracket nav-icon" style="color: #9ca3af;"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>
