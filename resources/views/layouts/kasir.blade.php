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

@stack('scripts')
</body>
</html>
