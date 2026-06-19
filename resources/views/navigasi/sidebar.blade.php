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
    <div class="logout-modal-card" id="logoutCard">
        <div class="logout-modal-body">
            <div class="logout-icon-wrapper">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3>Konfirmasi Keluar</h3>
            <p>Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
        </div>
        
        <div class="logout-modal-footer">
            <button onclick="closeLogoutModal()" class="btn-batal">Batal</button>
            <button onclick="submitLogout()" class="btn-keluar">Ya, Keluar</button>
        </div>
    </div>
</div>

<style>
    /* Styling Khusus Modal Logout (Tanpa Tailwind) */
    .logout-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .logout-modal-overlay.show {
        display: flex;
        opacity: 1;
    }
    .logout-modal-card {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 360px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transform: scale(0.95);
        opacity: 0;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .logout-modal-card.show {
        transform: scale(1);
        opacity: 1;
    }
    .logout-modal-body {
        padding: 30px 25px 20px;
        text-align: center;
    }
    .logout-icon-wrapper {
        width: 50px;
        height: 50px;
        background: #fef2f2;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    .logout-icon-wrapper i {
        color: #ef4444;
        font-size: 22px;
    }
    .logout-modal-body h3 {
        font-family: 'Quicksand', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 8px;
    }
    .logout-modal-body p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
    }
    .logout-modal-footer {
        background: #f8fafc;
        padding: 15px 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #f1f5f9;
    }
    .logout-modal-footer button {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .btn-batal {
        background: #e2e8f0;
        color: #475569;
    }
    .btn-batal:hover {
        background: #cbd5e1;
    }
    .btn-keluar {
        background: #ef4444;
        color: white;
    }
    .btn-keluar:hover {
        background: #dc2626;
    }
</style>

<script>
    function confirmLogout() {
        const modal = document.getElementById('logoutModal');
        const card = document.getElementById('logoutCard');
        
        modal.classList.add('show');
        setTimeout(() => {
            card.classList.add('show');
        }, 10);
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const card = document.getElementById('logoutCard');
        
        card.classList.remove('show');
        
        setTimeout(() => {
            modal.classList.remove('show');
        }, 200);
    }

    function submitLogout() {
        document.getElementById('logout-form').submit();
    }
</script>
