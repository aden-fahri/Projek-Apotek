@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-5">

    {{-- ===== HERO BANNER ===== --}}
    <div class="rounded-xl p-6" style="background: linear-gradient(135deg, #009688 0%, #00695c 100%);">
        <h2 class="text-[20px] font-bold text-white">Selamat Datang, {{ $data['userName'] }}</h2>
        <p class="text-[13px] text-white/80 mt-1 mb-4">Ringkasan aktivitas apotek hari ini. Pantau stok dan penjualan untuk menjaga kelancaran operasional.</p>
        <button class="bg-white/20 hover:bg-white/30 text-white text-[12px] font-semibold px-4 py-2 rounded-lg border border-white/30 transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus text-[11px]"></i>
            Transaksi Baru
        </button>
    </div>

    {{-- ===== ROW 1: 3 STAT CARDS BESAR ===== --}}
    <div class="grid grid-cols-3 gap-4">
        {{-- Total Obat --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Total Obat</p>
                    <p class="text-[28px] font-bold text-gray-800 mt-1 leading-none">{{ $data['totalObat'] }}</p>
                    <p class="text-[11px] text-[#009688] mt-2">
                        <i class="fa-solid fa-arrow-trend-up mr-1"></i>{{ $data['trendObat'] }}
                    </p>
                </div>
                <div class="relative w-12 h-12">
                    <svg viewBox="0 0 36 36" class="w-12 h-12 -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#009688" stroke-width="3"
                                stroke-dasharray="75 25" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Stok --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Total Stok</p>
                    <p class="text-[28px] font-bold text-gray-800 mt-1 leading-none">{{ $data['totalStok'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-2">{{ $data['stokNote'] }}</p>
                </div>
                <div class="relative w-12 h-12">
                    <svg viewBox="0 0 36 36" class="w-12 h-12 -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#009688" stroke-width="3"
                                stroke-dasharray="88 12" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Penjualan Hari Ini --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Penjualan Hari Ini</p>
                    <p class="text-[22px] font-bold text-gray-800 mt-1 leading-none">{{ $data['penjualanHariIni'] }}</p>
                    <p class="text-[11px] text-[#10b981] mt-2">
                        <i class="fa-solid fa-arrow-trend-up mr-1"></i>{{ $data['trendPenjualan'] }}
                    </p>
                </div>
                <div class="relative w-12 h-12">
                    <svg viewBox="0 0 36 36" class="w-12 h-12 -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3"
                                stroke-dasharray="60 40" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: 4 MINI STAT CARDS ===== --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="stat-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-[#e0f2f1] flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-receipt text-[#009688] text-[13px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400">Total Transaksi</p>
                <p class="text-[15px] font-bold text-gray-800">{{ $data['totalTransaksi'] }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-[#e0f2f1] flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-chart-line text-[#009688] text-[13px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400">Rata-rata Nilai</p>
                <p class="text-[15px] font-bold text-gray-800">{{ $data['rataRataNilai'] }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-truck text-blue-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400">Supplier Aktif</p>
                <p class="text-[15px] font-bold text-gray-800">{{ $data['supplierAktif'] }}</p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-tag text-purple-500 text-[13px]"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-400">Kategori Obat</p>
                <p class="text-[15px] font-bold text-gray-800">{{ $data['kategoriObat'] }}</p>
            </div>
        </div>
    </div>

    {{-- ===== ROW 3: CHARTS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        {{-- Bar Chart Penjualan --}}
        <div class="col-span-2 stat-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[13px] font-semibold text-gray-700">Grafik Penjualan 7 Hari Terakhir</h3>
                <a href="{{ route('laporan') }}" class="text-[11px] text-[#009688] hover:underline">Detail</a>
            </div>
            <canvas id="chartPenjualan" height="140"></canvas>
        </div>

        {{-- Donut Chart Distribusi --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Distribusi Obat</h3>
            <canvas id="chartDistribusi" height="140"></canvas>
            <div class="mt-3 space-y-1.5">
                @foreach($data['distribusiObat'] as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-sm" style="background:{{ $item['warna'] }}"></div>
                        <span class="text-[11px] text-gray-600">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-700">{{ $item['persen'] }}%</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== ROW 4: KONDISI INVENTARIS ===== --}}
    <div class="grid grid-cols-3 gap-4">
        {{-- Progress bars --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Kondisi Inventaris</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-[12px] text-gray-600">Aman</span>
                        <span class="text-[11px] font-semibold text-[#009688]">{{ $data['inventarisAman'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-[#009688] h-2 rounded-full" style="width:{{ $data['inventarisAman'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-[12px] text-gray-600">Menengah</span>
                        <span class="text-[11px] font-semibold text-orange-500">{{ $data['inventarisMenengah'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-400 h-2 rounded-full" style="width:{{ $data['inventarisMenengah'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-[12px] text-gray-600">Rendah</span>
                        <span class="text-[11px] font-semibold text-red-500">{{ $data['inventarisRendah'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width:{{ $data['inventarisRendah'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue Pendapatan Minggu --}}
        <div class="stat-card text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-1">Pendapatan Minggu Ini</p>
            <p class="text-[22px] font-bold text-gray-800">{{ $data['pendapatanMinggu'] }}</p>
        </div>
        <div class="grid grid-rows-2 gap-4">
            <div class="stat-card text-center py-3">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-1">Pendapatan Bulan Ini</p>
                <p class="text-[18px] font-bold text-gray-800">{{ $data['pendapatanBulan'] }}</p>
            </div>
            <div class="stat-card text-center py-3">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-1">Estimasi Laba</p>
                <p class="text-[18px] font-bold text-[#009688]">{{ $data['estimasiLaba'] }}</p>
            </div>
        </div>
    </div>

    {{-- ===== ROW 5: TOP 5 OBAT + AKTIVITAS ===== --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Top 5 --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Top 5 Obat Terlaris</h3>
            <div class="space-y-3">
                @foreach($data['topObat'] as $obat)
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full bg-[#e0f2f1] flex items-center justify-center flex-shrink-0">
                        <span class="text-[10px] font-bold text-[#009688]">{{ $obat['no'] }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-medium text-gray-700">{{ $obat['nama'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $obat['kategori'] }}</p>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-600">{{ $obat['terjual'] }} Terjual</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-4">
                @foreach($data['aktivitas'] as $item)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $item['warna'] === 'teal' ? 'bg-[#009688]' : 'bg-orange-500' }}"></div>
                    <div>
                        <p class="text-[12px] text-gray-700 leading-snug">{{ $item['text'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $item['waktu'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== ROW 6: ALERT BANNERS ===== --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Kadaluwarsa Alert --}}
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid fa-triangle-exclamation text-orange-500"></i>
                <h3 class="text-[13px] font-semibold text-orange-800">Obat Mendekati Kadaluwarsa</h3>
            </div>
            <p class="text-[12px] text-orange-700">Terdapat 5 item obat yang akan kadaluwarsa dalam 30 hari ke depan. Harap segera lakukan penjualan.</p>
            <a href="{{ route('stok-obat') }}" class="text-[11px] text-[#009688] font-semibold mt-2 inline-block hover:underline">Lihat Detail</a>
        </div>

        {{-- Stok Rendah Alert --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid fa-bell text-gray-500"></i>
                <h3 class="text-[13px] font-semibold text-gray-700">Peringatan Stok Rendah</h3>
            </div>
            <p class="text-[12px] text-gray-600">12 item obat saat ini berada di bawah batas stok minimum. Segera lakukan pemesanan ke supplier.</p>
            <a href="{{ route('supplier') }}" class="text-[11px] text-[#009688] font-semibold mt-2 inline-block hover:underline">Buat PO Baru</a>
        </div>
    </div>

    {{-- ===== ROW 7: 10 TRANSAKSI TERAKHIR ===== --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[13px] font-semibold text-gray-700">10 Transaksi Terakhir</h3>
            <a href="{{ route('riwayat-transaksi') }}" class="text-[11px] text-[#009688] hover:underline font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="text-left">ID Transaksi</th>
                        <th class="text-left">Waktu</th>
                        <th class="text-left">Kasir</th>
                        <th class="text-left">Total</th>
                        <th class="text-left">Metode</th>
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @foreach($data['transaksi'] as $trx)
                    <tr>
                        <td><a href="#" class="text-[#009688] font-medium hover:underline">{{ $trx['id'] }}</a></td>
                        <td class="text-gray-500">{{ $trx['waktu'] }}</td>
                        <td>{{ $trx['kasir'] }}</td>
                        <td class="font-medium">{{ $trx['total'] }}</td>
                        <td class="text-gray-500">{{ $trx['metode'] }}</td>
                        <td><span class="badge-selesai">{{ $trx['status'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== ROW 8: AKSI CEPAT + KALENDER ===== --}}
    <div class="grid grid-cols-2 gap-4">
        {{-- Aksi Cepat --}}
        <div class="stat-card">
            <h3 class="text-[13px] font-semibold text-gray-700 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-5 gap-2">
                <button class="flex flex-col items-center gap-2 py-3 px-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-plus text-[#009688] text-[16px]"></i>
                    <span class="text-[10px] text-gray-600 text-center leading-tight">Tambah Obat</span>
                </button>
                <button class="flex flex-col items-center gap-2 py-3 px-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-arrows-rotate text-[#009688] text-[16px]"></i>
                    <span class="text-[10px] text-gray-600 text-center leading-tight">Update Stok</span>
                </button>
                <button class="flex flex-col items-center gap-2 py-3 px-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-truck text-[#009688] text-[16px]"></i>
                    <span class="text-[10px] text-gray-600 text-center leading-tight">Tambah Supplier</span>
                </button>
                <button class="flex flex-col items-center gap-2 py-3 px-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-print text-[#009688] text-[16px]"></i>
                    <span class="text-[10px] text-gray-600 text-center leading-tight">Cetak Laporan</span>
                </button>
                <button class="flex flex-col items-center gap-2 py-3 px-1 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-user-plus text-[#009688] text-[16px]"></i>
                    <span class="text-[10px] text-gray-600 text-center leading-tight">Tambah Pengguna</span>
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

    {{-- ===== FOOTER STATS ===== --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="grid grid-cols-3 gap-4 divide-x divide-gray-100">
            <div class="px-4 first:pl-0">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Penjualan (Bln Ini)</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-[18px] font-bold text-gray-800">{{ $data['footerPenjualan'] }}</p>
                    <span class="text-[10px] text-[#10b981] flex items-center gap-0.5">
                        <i class="fa-solid fa-arrow-trend-up"></i> 12.5%
                    </span>
                </div>
                <p class="text-[10px] text-gray-400">vs Bulan lalu</p>
            </div>
            <div class="px-4">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Pembelian (Bln Ini)</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-[18px] font-bold text-gray-800">{{ $data['footerPembelian'] }}</p>
                    <span class="text-[10px] text-red-500 flex items-center gap-0.5">
                        <i class="fa-solid fa-arrow-trend-down"></i> 4.2%
                    </span>
                </div>
                <p class="text-[10px] text-gray-400">vs Bulan lalu</p>
            </div>
            <div class="px-4">
                <p class="text-[10px] text-gray-400 uppercase tracking-wide">Pertumbuhan Penjualan</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-[18px] font-bold text-gray-800">{{ $data['footerPertumbuhan'] }}</p>
                    <span class="text-[10px] text-[#10b981] flex items-center gap-0.5">
                        <i class="fa-solid fa-arrow-trend-up"></i> 8.4%
                    </span>
                </div>
                <p class="text-[10px] text-gray-400">vs Bulan lalu</p>
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
                        i === penjualanData.length - 2 ? '#009688' : '#b2dfdb'
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
                            callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'M'
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
