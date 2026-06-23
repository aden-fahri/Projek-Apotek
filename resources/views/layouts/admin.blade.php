<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediFlow Pro - Pharmacy Management System">
    <title>@yield('title', 'Dashboard') — {{ \App\Models\PharmacySetting::getSetting()->pharmacy_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}?v=1.1">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body { font-family: 'Quicksand', sans-serif !important; }
    </style>
</head>
<body>

<div class="layout-wrapper">

    {{-- ===== SIDEBAR ADMIN ===== --}}
    @include('navigasi.sidebar')

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
<script>
    function confirmDelete(event, form, title, text) {
        event.preventDefault();
        Swal.fire({
            title: title || 'Apakah Anda Yakin?',
            text: text || "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg px-4 py-2 font-semibold',
                cancelButton: 'rounded-lg px-4 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
</body>
</html>
