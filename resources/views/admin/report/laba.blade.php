@extends('layouts.admin')

@section('title', 'Buku Besar')

@push('styles')
    @vite(['resources/css/laporan.css'])
    <style>
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 999; display: none;
            align-items: center; justify-content: center; backdrop-filter: blur(4px);
        }
        .modal-content {
            background: #fff; width: 500px; max-width: 90%; border-radius: 8px;
            padding: 20px; position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .modal-header { font-size: 18px; font-weight: 600; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        .modal-close { position: absolute; top: 15px; right: 20px; cursor: pointer; font-size: 20px; color: #64748b; }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .detail-table th, .detail-table td { border-bottom: 1px solid #e2e8f0; padding: 8px; text-align: left; font-size: 13.5px; }
        .detail-table th { background: #f8fafc; font-weight: 600; color: #475569; }
    </style>
@endpush

@section('content')
{{-- ===== PAGE HEADER ===== --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Buku Besar</h1>
        <p class="page-subtitle">Laporan komprehensif seluruh arus kas masuk dan keluar.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.laporan.laba.export', request()->query()) }}"
           id="btn-export-laba"
           class="btn btn-excel">
            <i class="fa-solid fa-file-excel mr-1"></i> Export ke Excel
        </a>
    </div>
</div>

{{-- ===== FILTER PERIODE ===== --}}
<div class="filter-section" id="filter-laba">
    <form method="GET" action="{{ route('admin.laporan.laba') }}" id="form-filter-laba">
        <div class="filter-grid" style="align-items:center;">
            <div class="filter-field">
                <label class="filter-label">Periode Tanggal</label>
                <div class="filter-date-range">
                    <span><i class="fa-regular fa-calendar text-gray-400"></i></span>
                    <input type="date" name="mulai_tanggal" id="mulai_laba"
                           value="{{ $mulai }}" onclick="this.showPicker()"
                           style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                    <span style="color:var(--color-text-muted);">—</span>
                    <input type="date" name="sampai_tanggal" id="sampai_laba"
                           value="{{ $sampai }}" onclick="this.showPicker()"
                           style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                </div>
            </div>
            <div class="filter-field">
                <label class="filter-label">Jenis Transaksi</label>
                <select name="jenis" class="form-input" style="width: 100%; font-size: 13.5px;">
                    <option value="Semua" {{ $filterJenis == 'Semua' ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="Jual Obat" {{ $filterJenis == 'Jual Obat' ? 'selected' : '' }}>Penjualan (Masuk)</option>
                    <option value="Beli Obat" {{ $filterJenis == 'Beli Obat' ? 'selected' : '' }}>Pembelian (Keluar)</option>
                    <option value="Retur Obat" {{ $filterJenis == 'Retur Obat' ? 'selected' : '' }}>Retur (Masuk)</option>
                </select>
            </div>
            <div class="filter-field" style="justify-content:flex-end;">
                <label class="filter-label" style="visibility:hidden;">Aksi</label>
                <div class="filter-actions">
                    <a href="{{ route('admin.laporan.laba') }}" class="btn btn-outline" id="btn-reset-laba">Reset</a>
                    <button type="submit" class="btn btn-primary" id="btn-terapkan-laba">Terapkan</button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ===== KARTU RINGKASAN ===== --}}
<div class="laba-grid">
    {{-- UANG MASUK --}}
    <div class="laba-card" style="border-top: 4px solid #10b981;">
        <div style="float:right; font-size:28px; color:rgba(16,185,129,0.2);"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="laba-card-label">Total Uang Masuk</div>
        <div class="laba-card-value" style="color: #059669;">Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
        <div class="laba-detail">Penjualan Kasir & Retur</div>
    </div>

    {{-- UANG KELUAR --}}
    <div class="laba-card" style="border-top: 4px solid #ef4444;">
        <div style="float:right; font-size:28px; color:rgba(239,68,68,0.2);"><i class="fa-solid fa-arrow-trend-down"></i></div>
        <div class="laba-card-label">Total Uang Keluar</div>
        <div class="laba-card-value" style="color: #dc2626;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
        <div class="laba-detail">Pembelian ke Supplier</div>
    </div>

    {{-- SALDO AKHIR --}}
    <div class="laba-card" style="border-top: 4px solid #3b82f6;">
        <div style="float:right; font-size:28px; color:rgba(59,130,246,0.2);"><i class="fa-solid fa-wallet"></i></div>
        <div class="laba-card-label">Saldo Akhir</div>
        <div class="laba-card-value" style="color: #2563eb;">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</div>
        <div class="laba-detail">Posisi Saldo per Tanggal {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</div>
    </div>
</div>

{{-- ===== TABEL BUKU BESAR ===== --}}
<div class="card">
    <div class="card-header">
        <span class="card-header-title">Rincian Transaksi</span>
    </div>

    <div class="table-wrapper">
        <table class="table" id="tabel-buku-besar">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Ref</th>
                    <th>Jenis</th>
                    <th>Keterangan</th>
                    <th>Oleh</th>
                    <th style="text-align:right;">Masuk</th>
                    <th style="text-align:right;">Keluar</th>
                    <th style="text-align:right;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f8fafc; font-weight: 600;">
                    <td colspan="5" style="text-align:right; font-size: 13px;">SALDO AWAL SEBELUM {{ strtoupper(\Carbon\Carbon::parse($mulai)->translatedFormat('d M Y')) }}</td>
                    <td style="text-align:right; color: #94a3b8;">-</td>
                    <td style="text-align:right; color: #94a3b8;">-</td>
                    <td style="text-align:right; color: #0f172a; font-size: 14px;">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>

                @forelse ($bukuBesar as $item)
                <tr>
                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="javascript:void(0)" onclick="showDetailModal('{{ $item['referensi'] }}', '{{ json_encode($item['rincian']) }}')" style="color: #2563eb; font-weight: 600; text-decoration: underline;">
                            {{ $item['referensi'] }}
                        </a>
                    </td>
                    <td>
                        @if($item['jenis'] == 'Jual Obat')
                            <span class="badge" style="background:#dcfce7; color:#166534; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Penjualan</span>
                        @elseif($item['jenis'] == 'Beli Obat')
                            <span class="badge" style="background:#fee2e2; color:#991b1b; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pembelian</span>
                        @else
                            <span class="badge" style="background:#fef9c3; color:#854d0e; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Retur</span>
                        @endif
                    </td>
                    <td>{{ $item['keterangan'] }}</td>
                    <td>{{ $item['oleh'] }}</td>
                    <td style="text-align:right; color: #16a34a; font-weight: 500;">
                        {{ $item['debit'] > 0 ? 'Rp ' . number_format($item['debit'], 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align:right; color: #dc2626; font-weight: 500;">
                        {{ $item['kredit'] > 0 ? 'Rp ' . number_format($item['kredit'], 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align:right; font-weight: 600; color: #334155; font-size: 13.5px;">
                        Rp {{ number_format($item['saldo'], 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="text-align: center; padding: 40px;">
                            <div class="empty-icon text-gray-300" style="font-size: 48px; margin-bottom: 16px;"><i class="fa-solid fa-folder-open"></i></div>
                            <p style="color: #64748b;">Tidak ada transaksi pada periode dan jenis ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal-overlay" id="detailModal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeDetailModal()">&times;</span>
        <div class="modal-header">Rincian Transaksi: <span id="modalRef"></span></div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody id="detailBody">
                <!-- Data akan diisi oleh JS -->
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showDetailModal(ref, rincianJson) {
        document.getElementById('modalRef').innerText = ref;
        let rincian = [];
        try {
            rincian = JSON.parse(rincianJson);
        } catch(e) {
            console.error(e);
        }

        let html = '';
        if(rincian.length === 0) {
            html = '<tr><td colspan="4" style="text-align:center;">Tidak ada rincian obat.</td></tr>';
        } else {
            rincian.forEach(item => {
                let harga = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.harga);
                let subtotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.subtotal);
                
                html += `<tr>
                    <td>${item.nama_obat}</td>
                    <td style="text-align:center;">${item.qty}</td>
                    <td style="text-align:right;">${harga}</td>
                    <td style="text-align:right; font-weight:600;">${subtotal}</td>
                </tr>`;
            });
        }
        document.getElementById('detailBody').innerHTML = html;
        document.getElementById('detailModal').style.display = 'flex';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }
</script>
@endpush
