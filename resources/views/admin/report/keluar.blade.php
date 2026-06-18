@extends('layouts.admin')

@section('title', 'Laporan Keluar (Uang Keluar)')

@push('styles')
    @vite(['resources/css/laporan.css'])
@endpush

@section('content')
{{-- ===== PAGE HEADER ===== --}}
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="#">Laporan</a>
            <span>›</span>
            <span>Pembelian</span>
        </div>
        <h1 class="page-title">Laporan Keluar (Pembelian)</h1>
        <p class="page-subtitle">Laporan uang keluar untuk pembelian stok obat dari suplayer.</p>
    </div>
    <a href="{{ route('admin.laporan.keluar.export', request()->query()) }}"
       id="btn-export-keluar"
       class="btn btn-excel">
        <i class="fa-solid fa-file-excel mr-1"></i> Export ke Excel
    </a>
</div>

{{-- ===== LAYOUT: FILTER + SUMMARY ===== --}}
<div style="display: grid; grid-template-columns: 1fr 280px; gap: 16px; margin-bottom: 20px; align-items: start;">

    {{-- FILTER --}}
    <div class="filter-section" style="margin-bottom: 0;">
        <div class="filter-title"><i class="fa-solid fa-filter mr-1"></i> Filter Laporan</div>
        <form method="GET" action="{{ route('admin.laporan.keluar') }}" id="form-filter-keluar">
            <div class="filter-grid">

                <div class="filter-field">
                    <label class="filter-label">Periode Tanggal</label>
                    <div class="filter-date-range">
                        <span><i class="fa-regular fa-calendar text-gray-400"></i></span>
                        <input type="date" name="mulai_tanggal" id="mulai_keluar"
                               value="{{ $mulai }}"
                               style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                        <span style="color:var(--color-text-muted);">—</span>
                        <input type="date" name="sampai_tanggal" id="sampai_keluar"
                               value="{{ $sampai }}"
                               style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                    </div>
                </div>

                <div class="filter-field">
                    <label class="filter-label">Suplayer</label>
                    <select name="supplier_id" class="filter-select" id="select-supplier">
                        <option value="">Semua Suplayer</option>
                        @foreach ($daftarSupplier as $supplier)
                            <option value="{{ $supplier->id }}" {{ $supplierId == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field" style="justify-content:flex-end;">
                    <label class="filter-label" style="visibility:hidden;">Aksi</label>
                    <div class="filter-actions">
                        <a href="{{ route('admin.laporan.keluar') }}" class="btn btn-outline" id="btn-reset-keluar">Reset</a>
                        <button type="submit" class="btn btn-primary" id="btn-terapkan-keluar"><i class="fa-solid fa-check mr-1"></i> Terapkan</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- SUMMARY CARD --}}
    <div class="summary-card">
        <div class="summary-icon text-[#0D9488]" style="margin-bottom:12px; font-size: 24px;"><i class="fa-solid fa-cart-shopping"></i></div>
        <div class="summary-label">TOTAL UANG KELUAR</div>
        <div class="summary-value">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
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
        <span class="card-header-title">Data Pembelian (Uang Keluar)</span>
        <span style="font-size:12.5px; color:var(--color-text-muted);">
            Menampilkan {{ $pembelian->firstItem() ?? 0 }}–{{ $pembelian->lastItem() ?? 0 }}
            dari {{ $pembelian->total() }} data
        </span>
    </div>

    <div class="table-wrapper">
        <table class="table" id="tabel-laporan-keluar">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor Invoice PO</th>
                    <th>Suplayer</th>
                    <th style="text-align:right;">Total Pembelian</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pembelian as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->purchase_date)->translatedFormat('d M Y') }}</td>
                    <td>
                        <a href="#" class="invoice-link">{{ $item->invoice_number }}</a>
                    </td>
                    <td>{{ $item->supplier?->name ?? '—' }}</td>
                    <td class="amount">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @if ($item->status === 'selesai')
                            <span class="badge badge-success">● Lunas</span>
                        @elseif ($item->status === 'pending')
                            <span class="badge badge-warning">● Tempo</span>
                        @else
                            <span class="badge badge-danger">● Dibatalkan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon text-gray-300" style="font-size: 48px; margin-bottom: 16px;"><i class="fa-solid fa-box-open"></i></div>
                            <p>Tidak ada data pembelian pada periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($pembelian->hasPages())
    <div class="pagination-wrapper">
        <span>Menampilkan {{ $pembelian->firstItem() }}–{{ $pembelian->lastItem() }} dari {{ $pembelian->total() }} data</span>
        {{ $pembelian->links('vendor.pagination.simple-teal') }}
    </div>
    @endif
</div>
@endsection


