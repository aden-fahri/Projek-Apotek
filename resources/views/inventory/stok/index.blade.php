@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.kasir')

@section('title', 'Stok & Inventaris')

@section('content')
<div class="space-y-5">
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

    <!-- Header Section -->
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div class="flex flex-col gap-1">
            <h1 class="text-[28px] font-bold text-gray-800 leading-tight">Stok Obat</h1>
            <p class="text-[14px] font-medium text-gray-500">Daftar stok obat fisik apotek berdasarkan batch dan masa kadaluwarsa</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('data-obat') }}" class="bg-teal-600 hover:bg-teal-700 text-white font-bold text-[14px] px-5 py-3 rounded-lg shadow-sm transition-all flex items-center gap-2">
            <i class="fa-solid fa-gear"></i> Kelola Data Obat
        </a>
        @endif
    </div>

    {{-- ===== HEADER STATISTICS ===== --}}
    <div class="grid grid-cols-4 gap-4">
        {{-- Total Jenis --}}
        <div class="stat-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Total Jenis Obat</p>
                    <p class="text-[28px] font-extrabold text-gray-800 mt-1.5 leading-none">{{ $stats['total_jenis'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(13, 148, 136, 0.1);">
                    <i class="fa-solid fa-pills text-[18px]" style="color: #0d9488;"></i>
                </div>
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="stat-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Stok Menipis</p>
                    <p class="text-[28px] font-extrabold mt-1.5 leading-none {{ $stats['stok_menipis'] > 0 ? 'text-amber-500' : 'text-gray-800' }}">{{ $stats['stok_menipis'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.1);">
                    <i class="fa-solid fa-triangle-exclamation text-[18px]" style="color: #f59e0b;"></i>
                </div>
            </div>
        </div>

        {{-- Kadaluwarsa H-30 --}}
        <div class="stat-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Kadaluwarsa (≤30 H)</p>
                    <p class="text-[28px] font-extrabold mt-1.5 leading-none {{ $stats['kadaluwarsa_30'] > 0 ? 'text-red-500' : 'text-gray-800' }}">{{ $stats['kadaluwarsa_30'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(239, 68, 68, 0.1);">
                    <i class="fa-solid fa-calendar-xmark text-[18px]" style="color: #ef4444;"></i>
                </div>
            </div>
        </div>

        {{-- Nilai Aset Stok --}}
        <div class="stat-card p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">Nilai Aset Stok</p>
                    <p class="text-[20px] font-extrabold text-teal-700 mt-2 leading-none">Rp {{ number_format($stats['nilai_aset'], 0, ',', '.') }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: rgba(16, 185, 129, 0.1);">
                    <i class="fa-solid fa-money-bill-wave text-[18px]" style="color: #10b981;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER ACTION ===== --}}
    <div class="stat-card p-4">
        <form method="GET" action="{{ route('stok-obat') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[14px]"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama obat, kode, atau kategori..." 
                       class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg pr-4 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors"
                       style="padding-left: 36px;">
            </div>
            
            <div class="w-[200px]">
                <select name="category" class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ $category == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="h-10 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-5 rounded-lg transition-colors flex items-center gap-2">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            @if($search || $category)
                <a href="{{ route('stok-obat') }}" class="h-10 border border-gray-200 hover:bg-gray-50 text-gray-600 font-semibold text-[13px] px-4 rounded-lg transition-colors flex items-center justify-center">
                    Reset
                </a>
            @endif


        </form>
    </div>

    {{-- ===== TABLE DATA ===== --}}
    <div class="stat-card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="table-header">
                    <tr>
                        <th class="text-left w-[12%]">Kode Obat</th>
                        <th class="text-left w-[33%]">Informasi Obat</th>
                        <th class="text-left w-[15%]">Kategori</th>
                        <th class="text-right w-[12%]">Harga Jual</th>
                        <th class="text-center w-[10%]">Stok</th>
                        <th class="text-center w-[10%]">Status</th>
                        <th class="text-center w-[8%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @forelse($medicines as $med)
                        @php
                            $isExpiringSoon = false;
                            if ($med->nearest_expiry) {
                                $daysToExpiry = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($med->nearest_expiry), false);
                                if ($daysToExpiry <= 30) {
                                    $isExpiringSoon = true;
                                }
                            }
                            
                            $isLowStock = $med->stock_status === 'Stok Rendah';
                            $isOut = $med->stock_status === 'Habis';
                            
                            // Row class or border style based on conditions
                            $rowClass = '';
                            $borderClass = '';
                            if ($isOut || $isExpiringSoon) {
                                $rowClass = 'bg-red-50/10';
                                $borderClass = 'border-l-[4px] border-l-red-500 pl-3';
                            } elseif ($isLowStock) {
                                $rowClass = 'bg-amber-50/10';
                                $borderClass = 'border-l-[4px] border-l-amber-500 pl-3';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="font-semibold text-gray-700 {{ $borderClass }}">{{ $med->medicine_code }}</td>
                            <td>
                                <div>
                                    <p class="font-bold text-gray-800 text-[14px]">{{ $med->medicine_name }}</p>
                                    @if($med->nearest_expiry)
                                        <p class="text-[11px] text-gray-400 mt-0.5">
                                            Kadaluwarsa Terdekat: 
                                            <span class="font-semibold {{ $isExpiringSoon ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                {{ \Carbon\Carbon::parse($med->nearest_expiry)->translatedFormat('d F Y') }}
                                            </span>
                                        </p>
                                    @else
                                        <p class="text-[11px] text-gray-400 mt-0.5">Belum ada batch stok</p>
                                    @endif

                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-teal-700 bg-teal-50 px-2 py-0.5 rounded border border-teal-100">
                                            <i class="fa-solid fa-layer-group"></i>
                                            {{ isset($batches[$med->medicine_id]) ? count($batches[$med->medicine_id]) : 0 }} Batch
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-gray-500 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">
                                            Min. Stok: {{ $med->min_stock }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 text-[11px] font-semibold bg-gray-100 text-gray-600 rounded-full">
                                    {{ $med->category_name ?: 'Lainnya' }}
                                </span>
                            </td>
                            <td class="text-right font-medium">Rp {{ number_format($med->selling_price, 0, ',', '.') }}</td>
                            <td class="text-center font-bold text-[14px]">
                                <span class="{{ $isLowStock || $isOut ? 'text-amber-600' : 'text-gray-800' }}">
                                    {{ $med->total_stock }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-normal"> / {{ $med->unit_name ?: 'Unit' }}</span>
                            </td>
                            <td class="text-center">
                                @if($isExpiringSoon)
                                    <span class="inline-flex items-center bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-0.5 rounded-full" title="Obat akan kadaluwarsa dalam 30 hari!">ED ≤30 H</span>
                                @elseif($isOut)
                                    <span class="inline-flex items-center bg-red-100 text-red-600 text-xs font-semibold px-2.5 py-0.5 rounded-full">Habis</span>
                                @elseif($isLowStock)
                                    <span class="badge-hari">Menipis</span>
                                @else
                                    <span class="badge-selesai">Aman</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2.5">
                                    {{-- Detail --}}
                                    <button type="button" 
                                            data-id="{{ $med->medicine_id }}"
                                            data-name="{{ $med->medicine_name }}"
                                            data-code="{{ $med->medicine_code }}"
                                            class="text-teal-600 hover:text-teal-800 transition-colors btn-detail-batch cursor-pointer" 
                                            title="Lihat Detail Batch">
                                        <i class="fa-regular fa-file-lines text-[16px]"></i>
                                    </button>


                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Belum ada data obat yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="mt-4">
            {{ $medicines->links() }}
        </div>
    </div>
</div>

{{-- ===== DETAIL BATCH MODAL ===== --}}
<div id="batchModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="left: 220px; z-index: 9999;">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
    
    {{-- Modal Card --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-lg w-full m-4 relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modalCard" style="max-width: 500px; width: 100%;">
        {{-- Header --}}
        <div class="bg-teal-700 p-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-[16px]" id="modalTitle">Detail Batch Obat</h3>
                <p class="text-[11px] text-white/80 mt-0.5" id="modalSubtitle">Kode: -</p>
            </div>
            <button onclick="closeModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-[18px]"></i>
            </button>
        </div>
        
        {{-- Body --}}
        <div class="p-5 max-h-[400px] overflow-y-auto">
            <div id="modalLoading" class="text-center py-6 text-gray-400">
                <i class="fa-solid fa-circle-notch fa-spin text-teal-600 text-[24px]"></i>
                <p class="text-[12px] mt-2">Memuat detail batch...</p>
            </div>
            
            <div id="modalContent" class="hidden space-y-4">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 text-[11px] uppercase tracking-wider text-gray-400 font-semibold">
                            <th class="text-left pb-2">No. Batch</th>
                            <th class="text-center pb-2">Sisa Stok</th>
                            <th class="text-left pb-2 pl-4">Tanggal Kadaluwarsa</th>
                            <th class="text-center pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody id="batchTableBody" class="text-[13px] text-gray-700 divide-y divide-gray-50">
                        {{-- Batches will be inserted here dynamically --}}
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end">
            <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>



<div id="batches-data-container" data-batches="{{ json_encode($batches) }}" class="hidden"></div>

@push('scripts')
<script>
    // Embed the batches data from backend
    const batchesData = JSON.parse(document.getElementById('batches-data-container').dataset.batches);

    // Use event delegation on document so click events trigger even if page loaded dynamically
    document.addEventListener('click', function(e) {
        const detailBtn = e.target.closest('.btn-detail-batch');
        if (detailBtn) {
            const id = detailBtn.dataset.id;
            const name = detailBtn.dataset.name;
            const code = detailBtn.dataset.code;
            showBatchDetail(id, name, code);
            return;
        }
    });

    function showBatchDetail(medicineId, medicineName, medicineCode) {
        const modal = document.getElementById('batchModal');
        const card = document.getElementById('modalCard');
        const title = document.getElementById('modalTitle');
        const subtitle = document.getElementById('modalSubtitle');
        const tableBody = document.getElementById('batchTableBody');
        const loading = document.getElementById('modalLoading');
        const content = document.getElementById('modalContent');
        
        title.innerText = medicineName;
        subtitle.innerText = 'Kode: ' + medicineCode;
        tableBody.innerHTML = '';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);

        loading.classList.remove('hidden');
        content.classList.add('hidden');

        // Fetch batches for this medicine
        const medicineBatches = batchesData[medicineId] || [];

        if (medicineBatches.length > 0) {
            medicineBatches.forEach(batch => {
                const expiryDate = new Date(batch.expiry_date);
                const today = new Date();
                const diffTime = expiryDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                let badgeClass = 'badge-selesai';
                let statusText = 'Aman';
                
                if (diffDays <= 0) {
                    badgeClass = 'bg-red-100 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full';
                    statusText = 'Expired';
                } else if (diffDays <= 30) {
                    badgeClass = 'bg-red-50 text-red-500 text-xs font-semibold px-2 py-0.5 rounded-full';
                    statusText = 'ED ≤30 H';
                } else if (diffDays <= 90) {
                    badgeClass = 'badge-hari';
                    statusText = 'ED ≤90 H';
                }

                // Format date manually
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                const formattedDate = expiryDate.toLocaleDateString('id-ID', options);

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="py-3 font-semibold text-gray-700">${batch.batch_number || '-'}</td>
                    <td class="py-3 text-center font-bold text-[14px]">${batch.quantity}</td>
                    <td class="py-3 pl-4 text-gray-500">${formattedDate}</td>
                    <td class="py-3 text-center"><span class="${badgeClass}">${statusText}</span></td>
                `;
                tableBody.appendChild(tr);
            });
        } else {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="4" class="py-6 text-center text-gray-400">Tidak ada batch stok tersedia saat ini.</td>`;
            tableBody.appendChild(tr);
        }

        loading.classList.add('hidden');
        content.classList.remove('hidden');
        content.className = 'block space-y-4';
    }

    function closeModal() {
        const modal = document.getElementById('batchModal');
        const card = document.getElementById('modalCard');
        
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>
@endpush
@endsection
