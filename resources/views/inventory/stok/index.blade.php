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

            @if(auth()->user()->role === 'admin')
                <div class="ml-auto flex items-center gap-2">
                    <button type="button" onclick="openAddCategoryModal()" class="h-10 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-4 rounded-lg transition-colors flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-folder-plus text-[11px]"></i> Tambah Kategori
                    </button>
                    <button type="button" onclick="openAddMedicineModal()" class="h-10 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[13px] px-4 rounded-lg transition-colors flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-pills text-[11px]"></i> Tambah Produk Obat
                    </button>
                    <a href="{{ route('purchase-order.create') }}" class="h-10 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-4 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-plus text-[11px]"></i> Tambah Stok
                    </a>
                </div>
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

                                    @if(auth()->user()->role === 'admin')
                                        {{-- Edit --}}
                                        @php
                                            $firstBatch = isset($batches[$med->medicine_id]) ? $batches[$med->medicine_id]->first() : null;
                                            $firstBatchNumber = $firstBatch ? $firstBatch->batch_number : '';
                                            $firstExpiryDate = $firstBatch ? ($firstBatch->expiry_date instanceof \Carbon\Carbon ? $firstBatch->expiry_date->format('Y-m-d') : substr($firstBatch->expiry_date, 0, 10)) : '';
                                        @endphp
                                        <button type="button"
                                                data-id="{{ $med->medicine_id }}"
                                                data-name="{{ $med->medicine_name }}"
                                                data-code="{{ $med->medicine_code }}"
                                                data-category_id="{{ $med->category_id }}"
                                                data-group_id="{{ $med->group_id }}"
                                                data-unit_id="{{ $med->unit_id }}"
                                                data-purchase_price="{{ $med->purchase_price }}"
                                                data-selling_price="{{ $med->selling_price }}"
                                                data-min_stock="{{ $med->min_stock }}"
                                                data-total_stock="{{ $med->total_stock }}"
                                                data-first_batch_number="{{ $firstBatchNumber }}"
                                                data-first_expiry_date="{{ $firstExpiryDate }}"
                                                data-description="{{ $med->description }}"
                                                data-requires_prescription="{{ $med->requires_prescription ? '1' : '0' }}"
                                                class="text-amber-500 hover:text-amber-700 transition-colors btn-edit-medicine cursor-pointer"
                                                title="Edit Obat">
                                            <i class="fa-regular fa-pen-to-square text-[16px]"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('medicines.destroy', $med->medicine_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk obat {{ addslashes($med->medicine_name) }}? Semua batch stok obat ini juga akan terhapus.')" style="margin: 0; display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Obat">
                                                <i class="fa-regular fa-trash-can text-[16px]"></i>
                                            </button>
                                        </form>
                                    @endif
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
<div id="batchModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
    
    {{-- Modal Card --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-lg w-full m-4 relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modalCard">
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

{{-- ===== TAMBAH PRODUK OBAT MODAL ===== --}}
<div id="addMedicineModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddMedicineModal()"></div>
    
    {{-- Modal Card --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-lg w-full m-4 relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="addMedicineCard">
        {{-- Header --}}
        <div class="bg-teal-700 p-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-[16px]">Tambah Produk Obat Baru</h3>
                <p class="text-[11px] text-white/80 mt-0.5">Daftarkan produk obat baru ke dalam sistem master data</p>
            </div>
            <button onclick="closeAddMedicineModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-[18px]"></i>
            </button>
        </div>
        
        <form action="{{ route('medicines.store') }}" method="POST">
            @csrf
            {{-- Body --}}
            <div class="p-5 max-h-[500px] overflow-y-auto space-y-4 text-left">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Kode Obat (Opsional)</label>
                        <input type="text" name="code" placeholder="Misal: OBT-001 (Auto jika kosong)" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Obat <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Nama obat" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Kategori <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-1.5">
                            <select name="category_id" id="add_category_id" required class="flex-1 h-10 bg-gray-50 border border-gray-200 rounded-lg px-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                                <option value="">Pilih</option>
                                @foreach($categoriesList as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openAddCategoryModal('add_category_id')" class="w-10 h-10 bg-teal-50 text-teal-600 hover:bg-teal-100 border border-teal-200 rounded-lg flex items-center justify-center transition-colors cursor-pointer" title="Tambah Kategori Baru">
                                <i class="fa-solid fa-plus text-[14px]"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Golongan <span class="text-red-500">*</span></label>
                        <select name="group_id" required class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                            <option value="">Pilih</option>
                            @foreach($groupsList as $grp)
                                <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Satuan <span class="text-red-500">*</span></label>
                        <select name="unit_id" required class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                            <option value="">Pilih</option>
                            @foreach($unitsList as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Harga Beli <span class="text-red-500">*</span></label>
                        <input type="number" name="purchase_price" required min="0" placeholder="Rp" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Harga Jual <span class="text-red-500">*</span></label>
                        <input type="number" name="selling_price" required min="0" placeholder="Rp" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Stok Minimal (Tanda Menipis) <span class="text-red-500">*</span></label>
                        <input type="number" name="min_stock" required min="0" value="10" placeholder="Min" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-3 mt-3">
                    <p class="text-[12px] font-bold text-gray-700 mb-2">Stok Awal / Tersedia (Opsional)</p>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Stok Tersedia</label>
                            <input type="number" name="initial_stock" id="add_initial_stock" min="0" value="0" placeholder="0" 
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">No. Batch</label>
                            <input type="text" name="batch_number" id="add_batch_number" placeholder="SA-OBT..." 
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tgl Kadaluwarsa <span id="add_expiry_star" class="text-red-500 hidden">*</span></label>
                            <input type="date" name="expiry_date" id="add_expiry_date" 
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan / Deskripsi</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi obat..." 
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_prescription" id="add_requires_prescription" value="1" 
                           class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <label for="add_requires_prescription" class="text-[13px] font-medium text-gray-600 cursor-pointer select-none">Membutuhkan Resep Dokter</label>
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeAddMedicineModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors">
                    Simpan Obat
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT PRODUK OBAT MODAL ===== --}}
<div id="editMedicineModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditMedicineModal()"></div>
    
    {{-- Modal Card --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-lg w-full m-4 relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="editMedicineCard">
        {{-- Header --}}
        <div class="bg-teal-700 p-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-[16px]">Edit Produk Obat</h3>
                <p class="text-[11px] text-white/80 mt-0.5" id="editMedicineSubtitle">Ubah data master produk obat</p>
            </div>
            <button onclick="closeEditMedicineModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-[18px]"></i>
            </button>
        </div>
        
        <form action="" method="POST" id="editMedicineForm">
            @csrf
            @method('PUT')
            {{-- Body --}}
            <div class="p-5 max-h-[500px] overflow-y-auto space-y-4 text-left">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Kode Obat <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="edit_code" required 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Obat <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Kategori <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-1.5">
                            <select name="category_id" id="edit_category_id" required class="flex-1 h-10 bg-gray-50 border border-gray-200 rounded-lg px-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                                <option value="">Pilih</option>
                                @foreach($categoriesList as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openAddCategoryModal('edit_category_id')" class="w-10 h-10 bg-teal-50 text-teal-600 hover:bg-teal-100 border border-teal-200 rounded-lg flex items-center justify-center transition-colors cursor-pointer" title="Tambah Kategori Baru">
                                <i class="fa-solid fa-plus text-[14px]"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Golongan <span class="text-red-500">*</span></label>
                        <select name="group_id" id="edit_group_id" required class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                            <option value="">Pilih</option>
                            @foreach($groupsList as $grp)
                                <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Satuan <span class="text-red-500">*</span></label>
                        <select name="unit_id" id="edit_unit_id" required class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                            <option value="">Pilih</option>
                            @foreach($unitsList as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Harga Beli <span class="text-red-500">*</span></label>
                        <input type="number" name="purchase_price" id="edit_purchase_price" required min="0" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Harga Jual <span class="text-red-500">*</span></label>
                        <input type="number" name="selling_price" id="edit_selling_price" required min="0" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Stok Minimal (Tanda Menipis) <span class="text-red-500">*</span></label>
                        <input type="number" name="min_stock" id="edit_min_stock" required min="0" 
                               class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-3 mt-3">
                    <p class="text-[12px] font-bold text-gray-700 mb-2">Stok Tersedia & Batch Pertama</p>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Stok Tersedia</label>
                            <input type="number" name="initial_stock" id="edit_initial_stock" min="0" placeholder="0" 
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">No. Batch</label>
                            <input type="text" name="batch_number" id="edit_batch_number" placeholder="No. Batch" 
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tgl Kadaluwarsa <span id="edit_expiry_star" class="text-red-500 hidden">*</span></label>
                            <input type="date" name="expiry_date" id="edit_expiry_date" 
                                   class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Mengubah nilai di atas akan memperbarui sisa stok batch aktif pertama dari obat ini.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Keterangan / Deskripsi</label>
                    <textarea name="description" id="edit_description" rows="2" placeholder="Deskripsi obat..." 
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="requires_prescription" id="edit_requires_prescription" value="1" 
                           class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <label for="edit_requires_prescription" class="text-[13px] font-medium text-gray-600 cursor-pointer select-none">Membutuhkan Resep Dokter</label>
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeEditMedicineModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== TAMBAH KATEGORI BARU MODAL ===== --}}
<div id="addCategoryModal" class="fixed inset-0 z-[60] flex items-center justify-center hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddCategoryModal()"></div>
    
    {{-- Modal Card --}}
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 max-w-md w-full m-4 relative z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="addCategoryCard">
        {{-- Header --}}
        <div class="bg-teal-700 p-4 text-white flex justify-between items-center">
            <div>
                <h3 class="font-bold text-[16px]">Tambah Kategori Baru</h3>
                <p class="text-[11px] text-white/80 mt-0.5">Buat kategori baru untuk mengelompokkan obat-obatan</p>
            </div>
            <button type="button" onclick="closeAddCategoryModal()" class="text-white/80 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-[18px]"></i>
            </button>
        </div>
        
        <form id="addCategoryForm" onsubmit="submitCategoryForm(event)">
            @csrf
            {{-- Body --}}
            <div class="p-5 space-y-4 text-left">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="category_name" required placeholder="Misal: Antibiotik, Vitamin" 
                           class="w-full h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Deskripsi / Keterangan</label>
                    <textarea name="description" id="category_description" rows="3" placeholder="Keterangan singkat kategori..." 
                              class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors"></textarea>
                </div>
                <div id="category_error_alert" class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-3 flex items-start gap-2.5 hidden">
                    <i class="fa-solid fa-triangle-exclamation text-red-600 mt-0.5"></i>
                    <p class="text-[12px] font-medium" id="category_error_text">Terjadi kesalahan</p>
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeAddCategoryModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" id="btn_submit_category" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[13px] px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <span id="spinner_category" class="hidden"><i class="fa-solid fa-circle-notch fa-spin text-white"></i></span>
                    Simpan Kategori
                </button>
            </div>
        </form>
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

        const editBtn = e.target.closest('.btn-edit-medicine');
        if (editBtn) {
            const data = {
                id: editBtn.dataset.id,
                code: editBtn.dataset.code,
                name: editBtn.dataset.name,
                categoryId: editBtn.dataset.category_id,
                groupId: editBtn.dataset.group_id,
                unitId: editBtn.dataset.unit_id,
                purchasePrice: editBtn.dataset.purchase_price,
                sellingPrice: editBtn.dataset.selling_price,
                minStock: editBtn.dataset.min_stock,
                totalStock: editBtn.dataset.total_stock,
                firstBatchNumber: editBtn.dataset.first_batch_number,
                firstExpiryDate: editBtn.dataset.first_expiry_date,
                description: editBtn.dataset.description,
                requiresPrescription: editBtn.dataset.requires_prescription
            };
            openEditMedicineModal(data);
            return;
        }
    });

    // Add Medicine Modal functions
    function openAddMedicineModal() {
        const modal = document.getElementById('addMedicineModal');
        const card = document.getElementById('addMedicineCard');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddMedicineModal() {
        const modal = document.getElementById('addMedicineModal');
        const card = document.getElementById('addMedicineCard');
        
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Edit Medicine Modal functions
    function openEditMedicineModal(data) {
        const modal = document.getElementById('editMedicineModal');
        const card = document.getElementById('editMedicineCard');
        
        // Populate inputs
        document.getElementById('editMedicineForm').action = '/stok-obat/' + data.id;
        document.getElementById('editMedicineSubtitle').innerText = 'Kode: ' + data.code;
        document.getElementById('edit_code').value = data.code;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_category_id').value = data.categoryId;
        document.getElementById('edit_group_id').value = data.groupId;
        document.getElementById('edit_unit_id').value = data.unitId;
        document.getElementById('edit_purchase_price').value = Math.round(data.purchasePrice);
        document.getElementById('edit_selling_price').value = Math.round(data.sellingPrice);
        document.getElementById('edit_min_stock').value = data.minStock;
        document.getElementById('edit_initial_stock').value = data.totalStock || 0;
        document.getElementById('edit_batch_number').value = data.firstBatchNumber || '';
        document.getElementById('edit_expiry_date').value = data.firstExpiryDate || '';
        document.getElementById('edit_description').value = data.description || '';
        document.getElementById('edit_requires_prescription').checked = data.requiresPrescription === '1';
        
        // Dynamic validate run immediately
        const event = new Event('input');
        document.getElementById('edit_initial_stock').dispatchEvent(event);

        modal.classList.remove('hidden');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditMedicineModal() {
        const modal = document.getElementById('editMedicineModal');
        const card = document.getElementById('editMedicineCard');
        
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Add Category AJAX functions
    let categoryTargetDropdownId = null;

    function openAddCategoryModal(targetDropdownId = null) {
        categoryTargetDropdownId = targetDropdownId;
        
        const modal = document.getElementById('addCategoryModal');
        const card = document.getElementById('addCategoryCard');
        
        // Reset form
        document.getElementById('addCategoryForm').reset();
        document.getElementById('category_error_alert').classList.add('hidden');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeAddCategoryModal() {
        const modal = document.getElementById('addCategoryModal');
        const card = document.getElementById('addCategoryCard');
        
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function submitCategoryForm(event) {
        event.preventDefault();
        
        const form = document.getElementById('addCategoryForm');
        const spinner = document.getElementById('spinner_category');
        const submitBtn = document.getElementById('btn_submit_category');
        const errorAlert = document.getElementById('category_error_alert');
        const errorText = document.getElementById('category_error_text');
        
        // Show spinner and disable button
        spinner.classList.remove('hidden');
        submitBtn.disabled = true;
        errorAlert.classList.add('hidden');
        
        const formData = new FormData(form);
        
        fetch("{{ route('categories.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            spinner.classList.add('hidden');
            submitBtn.disabled = false;
            
            if (res.status === 200 && res.body.success) {
                const category = res.body.category;
                
                // Add to dropdowns
                const dropdowns = ['add_category_id', 'edit_category_id'];
                dropdowns.forEach(id => {
                    const select = document.getElementById(id);
                    if (select) {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.text = category.name;
                        select.appendChild(option);
                    }
                });

                // Also add to category filter select (name is the value there)
                const filterSelect = document.querySelector('select[name="category"]');
                if (filterSelect) {
                    const option = document.createElement('option');
                    option.value = category.name;
                    option.text = category.name;
                    filterSelect.appendChild(option);
                }
                
                // Select in active dropdown
                if (categoryTargetDropdownId) {
                    const select = document.getElementById(categoryTargetDropdownId);
                    if (select) {
                        select.value = category.id;
                    }
                }
                
                closeAddCategoryModal();
                showToast(res.body.message || 'Kategori baru berhasil ditambahkan!');
            } else {
                errorText.innerText = res.body.message || 'Terjadi kesalahan saat menyimpan kategori.';
                errorAlert.classList.remove('hidden');
            }
        })
        .catch(error => {
            spinner.classList.add('hidden');
            submitBtn.disabled = false;
            errorText.innerText = 'Koneksi gagal atau terjadi kesalahan internal server.';
            errorAlert.classList.remove('hidden');
            console.error('Error:', error);
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 bg-teal-800 text-white rounded-xl shadow-lg px-5 py-3.5 z-[100] flex items-center gap-3 transform translate-y-10 opacity-0 transition-all duration-300';
        toast.innerHTML = `
            <i class="fa-solid fa-circle-check text-emerald-400 text-[18px]"></i>
            <span class="text-[13px] font-medium">${message}</span>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        }, 10);
        
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }

    // Toggle expiry date required status based on stock value
    function toggleExpiryRequired(stockId, expiryId, starId) {
        const stockInput = document.getElementById(stockId);
        const expiryInput = document.getElementById(expiryId);
        const star = document.getElementById(starId);
        if (!stockInput || !expiryInput) return;

        const checkValue = () => {
            const stockVal = parseInt(stockInput.value) || 0;
            if (stockVal > 0) {
                expiryInput.setAttribute('required', 'required');
                if (star) star.classList.remove('hidden');
            } else {
                expiryInput.removeAttribute('required');
                if (star) star.classList.add('hidden');
            }
        };

        stockInput.addEventListener('input', checkValue);
        stockInput.addEventListener('change', checkValue);
        checkValue();
    }

    toggleExpiryRequired('add_initial_stock', 'add_expiry_date', 'add_expiry_star');
    toggleExpiryRequired('edit_initial_stock', 'edit_expiry_date', 'edit_expiry_star');

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
