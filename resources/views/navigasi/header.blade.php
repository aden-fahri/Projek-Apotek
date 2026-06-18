<header class="admin-header">
    {{-- Center: Page title --}}
    <h2 style="font-size: 15px; font-weight: 600; color: #1f2937; margin: 0; position: absolute; left: 50%; transform: translateX(-50%);">
        @yield('page-title', 'Dashboard')
    </h2>

    {{-- Right Items --}}
    <div class="header-right">
        <div class="header-search" style="width: 200px;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="search-admin" placeholder="Search...">
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
