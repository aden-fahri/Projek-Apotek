@php $sidebarKasir = \App\Models\PharmacySetting::getSetting(); @endphp

<aside class="sidebar-light" id="sidebar-kasir">
    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            @if($sidebarKasir->logo && file_exists(storage_path('app/public/' . $sidebarKasir->logo)))
                <img src="{{ Storage::url($sidebarKasir->logo) }}" alt="Logo" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
            @else
                <i class="fa-solid fa-pills"></i>
            @endif
        </div>
        <div class="sidebar-logo-text">
            <p class="sidebar-logo-title">{{ $sidebarKasir->pharmacy_name }}</p>
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
            <i class="fa-solid fa-box nav-icon"></i>
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
