@extends('layouts.admin')

@section('title', 'Laporan Laba')

@push('styles')
    @vite(['resources/css/laporan.css'])
@endpush

@section('content')
{{-- ===== PAGE HEADER ===== --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Laporan Laba</h1>
        <p class="page-subtitle">Ringkasan laba kotor dan bersih periode berjalan.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <button type="button" onclick="document.getElementById('filter-laba').classList.toggle('d-none')"
                class="btn btn-outline" id="btn-filter-toggle">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
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
                           value="{{ $mulai }}"
                           style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                    <span style="color:var(--color-text-muted);">—</span>
                    <input type="date" name="sampai_tanggal" id="sampai_laba"
                           value="{{ $sampai }}"
                           style="border:none; background:transparent; font-family:inherit; font-size:13.5px; outline:none;">
                </div>
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

{{-- ===== KARTU LABA KOTOR & BERSIH ===== --}}
<div class="laba-grid">

    {{-- LABA KOTOR --}}
    <div class="laba-card">
        <div style="float:right; font-size:28px; color:rgba(13,148,136,0.2);"><i class="fa-solid fa-chart-line"></i></div>
        <div class="laba-card-label">Laba Kotor</div>
        <div class="laba-card-value">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        @if ($pctLabaKotor >= 0)
            <div class="laba-badge">↑ {{ $pctLabaKotor }}% vs bulan lalu</div>
        @else
            <div class="laba-badge" style="background:rgba(239,68,68,.1); color:#dc2626;">↓ {{ abs($pctLabaKotor) }}% vs bulan lalu</div>
        @endif
        <div class="laba-detail">
            Total Penjualan: <strong>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</strong>
            &nbsp;–&nbsp; Total HPP: <strong>Rp {{ number_format($totalHpp, 0, ',', '.') }}</strong>
        </div>
    </div>

    {{-- LABA BERSIH --}}
    <div class="laba-card">
        <div style="float:right; font-size:28px; color:rgba(13,148,136,0.2);"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        <div class="laba-card-label">Laba Bersih</div>
        <div class="laba-card-value">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
        <div class="laba-badge" style="background:rgba(34,197,94,.1); color:#16a34a;">
            ✓ Sudah dikurangi return
        </div>
        <div class="laba-detail">
            Laba Kotor: <strong>Rp {{ number_format($labaKotor, 0, ',', '.') }}</strong>
            &nbsp;–&nbsp; Total Return: <strong>Rp {{ number_format($totalReturn, 0, ',', '.') }}</strong>
        </div>
    </div>

</div>

{{-- ===== TABEL RINCIAN PER OBAT ===== --}}
<div class="card">
    <div class="card-header">
        <span class="card-header-title">Rincian per Obat</span>
        <div class="table-search-wrap">
            <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <form method="GET" action="{{ route('admin.laporan.laba') }}" id="form-cari-obat">
                <input type="hidden" name="mulai_tanggal" value="{{ $mulai }}">
                <input type="hidden" name="sampai_tanggal" value="{{ $sampai }}">
                <input type="text" name="cari_obat" id="input-cari-obat"
                       value="{{ $cariObat }}"
                       placeholder="Cari nama obat..."
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table" id="tabel-rincian-laba">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th style="text-align:right;">Jumlah Terjual</th>
                    <th style="text-align:right;">HPP (AVG)</th>
                    <th style="text-align:right;">Harga Jual (AVG)</th>
                    <th style="text-align:right;">Total Laba</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rincianPerObat as $item)
                <tr>
                    <td>
                        <div class="medicine-name">{{ $item->medicine?->name ?? '—' }}</div>
                        <div class="medicine-sub">
                            {{ $item->medicine?->unit?->abbreviation ?? '' }}
                            @if ($item->medicine?->category)
                                • Kategori: {{ $item->medicine->category->name }}
                            @endif
                        </div>
                    </td>
                    <td style="text-align:right;">
                        {{ number_format($item->total_qty, 0, ',', '.') }}
                        {{ $item->medicine?->unit?->abbreviation ?? '' }}
                    </td>
                    <td style="text-align:right; color:var(--color-text-muted);">
                        Rp {{ number_format($item->avg_hpp, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right; color:var(--color-text-muted);">
                        Rp {{ number_format($item->avg_jual, 0, ',', '.') }}
                    </td>
                    <td style="text-align:right;" class="total-laba">
                        Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon text-gray-300" style="font-size: 48px; margin-bottom: 16px;"><i class="fa-solid fa-box-open"></i></div>
                            <p>Tidak ada data penjualan pada periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($rincianPerObat->hasPages())
    <div class="pagination-wrapper">
        <span>Menampilkan {{ $rincianPerObat->firstItem() }}–{{ $rincianPerObat->lastItem() }} dari {{ $rincianPerObat->total() }} data</span>
        {{ $rincianPerObat->links('vendor.pagination.simple-teal') }}
    </div>
    @endif
</div>
@endsection


