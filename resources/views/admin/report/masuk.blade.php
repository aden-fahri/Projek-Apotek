@extends('layouts.laporan')

@section('title', 'Laporan Masuk (Uang Masuk)')

@section('content')
{{-- ===== PAGE HEADER ===== --}}
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="#">Laporan</a>
            <span>›</span>
            <span>Penjualan</span>
        </div>
        <h1 class="page-title">Laporan Masuk (Penjualan)</h1>
        <p class="page-subtitle">Laporan uang masuk dari transaksi penjualan kasir.</p>
    </div>
    <a href="{{ route('admin.laporan.masuk.export', request()->query()) }}"
       id="btn-export-masuk"
       class="btn btn-excel">
        ⬇ Export ke Excel
    </a>
</div>

{{-- ===== LAYOUT: FILTER + SUMMARY ===== --}}
<div style="display: grid; grid-template-columns: 1fr 280px; gap: 16px; margin-bottom: 20px; align-items: start;">

    {{-- FILTER --}}
    <div class="filter-section" style="margin-bottom: 0;">
        <div class="filter-title">⊟ Filter Laporan</div>
        <form method="GET" action="{{ route('admin.laporan.masuk') }}" id="form-filter-masuk">
            <div class="filter-grid">

                <div class="filter-field">
                    <label class="filter-label">Periode Tanggal</label>
                    <div class="filter-date-range" style="gap:6px;">
                        <span>📅</span>
                        <input type="date" name="mulai_tanggal" id="mulai_masuk"
                               value="{{ $mulai }}"
                               style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                        <span style="color:var(--color-text-muted);">—</span>
                        <input type="date" name="sampai_tanggal" id="sampai_masuk"
                               value="{{ $sampai }}"
                               style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="filter-label">Kasir</label>
                    <select name="kasir_id" class="filter-select" id="select-kasir">
                        <option value="">Semua Kasir</option>
                        @foreach ($daftarKasir as $kasir)
                            <option value="{{ $kasir->id }}" {{ $kasirId == $kasir->id ? 'selected' : '' }}>
                                {{ $kasir->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label class="filter-label">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="filter-select" id="select-metode">
                        <option value="">Semua Metode</option>
                        @foreach ($daftarMetode as $metode)
                            <option value="{{ $metode }}" {{ $metodePembayaran == $metode ? 'selected' : '' }}>
                                {{ $metode }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field" style="justify-content:flex-end;">
                    <label class="filter-label" style="visibility:hidden;">Aksi</label>
                    <div class="filter-actions">
                        <a href="{{ route('admin.laporan.masuk') }}" class="btn btn-outline" id="btn-reset-masuk">Reset</a>
                        <button type="submit" class="btn btn-primary" id="btn-terapkan-masuk">⊟ Terapkan</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- SUMMARY CARD --}}
    <div class="summary-card">
        <div class="summary-icon" style="margin-bottom:12px;">📋</div>
        <div class="summary-label">TOTAL UANG MASUK</div>
        <div class="summary-value">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
        <div class="summary-change">
            @if ($persentasePerubahan >= 0)
                <span class="up">↑ {{ $persentasePerubahan }}%</span>
            @else
                <span class="down">↓ {{ abs($persentasePerubahan) }}%</span>
            @endif
            dari bulan lalu
        </div>
    </div>

</div>

{{-- ===== TABEL DATA ===== --}}
<div class="card">
    <div class="card-header">
        <span class="card-header-title">Data Penjualan (Uang Masuk)</span>
        <span style="font-size:12.5px; color:var(--color-text-muted);">
            Menampilkan {{ $transaksi->firstItem() ?? 0 }}–{{ $transaksi->lastItem() ?? 0 }}
            dari {{ $transaksi->total() }} data
        </span>
    </div>

    <div class="table-wrapper">
        <table class="table" id="tabel-laporan-masuk">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor Invoice</th>
                    <th>Kasir</th>
                    <th>Metode Bayar</th>
                    <th style="text-align:right;">Total Penjualan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $item)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($item->transaction_date)->translatedFormat('d M Y') }}
                        <br>
                        <span style="font-size:11.5px; color:var(--color-text-muted);">
                            {{ \Carbon\Carbon::parse($item->transaction_date)->format('H:i') }}
                        </span>
                    </td>
                    <td>
                        <a href="#" class="invoice-link">{{ $item->invoice_number }}</a>
                    </td>
                    <td>{{ $item->kasir?->name ?? '—' }}</td>
                    <td>{{ $item->payment_method }}</td>
                    <td class="amount">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @if ($item->status === 'selesai')
                            <span class="badge badge-success">● Selesai</span>
                        @elseif ($item->status === 'pending')
                            <span class="badge badge-warning">● Menunggu</span>
                        @else
                            <span class="badge badge-danger">● Dibatalkan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>Tidak ada data penjualan pada periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($transaksi->hasPages())
    <div class="pagination-wrapper">
        <span>Menampilkan {{ $transaksi->firstItem() }}–{{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} data</span>
        {{ $transaksi->links('vendor.pagination.simple-teal') }}
    </div>
    @endif
</div>
@endsection
