@extends('layouts.admin')

@section('title', 'Daftar Pembelian (PO)')

@section('content')
<div class="space-y-5">
    {{-- ===== HEADER ACTIONS ===== --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[20px] font-bold text-gray-800">Riwayat Pembelian Obat</h2>
            <p class="text-[13px] text-gray-400 mt-0.5">Daftar transaksi pengadaan obat dari supplier untuk penambahan stok.</p>
        </div>
        <a href="{{ route('purchase-order.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus text-[11px]"></i> Buat PO Baru
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-teal-50 border border-teal-200 text-teal-800 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-teal-600 text-[18px]"></i>
            <p class="text-[13px] font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-red-600 text-[18px]"></i>
            <p class="text-[13px] font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ===== TABLE DATA ===== --}}
    <div class="stat-card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="text-left w-[15%]">No. Invoice PO</th>
                        <th class="text-left w-[20%]">Supplier</th>
                        <th class="text-left w-[35%]">Daftar Item Obat</th>
                        <th class="text-left w-[12%]">Tanggal Order</th>
                        <th class="text-right w-[10%]">Total Nilai</th>
                        <th class="text-center w-[8%]">Status</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @forelse($purchaseOrders as $po)
                        <tr class="align-top">
                            <td class="font-semibold text-[#009688] pt-4">{{ $po->invoice_number }}</td>
                            <td class="pt-4">
                                <p class="font-bold text-gray-800 text-[14px]">{{ $po->supplier->name ?? 'Supplier' }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $po->supplier->city ?? '' }}</p>
                            </td>
                            <td class="pt-4">
                                <div class="space-y-1">
                                    @foreach($po->details as $detail)
                                        <div class="flex items-center justify-between text-[12px] bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded p-1.5">
                                            <span class="font-semibold text-gray-700">{{ $detail->medicine->name ?? 'Obat' }}</span>
                                            <span class="text-gray-400">
                                                Batch: <span class="text-gray-600 font-medium">{{ $detail->batch_number }}</span>
                                                | <span class="text-gray-800 font-bold">{{ $detail->quantity }}</span> x Rp {{ number_format($detail->purchase_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-gray-600 pt-4 text-[13px]">{{ $po->order_date->translatedFormat('d F Y') }}</td>
                            <td class="text-right font-bold text-[14px] pt-4 text-gray-800">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center pt-4">
                                @if($po->status === 'completed')
                                    <span class="badge-selesai">Selesai</span>
                                @elseif($po->status === 'pending')
                                    <span class="badge-hari">Pending</span>
                                @else
                                    <span class="inline-flex items-center bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-0.5 rounded-full">Batal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400">Belum ada riwayat pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
</div>
@endsection
