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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
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

@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    document.body.classList.toggle('sidebar-open');
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                }
            });
        }
    });
</script>
</body>
</html>
