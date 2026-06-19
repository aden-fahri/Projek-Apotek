<header class="admin-header">
    {{-- Right Items --}}
    <div class="header-right">
        <div class="avatar-circle" title="{{ auth()->user()->name }}">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
    </div>
</header>

