@extends('layouts.kasir')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard')

@section('content')
<div class="p-6 space-y-6">

    {{-- ===== WELCOME ===== --}}
    <div>
        <h2 class="text-[22px] font-bold text-gray-800">Selamat Datang, {{ $data['userName'] }}</h2>
        <p class="text-[13px] text-gray-500 mt-1">Kelola transaksi dan pantau kondisi stok hari ini.</p>
    </div>

    {{-- ===== ROW 1: 4 STAT CARDS BESAR ===== --}}
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

        {{-- Mendekati Kadaluwarsa --}}
        <div class="stat-card border-l-4 border-l-orange-400">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Mendekati<br>Kadaluwarsa</p>
                    <p class="text-[26px] font-bold text-gray-800 mt-1 leading-tight">{{ $data['mendekatiKadaluwarsa'] }}</p>
                    <p class="text-[11px] mt-1 text-orange-500">{{ $data['itemPerluDiperiksa'] }} item perlu diperiksa</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-xmark text-orange-500 text-[15px]"></i>
                </div>
            </div>
        </div>

        {{-- Stok Rendah --}}
        <div class="stat-card border-l-4 border-l-red-400">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Stok<br>Rendah</p>
                    <p class="text-[26px] font-bold text-gray-800 mt-1 leading-tight">{{ $data['stokRendah'] }}</p>
                    <p class="text-[11px] mt-1 text-red-500">Segera restock</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-[15px]"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: 4 MINI STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#e0f2f1] flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-chart-line text-[#009688] text-[14px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">Rata-rata Nilai</p>
                <p class="text-[14px] font-bold text-gray-800">{{ $data['rataRataNilai'] }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-pills text-orange-500 text-[14px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">Obat Terjual</p>
                <p class="text-[14px] font-bold text-gray-800">{{ $data['obatTerjual'] }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#e0f2f1] flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-file-prescription text-[#009688] text-[14px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">Resep Dilayani</p>
                <p class="text-[14px] font-bold text-gray-800">{{ $data['resepDilayani'] }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-users text-blue-500 text-[14px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">Pelanggan Aktif</p>
                <p class="text-[14px] font-bold text-gray-800">{{ $data['pelangganAktif'] }}</p>
            </div>
        </div>
    </div>

    {{-- ===== ROW 3: CHARTS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Chart Penjualan --}}
        <div class="lg:col-span-2 stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-3">Grafik Penjualan 7 Hari Terakhir</h3>
            <canvas id="chartPenjualan" height="140"></canvas>
        </div>
        {{-- Distribusi Obat --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-3">Distribusi Obat</h3>
            <canvas id="chartDistribusi" height="140"></canvas>
            <div class="mt-3 space-y-1.5">
                @foreach($data['distribusiObat'] as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-sm" style="--bg-color: {{ $item['warna'] }}; background-color: var(--bg-color);"></div>
                        <span class="text-[11px] text-gray-600">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700">{{ $item['persen'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== ROW 4: KONDISI INVENTARIS ===== --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[13px] font-semibold text-gray-700">Kondisi Inventaris Keseluruhan</h3>
            <a href="{{ route('stok-obat') }}" class="text-[11px] text-[#009688] hover:underline font-medium">Detail Inventaris</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#e0f2f1] flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-[#009688] text-[16px]"></i>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-800">{{ number_format($data['stokAman']) }}</p>
                    <p class="text-[11px] text-gray-500">Stok Aman</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-orange-500 text-[16px]"></i>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-800">{{ $data['perluRestock'] }}</p>
                    <p class="text-[11px] text-gray-500">Perlu Restock</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600 text-[16px]"></i>
                </div>
                <div>
                    <p class="text-[18px] font-bold text-gray-800">{{ $data['dalamPemesanan'] }}</p>
                    <p class="text-[11px] text-gray-500">Dalam Pemesanan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 5: TOP 5 OBAT + AKTIVITAS ===== --}}
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

        {{-- Aktivitas Terbaru --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-3">
                @foreach($data['aktivitas'] as $item)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $item['warna'] === 'teal' ? 'bg-[#009688]' : 'bg-orange-500' }}"></div>
                    <div>
                        <p class="text-[12px] text-gray-700 leading-snug">{{ $item['text'] }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $item['waktu'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== ROW 6: KADALUWARSA + STOK RENDAH ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

        {{-- Obat Stok Rendah --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-[14px]"></i>
                    <h3 class="text-[13px] font-semibold text-gray-700">Obat Stok Rendah</h3>
                </div>
                <a href="{{ route('stok-obat') }}" class="text-[11px] text-[#009688] hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @foreach($data['obatStokRendah'] as $obat)
                <div class="py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <p class="text-[12px] font-medium text-gray-700">{{ $obat['nama'] }}</p>
                            <p class="text-[11px] text-gray-400">Kategori: {{ $obat['kategori'] }}</p>
                        </div>
                        <span class="text-[11px] text-red-500 font-medium">Sisa {{ $obat['sisa'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $obat['persen'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== ROW 7: 10 TRANSAKSI TERAKHIR ===== --}}
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
                        <th class="text-left">Pelanggan</th>
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
                        <td>{{ $trx['pelanggan'] }}</td>
                        <td>{{ $trx['item'] }}</td>
                        <td class="font-medium">{{ $trx['total'] }}</td>
                        <td><span class="badge-selesai">{{ $trx['status'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== ROW 8: AKSI CEPAT + KALENDER ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Aksi Cepat --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-3 gap-3">
                <button class="flex flex-col items-center gap-2 py-3 px-2 bg-[#009688] rounded-lg hover:bg-[#00796b] transition-colors">
                    <i class="fa-solid fa-cart-plus text-white text-[18px]"></i>
                    <span class="text-[11px] text-white font-medium text-center leading-tight">Transaksi Baru</span>
                </button>
                <button class="flex flex-col items-center gap-2 py-3 px-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-[#009688] text-[18px]"></i>
                    <span class="text-[11px] text-gray-600 font-medium text-center leading-tight">Cari Obat</span>
                </button>
                <button class="flex flex-col items-center gap-2 py-3 px-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-clock-rotate-left text-[#009688] text-[18px]"></i>
                    <span class="text-[11px] text-gray-600 font-medium text-center leading-tight">Riwayat</span>
                </button>
            </div>
        </div>

        {{-- Kalender Operasional --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-3">Kalender Operasional</h3>
            @php
                $today = now();
                $daysInMonth = $today->daysInMonth;
                $firstDay = \Carbon\Carbon::create($today->year, $today->month, 1)->dayOfWeek;
                $dayNames = ['M','S','S','R','K','J','S'];
            @endphp
            <div class="text-center">
                <p class="text-[11px] font-semibold text-gray-600 mb-2">{{ $today->translatedFormat('F Y') }}</p>
                <div class="grid grid-cols-7 gap-0.5 text-[10px]">
                    @foreach($dayNames as $d)
                        <div class="text-center text-gray-400 font-medium py-1">{{ $d }}</div>
                    @endforeach
                    @for($i = 0; $i < $firstDay; $i++)
                        <div></div>
                    @endfor
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        <div class="text-center py-1 rounded {{ $day == $today->day ? 'bg-[#009688] text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $day }}
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== BAR CHART PENJUALAN =====
    const ctxBar = document.getElementById('chartPenjualan');
    if (ctxBar) {
        const penjualanData = @json($data['penjualanChart']);
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: penjualanData.map(d => d.hari),
                datasets: [{
                    data: penjualanData.map(d => d.nilai),
                    backgroundColor: penjualanData.map((d, i) =>
                        i === penjualanData.length - 1 ? '#009688' : '#b2dfdb'
                    ),
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#9ca3af' }
                    }
                }
            }
        });
    }

    // ===== DONUT CHART DISTRIBUSI =====
    const ctxDonut = document.getElementById('chartDistribusi');
    if (ctxDonut) {
        const distribusiData = @json($data['distribusiObat']);
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: distribusiData.map(d => d.label),
                datasets: [{
                    data: distribusiData.map(d => d.persen),
                    backgroundColor: distribusiData.map(d => d.warna),
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endpush
