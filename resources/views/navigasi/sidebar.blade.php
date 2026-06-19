<aside class="sidebar-light" id="sidebar-admin">
    {{-- Logo --}}
    @php $appSetting = \App\Models\PharmacySetting::getSetting(); @endphp
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            @if($appSetting->logo && file_exists(storage_path('app/public/' . $appSetting->logo)))
                <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
            @else
                <i class="fa-solid fa-pills"></i>
            @endif
        </div>
        <div class="sidebar-logo-text">
            <p class="sidebar-logo-title">{{ $appSetting->pharmacy_name }}</p>
            <p class="sidebar-logo-subtitle">Pharmacy Management</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        @php
            $sidebarItems = [
                ['type' => 'link', 'route' => 'dashboard.admin', 'icon' => 'fa-bolt-lightning', 'label' => 'Dashboard'],
                
                ['type' => 'header', 'label' => 'MASTER DATA'],
                ['type' => 'link', 'route' => 'supplier', 'icon' => 'fa-prescription-bottle', 'label' => 'Supplier'],
                ['type' => 'link', 'route' => 'stok-obat', 'icon' => 'fa-box', 'label' => 'Stok Obat'],
                
                ['type' => 'header', 'label' => 'TRANSAKSI'],
                ['type' => 'link', 'route' => 'purchase-order', 'icon' => 'fa-file-lines', 'label' => 'Pembelian (PO)'],
                ['type' => 'link', 'route' => 'return-obat', 'icon' => 'fa-arrow-rotate-left', 'label' => 'Return Obat'],
                
                ['type' => 'header', 'label' => 'LAPORAN'],
                ['type' => 'link', 'route' => 'admin.laporan.masuk', 'icon' => 'fa-arrow-trend-up', 'label' => 'Laporan Masuk'],
                ['type' => 'link', 'route' => 'admin.laporan.keluar', 'icon' => 'fa-arrow-trend-down', 'label' => 'Laporan Keluar'],
                ['type' => 'link', 'route' => 'admin.laporan.laba', 'icon' => 'fa-scale-balanced', 'label' => 'Laporan Laba'],
                
                ['type' => 'header', 'label' => 'PENGATURAN'],
                ['type' => 'link', 'route' => 'pengguna', 'icon' => 'fa-users', 'label' => 'Pengguna'],
                ['type' => 'link', 'route' => 'pengaturan', 'icon' => 'fa-gear', 'label' => 'Pengaturan'],
            ];
        @endphp

        @foreach($sidebarItems as $item)
            @if($item['type'] === 'header')
                <div class="sidebar-header-title">{{ $item['label'] }}</div>
            @else
                @php
                    $isActive = false;
                    $route = $item['route'];
                    if ($route === 'dashboard.admin') {
                        $isActive = request()->routeIs('dashboard.admin');
                    } elseif ($route === 'supplier') {
                        $isActive = request()->routeIs('supplier') || request()->routeIs('suppliers.*');
                    } elseif ($route === 'stok-obat') {
                        $isActive = request()->routeIs('stok-obat') || request()->routeIs('stok-obat.*') || request()->routeIs('medicines.*');
                    } elseif ($route === 'purchase-order') {
                        $isActive = request()->routeIs('purchase-order') || request()->routeIs('purchase-order.*');
                    } elseif ($route === 'return-obat') {
                        $isActive = request()->routeIs('return-obat') || request()->routeIs('return-obat.*');
                    } elseif ($route === 'admin.laporan.masuk') {
                        $isActive = request()->routeIs('admin.laporan.masuk') || request()->routeIs('admin.laporan.masuk.*');
                    } elseif ($route === 'admin.laporan.keluar') {
                        $isActive = request()->routeIs('admin.laporan.keluar') || request()->routeIs('admin.laporan.keluar.*');
                    } elseif ($route === 'admin.laporan.laba') {
                        $isActive = request()->routeIs('admin.laporan.laba') || request()->routeIs('admin.laporan.laba.*');
                    } elseif ($route === 'pengguna') {
                        $isActive = request()->routeIs('pengguna') || request()->routeIs('employees.*');
                    } elseif ($route === 'pengaturan') {
                        $isActive = request()->routeIs('pengaturan') || request()->routeIs('pengaturan.*');
                    }
                @endphp
                <a href="{{ route($route) }}" class="{{ $isActive ? 'active' : '' }}">
                    <i class="fa-solid {{ $item['icon'] }} nav-icon"></i>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Bottom: Keluar with pop-up confirmation --}}
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

{{-- ===== LOGOUT CONFIRMATION MODAL ===== --}}
<div id="logoutModal" class="logout-modal-overlay">
    {{-- Modal Card --}}
    <div class="logout-modal-card" id="logoutCard">
        {{-- Body --}}
        <div class="logout-modal-body">
            <div class="logout-modal-icon-wrapper">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3 class="logout-modal-title">Konfirmasi Keluar</h3>
            <p class="logout-modal-text">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
        </div>
        
        {{-- Footer --}}
        <div class="logout-modal-footer">
            <button onclick="closeLogoutModal()" class="logout-modal-btn logout-modal-btn-cancel">
                Batal
            </button>
            <button onclick="submitLogout()" class="logout-modal-btn logout-modal-btn-confirm">
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

<script>
    function confirmLogout() {
        const modal = document.getElementById('logoutModal');
        modal.classList.add('active');
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('active');
    }

    function submitLogout() {
        document.getElementById('logout-form').submit();
    }
</script>
