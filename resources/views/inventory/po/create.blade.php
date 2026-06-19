@extends('layouts.admin')

@section('title', 'Buat PO Baru')

@section('content')
<div class="space-y-5">
    {{-- ===== HEADER ACTIONS ===== --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[20px] font-bold text-gray-800">Buat Purchase Order Baru</h2>
            <p class="text-[13px] text-gray-400 mt-0.5">Tambah stok obat dengan membuat faktur pembelian dari pemasok resmi.</p>
        </div>
        <a href="{{ route('purchase-order') }}" class="border border-gray-200 hover:bg-gray-50 text-gray-600 font-semibold text-[13px] px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
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

    {{-- ===== FORM PO ===== --}}
    <form method="POST" action="{{ route('purchase-order.store') }}" id="poForm">
        @csrf
        
        <div class="grid grid-cols-3 gap-5">
            {{-- Column 1 & 2: Main PO Details --}}
            <div class="col-span-2 space-y-5">
                {{-- Form Header --}}
                <div class="stat-card space-y-4">
                    <h3 class="text-[14px] font-bold text-gray-700 border-b border-gray-100 pb-2">Informasi Transaksi</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-500 mb-1">Supplier / Pemasok <span class="text-red-500">*</span></label>
                            <select name="supplier_id" required class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-500 mb-1">Tanggal Pembelian <span class="text-red-500">*</span></label>
                            <input type="date" name="order_date" required value="{{ old('order_date', date('Y-m-d')) }}" 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Form Items --}}
                <div class="stat-card space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <h3 class="text-[14px] font-bold text-gray-700">Daftar Obat yang Dibeli</h3>
                        <button type="button" onclick="addMedicineRow()" class="text-teal-600 hover:text-teal-800 text-[12px] font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-[10px]"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold border-b border-gray-100">
                                    <th class="text-left pb-2 w-[30%]">Obat</th>
                                    <th class="text-left pb-2 w-[20%]">No. Batch</th>
                                    <th class="text-right pb-2 w-[12%]">Qty</th>
                                    <th class="text-right pb-2 w-[15%]">Harga Beli</th>
                                    <th class="text-left pb-2 pl-3 w-[15%]">ED</th>
                                    <th class="text-right pb-2 w-[15%]">Subtotal</th>
                                    <th class="text-center pb-2 w-[5%]"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsContainer" class="divide-y divide-gray-50">
                                {{-- Rows inserted here dynamically --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Column 3: Summary & Submit --}}
            <div class="space-y-5">
                <div class="stat-card space-y-4 sticky top-5">
                    <h3 class="text-[14px] font-bold text-gray-700 border-b border-gray-100 pb-2">Ringkasan Faktur</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-[13px]">
                            <span class="text-gray-400">Total Item</span>
                            <span class="font-semibold text-gray-700" id="summaryTotalItems">0</span>
                        </div>
                        <div class="flex justify-between text-[14px] border-t border-gray-100 pt-3">
                            <span class="font-bold text-gray-700">Grand Total</span>
                            <span class="font-extrabold text-teal-700 text-[18px]" id="summaryGrandTotal">Rp 0</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-[12px] font-semibold text-gray-500 mb-1">Catatan Tambahan</label>
                        <textarea name="notes" placeholder="Catatan pembelian..." rows="3" 
                                  class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-[13px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors resize-none">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold text-[14px] py-3 rounded-lg transition-colors flex items-center justify-center gap-2 mt-2 shadow-md shadow-teal-600/10">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Transaksi PO
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Template Row (hidden) --}}
<table class="hidden">
    <tbody id="rowTemplate">
        <tr class="item-row align-middle">
            <td class="py-3 pr-2">
                <select name="items[{index}][medicine_id]" required onchange="calculateRowSubtotal(this)"
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
                    <option value="">-- Pilih Obat --</option>
                    @foreach($medicines as $med)
                        <option value="{{ $med->id }}" data-price="{{ $med->purchase_price }}">{{ $med->name }} ({{ $med->code }})</option>
                    @endforeach
                </select>
            </td>
            <td class="py-3 pr-2">
                <input type="text" name="items[{index}][batch_number]" placeholder="BATCH-..." required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
            </td>
            <td class="py-3 pr-2">
                <input type="number" name="items[{index}][quantity]" placeholder="0" min="1" required oninput="calculateRowSubtotal(this)"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-right text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
            </td>
            <td class="py-3 pr-2">
                <input type="number" name="items[{index}][purchase_price]" placeholder="0" min="0" required oninput="calculateRowSubtotal(this)"
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-right text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
            </td>
            <td class="py-3 pr-2 pl-3">
                <input type="date" name="items[{index}][expiry_date]" required
                       class="w-full bg-gray-50 border border-gray-200 rounded-lg px-2 py-1.5 text-[12px] text-gray-700 focus:outline-none focus:border-teal-500 transition-colors">
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
    </tbody>
</table>

@push('scripts')
<script>
    let rowIndex = 0;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Add first row by default
        addMedicineRow();
    });

    function addMedicineRow() {
        const container = document.getElementById('itemsContainer');
        const template = document.getElementById('rowTemplate').innerHTML;
        
        // Replace placeholder index with real index
        const newRowHTML = template.replace(/{index}/g, rowIndex);
        
        container.insertAdjacentHTML('beforeend', newRowHTML);
        rowIndex++;
        
        updateSummary();
    }

    function removeRow(button) {
        const row = button.closest('.item-row');
        row.remove();
        updateSummary();
    }

    function calculateRowSubtotal(input) {
        const row = input.closest('.item-row');
        
        // Auto-fill price from medicine dropdown onchange
        if (input.tagName === 'SELECT') {
            const selectedOption = input.options[input.selectedIndex];
            const defaultPrice = selectedOption.getAttribute('data-price');
            const priceInput = row.querySelector('input[name*="[purchase_price]"]');
            if (defaultPrice && priceInput && !priceInput.value) {
                priceInput.value = Math.round(defaultPrice);
            }
        }

        const qtyInput = row.querySelector('input[name*="[quantity]"]');
        const priceInput = row.querySelector('input[name*="[purchase_price]"]');
        const subtotalDisplay = row.querySelector('.subtotal-display');

        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
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
    document.getElementById('poForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#itemsContainer .item-row');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Faktur PO harus memiliki minimal 1 item obat!');
            return;
        }

        const orderDateVal = document.querySelector('input[name="order_date"]').value;
        if (!orderDateVal) {
            e.preventDefault();
            alert('Tanggal pembelian harus diisi!');
            return;
        }
        
        const orderDate = new Date(orderDateVal);
        orderDate.setHours(0,0,0,0);

        let valid = true;
        let errorMessage = '';

        rows.forEach((row, index) => {
            if (!valid) return;

            const medSelect = row.querySelector('select[name*="[medicine_id]"]');
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[purchase_price]"]');
            const batchInput = row.querySelector('input[name*="[batch_number]"]');
            const expiryInput = row.querySelector('input[name*="[expiry_date]"]');

            if (!medSelect.value) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Silakan pilih obat.`;
            } else if (!batchInput.value.trim()) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Nomor batch tidak boleh kosong.`;
            } else if (parseInt(qtyInput.value) <= 0 || !qtyInput.value) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Jumlah harus minimal 1.`;
            } else if (parseFloat(priceInput.value) < 0 || !priceInput.value) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Harga beli tidak boleh negatif.`;
            } else if (!expiryInput.value) {
                valid = false;
                errorMessage = `Baris ke-${index + 1}: Tanggal kadaluwarsa (ED) harus diisi.`;
            } else {
                const expiryDate = new Date(expiryInput.value);
                expiryDate.setHours(0,0,0,0);
                if (expiryDate < orderDate) {
                    valid = false;
                    errorMessage = `Baris ke-${index + 1}: Tanggal kadaluwarsa (ED) tidak boleh sebelum tanggal pembelian.`;
                }
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
