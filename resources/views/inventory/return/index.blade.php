@extends('layouts.admin')

@section('title', 'Daftar Return Obat')

@section('content')
<div class="space-y-5">
    {{-- ===== HEADER ACTIONS ===== --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[20px] font-bold text-gray-800">Riwayat Return Obat</h2>
            <p class="text-[13px] text-gray-400 mt-0.5">Daftar pengembalian obat rusak, kadaluwarsa, atau salah kirim ke supplier.</p>
        </div>
        <a href="{{ route('return-obat.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus text-[11px]"></i> Buat Return Baru
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
                        <th class="text-left w-[15%]">No. Return</th>
                        <th class="text-left w-[18%]">Supplier</th>
                        <th class="text-left w-[33%]">Daftar Item Return</th>
                        <th class="text-left w-[12%]">Tanggal Return</th>
                        <th class="text-left w-[12%]">Alasan</th>
                        <th class="text-right w-[10%]">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @forelse($returns as $ret)
                        <tr class="align-top">
                            <td class="font-semibold text-red-600 pt-4">{{ $ret->return_number }}</td>
                            <td class="pt-4">
                                <p class="font-bold text-gray-800 text-[14px]">{{ $ret->supplier->name ?? 'Supplier' }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $ret->supplier->city ?? '' }}</p>
                            </td>
                            <td class="pt-4">
                                <div class="space-y-1">
                                    @foreach($ret->details as $detail)
                                        <div class="flex items-center justify-between text-[12px] bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded p-1.5">
                                            <span class="font-semibold text-gray-700">{{ $detail->medicine->name ?? 'Obat' }}</span>
                                            <span class="text-gray-400">
                                                Batch: <span class="text-gray-600 font-medium">{{ $detail->medicineStock->batch_number ?? '-' }}</span>
                                                | <span class="text-red-600 font-bold">{{ $detail->quantity }}</span> x Rp {{ number_format($detail->purchase_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-gray-600 pt-4 text-[13px]">{{ $ret->return_date->translatedFormat('d F Y') }}</td>
                            <td class="pt-4">
                                @if($ret->reason === 'expired')
                                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-red-50 text-red-600 rounded-full">Kadaluwarsa</span>
                                @elseif($ret->reason === 'damaged')
                                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-amber-50 text-amber-600 rounded-full">Obat Rusak</span>
                                @elseif($ret->reason === 'wrong_item')
                                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-blue-50 text-blue-600 rounded-full">Salah Kirim</span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-gray-100 text-gray-600 rounded-full">Lainnya</span>
                                @endif
                            </td>
                            <td class="text-right font-bold text-[14px] pt-4 text-gray-800">
                                Rp {{ number_format($ret->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400">Belum ada riwayat pengembalian (return).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $returns->links() }}
        </div>
    </div>
</div>
@endsection
