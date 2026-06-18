<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MediFlow Pro - Pharmacy Management System">
    <title>@yield('title', 'Dashboard') — MediFlow Pro</title>
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

    </div>
</div>

@stack('scripts')
</body>
</html>
