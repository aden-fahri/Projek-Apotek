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
