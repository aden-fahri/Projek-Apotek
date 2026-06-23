@extends('layouts.kasir')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard')

@section('content')
<div class="p-6 space-y-6">

    {{-- ===== WELCOME ===== --}}
    <div>
        <h2 class="text-[22px] font-bold text-gray-800">Selamat Datang, {{ $data['userName'] }}</h2>
        <p class="text-[13px] text-gray-500 mt-1">Kelola transaksi dan pantau kondisi apotek hari ini.</p>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Total Penjualan --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total Penjualan<br>Hari Ini</p>
                    <p class="text-[26px] font-bold text-gray-800 mt-1 leading-tight">{{ $data['totalPenjualan'] }}</p>
                    <p class="text-[11px] mt-1 {{ $data['trendPenjualanUp'] ? 'text-[#10b981]' : 'text-red-500' }}">
                        <i class="fa-solid {{ $data['trendPenjualanUp'] ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1"></i>
                        {{ $data['trendPenjualan'] }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-[#e0f2f1] flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-[#009688] text-[15px]"></i>
                </div>
            </div>
        </div>

        {{-- Jumlah Transaksi --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Jumlah<br>Transaksi</p>
                    <p class="text-[26px] font-bold text-gray-800 mt-1 leading-tight">{{ $data['jumlahTransaksi'] }}</p>
                    <p class="text-[11px] mt-1 {{ $data['trendTransaksiUp'] ? 'text-[#10b981]' : 'text-red-500' }}">
                        <i class="fa-solid {{ $data['trendTransaksiUp'] ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1"></i>
                        {{ $data['trendTransaksi'] }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-[#e0f2f1] flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-[#009688] text-[15px]"></i>
                </div>
            </div>
        </div>
        
        {{-- Obat Terjual --}}
        <div class="stat-card flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-pills text-orange-500 text-[14px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">Obat Terjual</p>
                <p class="text-[14px] font-bold text-gray-800">{{ $data['obatTerjual'] }}</p>
            </div>
        </div>
        
        {{-- Resep Dilayani --}}
        <div class="stat-card flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#e0f2f1] flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-file-prescription text-[#009688] text-[14px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">Resep Dilayani</p>
                <p class="text-[14px] font-bold text-gray-800">{{ $data['resepDilayani'] }}</p>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top 5 Obat Terlaris --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Top 5 Obat Terlaris</h3>
            <div class="space-y-3">
                @foreach($data['topObat'] as $obat)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[13px] font-medium text-gray-700">{{ $obat['nama'] }}</p>
                        <p class="text-[11px] text-gray-400">{{ $obat['kategori'] }}</p>
                    </div>
                    <span class="badge-terjual">{{ $obat['terjual'] }} Terjual</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Obat Mendekati Kadaluwarsa --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-orange-500 text-[14px]"></i>
                    <h3 class="text-[13px] font-semibold text-gray-700">Obat Mendekati Kadaluwarsa</h3>
                </div>
                <a href="{{ route('stok-obat') }}" class="text-[11px] text-[#009688] hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @foreach($data['obatKadaluwarsa'] as $obat)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-[12px] font-medium text-gray-700">{{ $obat['nama'] }}</p>
                        <p class="text-[11px] text-gray-400">{{ $obat['batch'] }}</p>
                    </div>
                    <span class="badge-hari">{{ $obat['hari'] }} Hari</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== TRANSAKSI TERAKHIR ===== --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[13px] font-semibold text-gray-700">10 Transaksi Terakhir</h3>
            <a href="{{ route('riwayat-transaksi') }}" class="text-[11px] text-[#009688] hover:underline font-medium">Lihat Riwayat</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="text-left">ID Transaksi</th>
                        <th class="text-left">Waktu</th>
                        <th class="text-left">Total Item</th>
                        <th class="text-left">Total Harga</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @foreach($data['transaksi'] as $trx)
                    <tr>
                        <td><a href="#" class="text-[#009688] font-medium hover:underline">{{ $trx['id'] }}</a></td>
                        <td class="text-gray-500">{{ $trx['waktu'] }}</td>
                        <td>{{ $trx['item'] }}</td>
                        <td class="font-medium">{{ $trx['total'] }}</td>
                        <td><span class="badge-selesai">{{ $trx['status'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
