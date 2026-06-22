<header class="admin-header">
    {{-- Left Items --}}
    <div class="header-left">
        <button type="button" class="icon-btn sidebar-toggle" id="sidebarToggle" style="margin-right: 15px;">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
    
    {{-- Right Items --}}
    <div class="header-right">
        <div class="avatar-circle" title="{{ auth()->user()->name }}">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
    </div>
</header>

