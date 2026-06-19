<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pharmacy Management System">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\PharmacySetting::getSetting()->pharmacy_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}?v={{ time() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body { font-family: 'Quicksand', sans-serif !important; }
    </style>
</head>
<body>

<div class="layout-wrapper">

    {{-- ===== SIDEBAR KASIR ===== --}}
    @include('navigasi.sidebar-kasir')

    {{-- ===== MAIN AREA ===== --}}
    <div class="main-area">

        {{-- Top Header --}}
        @include('navigasi.header')

        {{-- Page Content --}}
        <main class="page-content">
            @yield('content')
        </main>

        {{-- ===== FOOTER APOTEK ===== --}}
        @include('navigasi.footer')

    </div>
</div>

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

@stack('scripts')
</body>
</html>
