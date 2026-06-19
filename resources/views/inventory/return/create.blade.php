@extends('layouts.admin')

@section('title', 'Buat Return Baru')

@section('content')
<div class="space-y-5">
    {{-- ===== HEADER ACTIONS ===== --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[20px] font-bold text-gray-800">Buat Return Obat Baru</h2>
            <p class="text-[13px] text-gray-400 mt-0.5">Kembalikan obat kadaluwarsa, rusak, atau salah kirim ke supplier resmi.</p>
        </div>
        <a href="{{ route('return-obat') }}" class="border border-gray-200 hover:bg-gray-50 text-gray-600 font-semibold text-[13px] px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-[11px]"></i> Kembali
        </a>
    </div>

    {{-- Error Messages --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-red-600 text-[18px]"></i>
            <p class="text-[13px] font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4">
            <div class="flex items-center gap-3 mb-2">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-[18px]"></i>
                <p class="text-[13px] font-bold">Harap perbaiki kesalahan berikut:</p>
            </div>
            <ul class="list-disc list-inside text-[12px] text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== FORM RETURN ===== --}}
    <form method="POST" action="{{ route('return-obat.store') }}" id="returnForm">
        @csrf
        
        <div class="grid grid-cols-3 gap-5">
            {{-- Column 1 & 2: Form Details --}}
            <div class="col-span-2 space-y-5">
                {{-- Form Header --}}
                <div class="stat-card space-y-4">
                    <h3 class="text-[14px] font-bold text-gray-700 border-b border-gray-100 pb-2">Informasi Pengembalian</h3>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-500 mb-1">Supplier Tujuan <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-500 mb-1">Tanggal Return <span class="text-red-500">*</span></label>
                            <input type="date" name="return_date" required value="{{ old('return_date', date('Y-m-d')) }}" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-500 mb-1">Alasan Return <span class="text-red-500">*</span></label>
                            <select name="reason" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                                <option value="expired" {{ old('reason') == 'expired' ? 'selected' : '' }}>Kadaluwarsa (Expired)</option>
                                <option value="damaged" {{ old('reason') == 'damaged' ? 'selected' : '' }}>Kemasan/Obat Rusak</option>
                                <option value="wrong_item" {{ old('reason') == 'wrong_item' ? 'selected' : '' }}>Salah Kirim Barang</option>
                                <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Form Items --}}
                <div class="stat-card space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <h3 class="text-[14px] font-bold text-gray-700">Daftar Batch Obat yang Dikembalikan</h3>
                        <button type="button" onclick="addReturnRow()" class="text-teal-600 hover:text-teal-800 text-[12px] font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Baris
                        </button>
                    </div>

                    <div id="loadingMessage" class="text-center py-6 text-gray-400">
                        <i class="fa-solid fa-circle-notch fa-spin text-teal-600 text-[20px]"></i>
                        <p class="text-[12px] mt-2">Memuat daftar batch stok yang tersedia dari database...</p>
                    </div>

                    <div id="tableContainer" class="hidden overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold border-b border-gray-100">
                                    <th class="text-left pb-2 w-[45%]">Pilih Batch Stok</th>
                                    <th class="text-right pb-2 w-[12%]">Kuantitas Stok</th>
                                    <th class="text-right pb-2 w-[15%]">Qty Return</th>
                                    <th class="text-right pb-2 w-[15%]">Harga Beli</th>
                                    <th class="text-right pb-2 w-[15%]">Subtotal</th>
                                    <th class="text-center pb-2 w-[5%]"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer" class="divide-y divide-gray-50">
                                {{-- Rows inserted dynamically --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Column 3: Summary & Submit --}}
            <div class="space-y-5">
                <div class="stat-card space-y-4 sticky top-5">
                    <h3 class="text-[14px] font-bold text-gray-700 border-b border-gray-100 pb-2">Ringkasan Return</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-[13px]">
                            <span class="text-gray-400">Total Item</span>
                            <span class="font-semibold text-gray-700" id="summaryTotalItems">0</span>
                        </div>
                        <div class="flex justify-between text-[14px] border-t border-gray-100 pt-3">
                            <span class="font-bold text-gray-700">Total Nilai Return</span>
                            <span class="font-extrabold text-red-600 text-[18px]" id="summaryGrandTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-[12px] font-semibold text-gray-500 mb-1">Catatan Tambahan</label>
                        <textarea name="notes" placeholder="Catatan pengembalian obat..." rows="3" 
                                  class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-[14px] py-3 rounded-lg transition-colors flex items-center justify-center gap-2 mt-2 shadow-md shadow-red-600/10">
                        <i class="fa-solid fa-rotate-left"></i> Simpan Transaksi Return
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let rowIndex = 0;
    let availableStocks = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch available stocks from API
        fetch("{{ route('api.available-stocks') }}")
            .then(res => res.json())
            .then(data => {
                availableStocks = data;
                
                document.getElementById('loadingMessage').classList.add('hidden');
                document.getElementById('tableContainer').classList.remove('hidden');
                
                // Add first row
                addReturnRow();
            })
            .catch(err => {
                document.getElementById('loadingMessage').innerHTML = 
                    `<p class="text-red-500 font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i> Gagal memuat data stok dari server.</p>`;
                console.error(err);
            });
    });

    function addReturnRow() {
        const container = document.getElementById('itemsContainer');
        
        let selectOptions = '<option value="">-- Pilih Batch Obat --</option>';
        availableStocks.forEach(stock => {
            selectOptions += `<option value="${stock.id}" data-qty="${stock.quantity}" data-price="${stock.purchase_price}">
                ${stock.name} (${stock.code}) - Batch: ${stock.batch_number} (Stok: ${stock.quantity}, Exp: ${stock.expiry_date})
            </option>`;
        });

        const rowHTML = `
            <tr class="item-row align-middle">
                <td class="py-3 pr-2">
                    <select name="items[${rowIndex}][medicine_stock_id]" required onchange="onBatchSelected(this)"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        ${selectOptions}
                    </select>
                </td>
                <td class="py-3 pr-2 text-right font-semibold text-gray-600 text-[13px] stock-qty-display">
                    -
                </td>
                <td class="py-3 pr-2">
                    <input type="number" name="items[${rowIndex}][quantity]" placeholder="0" min="1" required oninput="calculateRowSubtotal(this)" disabled
                           class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-right text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                </td>
                <td class="py-3 pr-2 text-right font-medium text-gray-500 text-[13px] purchase-price-display">
                    -
                </td>
                <td class="py-3 text-right font-bold text-gray-700 text-[13px] subtotal-display">
                    Rp 0
                </td>
                <td class="py-3 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 transition-colors">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        `;
        
        container.insertAdjacentHTML('beforeend', rowHTML);
        rowIndex++;
        
        updateSummary();
    }

    function removeRow(button) {
        const row = button.closest('.item-row');
        row.remove();
        updateSummary();
    }

    function onBatchSelected(select) {
        const row = select.closest('.item-row');
        const qtyDisplay = row.querySelector('.stock-qty-display');
        const priceDisplay = row.querySelector('.purchase-price-display');
        const qtyInput = row.querySelector('input[name*="[quantity]"]');
        
        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const maxQty = parseInt(selectedOption.getAttribute('data-qty'));
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            
            qtyDisplay.innerText = maxQty;
            priceDisplay.innerText = 'Rp ' + price.toLocaleString('id-ID');
            priceDisplay.setAttribute('data-value', price);
            
            qtyInput.removeAttribute('disabled');
            qtyInput.setAttribute('max', maxQty);
            qtyInput.value = '';
        } else {
            qtyDisplay.innerText = '-';
            priceDisplay.innerText = '-';
            priceDisplay.removeAttribute('data-value');
            qtyInput.setAttribute('disabled', 'true');
            qtyInput.value = '';
        }
        
        calculateRowSubtotal(qtyInput);
    }

    function calculateRowSubtotal(qtyInput) {
        const row = qtyInput.closest('.item-row');
        const priceDisplay = row.querySelector('.purchase-price-display');
        const subtotalDisplay = row.querySelector('.subtotal-display');

        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceDisplay.getAttribute('data-value')) || 0;
        const maxQty = parseFloat(row.querySelector('.stock-qty-display').innerText) || 0;

        // Constraint check
        if (qty > maxQty) {
            qtyInput.value = maxQty;
            calculateRowSubtotal(qtyInput);
            return;
        }

        const subtotal = qty * price;
        subtotalDisplay.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        subtotalDisplay.setAttribute('data-value', subtotal);

        updateSummary();
    }

    function updateSummary() {
        const rows = document.querySelectorAll('#itemsContainer .item-row');
        let totalItems = 0;
        let grandTotal = 0;

        rows.forEach(row => {
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const qty = parseFloat(qtyInput.value) || 0;
            totalItems += qty;

            const subtotalDisplay = row.querySelector('.subtotal-display');
            const subtotal = parseFloat(subtotalDisplay.getAttribute('data-value')) || 0;
            grandTotal += subtotal;
        });

        document.getElementById('summaryTotalItems').innerText = totalItems;
        document.getElementById('summaryGrandTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    // Client-side validation before submit
    document.getElementById('returnForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#itemsContainer .item-row');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Form return harus memiliki minimal 1 item obat!');
            return;
        }

        let valid = true;
        let errorMessage = '';

        rows.forEach((row, index) => {
            if (!valid) return;

            const batchSelect = row.querySelector('select[name*="[medicine_stock_id]"]');
            const qtyInput = row.querySelector('input[name*="[quantity]"]');

            if (!batchSelect.value) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Silakan pilih batch obat yang dikembalikan.`;
            } else if (parseInt(qtyInput.value) <= 0 || !qtyInput.value) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Jumlah return harus minimal 1.`;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert(errorMessage);
        }
    });
</script>
@endpush
@endsection
