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
        <a href="{{ route('dashboard.admin') }}"
           class="{{ request()->routeIs('dashboard.admin') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
            <i class="fa-solid fa-gauge-high w-4 text-center {{ request()->routeIs('dashboard.admin') ? 'text-white' : 'text-gray-400' }} text-sm"></i>
            Dashboard
        </a>

        <a href="{{ route('pengguna') }}"
           class="{{ request()->routeIs('pengguna') || request()->routeIs('employees.*') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
            <i class="fa-solid fa-users w-4 text-center {{ request()->routeIs('pengguna') || request()->routeIs('employees.*') ? 'text-white' : 'text-gray-400' }} text-sm"></i>
            Pengguna
        </a>

        {{-- Laporan Dropdown / Submenus --}}
        <div class="pt-2 pb-1">
            <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Laporan</p>
            <a href="{{ route('admin.laporan.masuk') }}"
               class="{{ request()->routeIs('admin.laporan.masuk') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-arrow-trend-up w-4 text-center {{ request()->routeIs('admin.laporan.masuk') ? 'text-white' : 'text-gray-400' }} text-sm"></i>
                Laporan Masuk
            </a>
            <a href="{{ route('admin.laporan.keluar') }}"
               class="{{ request()->routeIs('admin.laporan.keluar') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-arrow-trend-down w-4 text-center {{ request()->routeIs('admin.laporan.keluar') ? 'text-white' : 'text-gray-400' }} text-sm"></i>
                Laporan Keluar
            </a>
            <a href="{{ route('admin.laporan.laba') }}"
               class="{{ request()->routeIs('admin.laporan.laba') ? 'bg-[#009688] text-white' : 'text-gray-600 hover:bg-gray-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium transition-all duration-150">
                <i class="fa-solid fa-chart-line w-4 text-center {{ request()->routeIs('admin.laporan.laba') ? 'text-white' : 'text-gray-400' }} text-sm"></i>
                Laporan Laba
            </a>
        </div>
    </nav>

    {{-- Bottom: Logout Only --}}
    <div class="px-3 py-3 border-t border-gray-100">
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium text-red-600 hover:bg-red-50 transition-all text-left">
                <i class="fa-solid fa-right-from-bracket w-4 text-center text-red-400 text-sm"></i>
                Logout
            </button>
        </form>
    </div>
</aside>
