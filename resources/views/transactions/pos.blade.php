@extends('layouts.kasir')

@section('title', 'Sistem Kasir (POS)')

@push('styles')
<style>
    /* RESET & DASAR */
    * { box-sizing: border-box; }
    
    .pos-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        height: calc(100vh - 110px);
        font-family: 'Quicksand', sans-serif;
    }

    /* SCROLLBAR CUSTOM */
    .pos-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .pos-scroll::-webkit-scrollbar-track { background: transparent; }
    .pos-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .pos-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* LAYOUT KIRI (PRODUK) */
    .pos-left {
        display: flex;
        flex-direction: column;
        gap: 15px;
        overflow: hidden;
    }
    
    .pos-filter-bar {
        background: #ffffff;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
        display: flex;
        gap: 15px;
        flex-shrink: 0;
    }
    
    .pos-search-wrapper {
        position: relative;
        flex: 1;
    }
    .pos-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    .pos-input {
        width: 100%;
        padding: 10px 15px 10px 35px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
        font-family: 'Quicksand', sans-serif;
        font-size: 13px;
        outline: none;
        transition: 0.2s;
    }
    .pos-input:focus {
        border-color: #009688;
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(0, 150, 136, 0.1);
    }

    /* GRID PRODUK */
    .pos-product-grid-wrapper {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
        padding-bottom: 20px;
    }
    .pos-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }
    
    /* KARTU PRODUK */
    .pos-product-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        overflow: hidden;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        position: relative;
        padding: 12px;
        min-height: 120px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pos-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        border-color: rgba(0, 150, 136, 0.4);
    }
    .pos-badge-stock {
        position: absolute;
        top: 12px;
        right: 12px;
        color: white;
        font-size: 10px;
        font-weight: bold;
        padding: 3px 8px;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .badge-ok { background: #009688; }
    .badge-warn { background: #f59e0b; }
    .badge-danger { background: #ef4444; }

    .pos-product-info {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding-top: 15px;
    }
    .pos-product-cat {
        font-size: 10px;
        color: #9ca3af;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .pos-product-name {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 8px 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .pos-product-card:hover .pos-product-name {
        color: #009688;
    }
    .pos-product-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 8px;
        border-top: 1px solid #f9fafb;
    }
    .pos-product-price {
        font-size: 14px;
        font-weight: 700;
        color: #009688;
    }
    .pos-btn-add {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: #e0f2f1;
        color: #009688;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .pos-product-card:hover .pos-btn-add {
        background: #009688;
        color: white;
    }

    /* EMPTY STATE */
    .pos-empty-state {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px 20px;
        text-align: center;
    }
    .pos-empty-state.show { display: flex; }
    .pos-empty-icon {
        width: 70px;
        height: 70px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        border: 1px solid #f3f4f6;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .pos-empty-icon i { font-size: 28px; color: #d1d5db; }
    .pos-empty-title { font-size: 15px; font-weight: bold; color: #374151; margin: 0 0 5px 0; }
    .pos-empty-desc { font-size: 13px; color: #6b7280; margin: 0; }

    /* LAYOUT KANAN (KERANJANG) */
    .pos-right {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .pos-cart-header {
        padding: 15px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .pos-cart-title {
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pos-cart-title i { color: #009688; }
    .pos-cart-badge {
        background: #009688;
        color: white;
        font-size: 11px;
        font-weight: bold;
        padding: 4px 10px;
        border-radius: 20px;
    }

    /* ITEM KERANJANG */
    .pos-cart-body {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        background: #ffffff;
    }
    .pos-cart-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: none;
        flex-direction: column;
        gap: 12px;
    }
    .pos-cart-list.show { display: flex; }
    .pos-cart-item {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        transition: 0.2s;
    }
    .pos-cart-item:hover { border-color: rgba(0, 150, 136, 0.3); }
    .pos-cart-item-info { flex: 1; overflow: hidden; padding-right: 10px; }
    .pos-cart-item-name {
        font-size: 13px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pos-cart-item-price { font-size: 12px; font-weight: 700; color: #009688; }
    
    .pos-cart-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }
    .pos-qty-controls {
        display: flex;
        align-items: center;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 3px;
    }
    .pos-qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        transition: 0.2s;
    }
    .pos-qty-btn:hover { background: #f3f4f6; color: #1f2937; }
    .pos-qty-val {
        font-size: 13px;
        font-weight: 700;
        width: 24px;
        text-align: center;
        color: #1f2937;
    }
    .pos-btn-remove {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 10px;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
    }
    .pos-btn-remove:hover { text-decoration: underline; }

    /* CHECKOUT AREA */
    .pos-checkout-area {
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
        padding: 15px 20px;
    }
    .pos-summary-box {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 15px;
    }
    .pos-summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 12px;
    }
    .pos-summary-label { color: #6b7280; font-weight: 600; }
    .pos-summary-val { color: #374151; font-weight: 700; }
    .pos-summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #cbd5e1;
        align-items: center;
    }
    .pos-summary-total-label { font-size: 14px; font-weight: 800; color: #1f2937; text-transform: uppercase; }
    .pos-summary-total-val { font-size: 18px; font-weight: 800; color: #009688; }

    .pos-form-group { margin-bottom: 12px; }
    .pos-form-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .pos-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    
    .pos-change-box {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        padding: 10px 15px;
        display: none;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .pos-change-box.show { display: flex; }
    .pos-change-label { color: #047857; font-size: 13px; font-weight: 700; }
    .pos-change-val { color: #059669; font-size: 16px; font-weight: 800; }

    .pos-btn-submit {
        width: 100%;
        background: #009688;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        font-family: 'Quicksand', sans-serif;
    }
    .pos-btn-submit:hover { background: #00796b; box-shadow: 0 4px 12px rgba(0,150,136,0.3); }
    .pos-btn-submit:disabled { background: #cbd5e1; color: #94a3b8; cursor: not-allowed; box-shadow: none; }

    /* SUCCESS MODAL NATIVE CSS */
    .pos-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .pos-modal-overlay.show { display: flex; }
    .pos-modal-card {
        background: white;
        width: 100%;
        max-width: 380px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        position: relative;
    }
    .pos-modal-header-bg { height: 80px; background: #009688; }
    .pos-modal-content {
        padding: 25px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: -60px;
        text-align: center;
    }
    .pos-modal-icon {
        width: 60px; height: 60px;
        background: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; color: #10b981;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }
    .pos-modal-title { font-size: 18px; font-weight: 800; color: #1f2937; margin: 0 0 5px 0; }
    .pos-modal-subtitle { font-size: 13px; color: #6b7280; margin: 0 0 20px 0; }
    .pos-modal-inv { color: #009688; font-weight: 700; }
    
    .pos-modal-receipt {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .pos-modal-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
    .pos-modal-label { color: #6b7280; }
    .pos-modal-val { color: #1f2937; font-weight: 700; }
    .pos-modal-total-row {
        display: flex; justify-content: space-between;
        margin-top: 10px; padding-top: 10px;
        border-top: 1px dashed #cbd5e1;
    }
    .pos-modal-total-val { color: #10b981; font-weight: 800; font-size: 14px; }

    .pos-modal-actions {
        display: flex; width: 100%; gap: 10px;
    }
    .pos-btn-secondary {
        flex: 1; padding: 10px; border-radius: 8px;
        background: #f1f5f9; color: #475569;
        border: none; font-weight: 700; font-size: 13px; cursor: pointer;
        font-family: 'Quicksand', sans-serif;
    }
    .pos-btn-secondary:hover { background: #e2e8f0; }
    .pos-btn-primary {
        flex: 1; padding: 10px; border-radius: 8px;
        background: #009688; color: white;
        border: none; font-weight: 700; font-size: 13px; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 5px;
        font-family: 'Quicksand', sans-serif;
    }
    .pos-btn-primary:hover { background: #00796b; }

    @media (max-width: 992px) {
        .pos-wrapper {
            grid-template-columns: 1fr;
            height: auto;
        }
        .pos-right {
            min-height: 500px;
        }
    }
</style>
@endpush

@section('content')
<div class="pos-wrapper">

    <!-- KIRI: PRODUK & PENCARIAN -->
    <div class="pos-left">
        <!-- Filter Bar -->
        <div class="pos-filter-bar">
            <div class="pos-search-wrapper">
                <i class="fa-solid fa-search pos-search-icon"></i>
                <input type="text" id="searchInput" class="pos-input" placeholder="Cari nama atau kode obat...">
            </div>
            <select id="categoryFilter" class="pos-input" style="width: 200px;">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Grid Obat -->
        <div class="pos-product-grid-wrapper pos-scroll">
            <div id="productGrid" class="pos-product-grid">
                @foreach($medicines as $med)
                <div class="pos-product-card" onclick="addToCart({{ $med->id }}, '{{ addslashes($med->name) }}', {{ $med->selling_price }}, {{ $med->current_stock }})">
                    @if($med->current_stock <= 0)
                        <span class="pos-badge-stock badge-danger">Habis</span>
                    @elseif($med->current_stock <= 10)
                        <span class="pos-badge-stock badge-warn">Sisa {{ $med->current_stock }}</span>
                    @else
                        <span class="pos-badge-stock badge-ok">Stok: {{ $med->current_stock }}</span>
                    @endif
                    <div class="pos-product-info">
                        <div class="pos-product-cat">{{ $med->category->name ?? 'Umum' }}</div>
                        <h4 class="pos-product-name">{{ $med->name }}</h4>
                        <div class="pos-product-footer">
                            <span class="pos-product-price">Rp {{ number_format($med->selling_price, 0, ',', '.') }}</span>
                            <button class="pos-btn-add"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State Pencarian -->
            <div id="noProductMsg" class="pos-empty-state">
                <div class="pos-empty-icon"><i class="fa-solid fa-box-open"></i></div>
                <h3 class="pos-empty-title">Obat Tidak Ditemukan</h3>
                <p class="pos-empty-desc">Silakan coba dengan kata kunci lain.</p>
            </div>
        </div>
    </div>

    <!-- KANAN: KERANJANG & CHECKOUT -->
    <div class="pos-right">
        <div class="pos-cart-header">
            <h3 class="pos-cart-title"><i class="fa-solid fa-basket-shopping"></i> Daftar Belanja</h3>
            <span class="pos-cart-badge" id="cartCountBadge">0 Item</span>
        </div>

        <div class="pos-cart-body pos-scroll">
            <!-- Empty Cart -->
            <div id="emptyCartMsg" class="pos-empty-state" style="display: flex; height: 100%;">
                <div class="pos-empty-icon" style="background: transparent; border: none; box-shadow: none;">
                    <i class="fa-solid fa-cart-arrow-down" style="font-size: 40px; color: #e5e7eb;"></i>
                </div>
                <h3 class="pos-empty-title">Keranjang Kosong</h3>
                <p class="pos-empty-desc">Pilih obat dari daftar untuk menambahkannya.</p>
            </div>

            <!-- List Cart -->
            <ul id="cartList" class="pos-cart-list"></ul>
        </div>

        <div class="pos-checkout-area">
            <div class="pos-summary-box">
                <div class="pos-summary-row">
                    <span class="pos-summary-label">Subtotal</span>
                    <span class="pos-summary-val" id="summarySubtotal">Rp 0</span>
                </div>
                <div class="pos-summary-row">
                    <span class="pos-summary-label">Pajak (0%)</span>
                    <span class="pos-summary-val" id="summaryTax">Rp 0</span>
                </div>
                <div class="pos-summary-total">
                    <span class="pos-summary-total-label">Total Bayar</span>
                    <span class="pos-summary-total-val" id="summaryTotal">Rp 0</span>
                </div>
            </div>

            <form id="checkoutForm" onsubmit="processCheckout(event)">
                <div class="pos-form-group">
                    <label class="pos-form-label">Nama Pelanggan (Opsional)</label>
                    <input type="text" id="customerName" class="pos-input" placeholder="Pelanggan Umum">
                </div>
                
                <div class="pos-grid-2">
                    <div class="pos-form-group">
                        <label class="pos-form-label">Metode</label>
                        <select id="paymentMethod" class="pos-input">
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="pos-form-group">
                        <label class="pos-form-label">Uang Diterima</label>
                        <input type="number" id="paidAmount" class="pos-input" required min="0" placeholder="0" style="font-weight: bold; color: #1f2937;">
                    </div>
                </div>

                <div id="changeContainer" class="pos-change-box">
                    <span class="pos-change-label">Kembalian:</span>
                    <span class="pos-change-val" id="changeAmount">Rp 0</span>
                </div>

                <button type="submit" id="btnCheckout" class="pos-btn-submit" disabled>
                    <i class="fa-solid fa-check-circle"></i> Selesaikan Transaksi
                </button>
            </form>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div id="successModal" class="pos-modal-overlay">
    <div class="pos-modal-card" id="successCard">
        <div class="pos-modal-header-bg"></div>
        <div class="pos-modal-content">
            <div class="pos-modal-icon"><i class="fa-solid fa-check-circle"></i></div>
            <h2 class="pos-modal-title">Transaksi Berhasil!</h2>
            <p class="pos-modal-subtitle">No. Invoice: <span id="successInvoice" class="pos-modal-inv">INV-XXX</span></p>
            
            <div class="pos-modal-receipt">
                <div class="pos-modal-row">
                    <span class="pos-modal-label">Total Belanja</span>
                    <span class="pos-modal-val" id="successTotal">Rp 0</span>
                </div>
                <div class="pos-modal-row">
                    <span class="pos-modal-label">Jumlah Dibayar</span>
                    <span class="pos-modal-val" id="successPaid">Rp 0</span>
                </div>
                <div class="pos-modal-total-row">
                    <span class="pos-modal-label">Uang Kembali</span>
                    <span class="pos-modal-total-val" id="successChange">Rp 0</span>
                </div>
            </div>
            
            <div class="pos-modal-actions">
                <button type="button" class="pos-btn-secondary" onclick="resetCartAndCloseModal()">Transaksi Baru</button>
                <button type="button" class="pos-btn-primary" onclick="printReceipt()"><i class="fa-solid fa-print"></i> Cetak Struk</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let cart = [];
    let isProcessing = false;
    
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR',
            minimumFractionDigits: 0, maximumFractionDigits: 0
        }).format(amount).replace('Rp', 'Rp ');
    };

    const emptyCartMsg = document.getElementById('emptyCartMsg');
    const cartList = document.getElementById('cartList');
    const cartCountBadge = document.getElementById('cartCountBadge');
    
    const summarySubtotal = document.getElementById('summarySubtotal');
    const summaryTax = document.getElementById('summaryTax');
    const summaryTotal = document.getElementById('summaryTotal');
    const paidAmountInput = document.getElementById('paidAmount');
    const changeContainer = document.getElementById('changeContainer');
    const changeAmountTxt = document.getElementById('changeAmount');
    const btnCheckout = document.getElementById('btnCheckout');
    
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const productGrid = document.getElementById('productGrid');
    const noProductMsg = document.getElementById('noProductMsg');

    paidAmountInput.addEventListener('input', updateChange);
    searchInput.addEventListener('input', debounce(fetchMedicines, 300));
    categoryFilter.addEventListener('change', fetchMedicines);

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }

    function addToCart(id, name, price, stock) {
        if (stock <= 0) { alert('Maaf, stok obat ini sudah habis.'); return; }
        const item = cart.find(i => i.id === id);
        if (item) {
            if (item.quantity >= stock) { alert('Maksimal stok tercapai.'); return; }
            item.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1, maxStock: stock });
        }
        renderCart();
    }

    function updateCartQty(id, change) {
        const item = cart.find(i => i.id === id);
        if (!item) return;
        const newQty = item.quantity + change;
        if (newQty <= 0) { removeFromCart(id); return; }
        if (newQty > item.maxStock) { alert('Maksimal stok tercapai.'); return; }
        item.quantity = newQty;
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function renderCart() {
        if (cart.length === 0) {
            emptyCartMsg.style.display = 'flex';
            cartList.classList.remove('show');
            btnCheckout.disabled = true;
        } else {
            emptyCartMsg.style.display = 'none';
            cartList.classList.add('show');
            btnCheckout.disabled = false;
        }

        cartList.innerHTML = '';
        let totalItems = 0; let subtotal = 0;

        cart.forEach(item => {
            totalItems += item.quantity;
            subtotal += item.price * item.quantity;

            const li = document.createElement('li');
            li.className = 'pos-cart-item';
            li.innerHTML = `
                <div class="pos-cart-item-info">
                    <h5 class="pos-cart-item-name" title="${item.name}">${item.name}</h5>
                    <div class="pos-cart-item-price">${formatCurrency(item.price)}</div>
                </div>
                <div class="pos-cart-actions">
                    <div class="pos-qty-controls">
                        <button type="button" class="pos-qty-btn" onclick="updateCartQty(${item.id}, -1)"><i class="fa-solid fa-minus"></i></button>
                        <span class="pos-qty-val">${item.quantity}</span>
                        <button type="button" class="pos-qty-btn" onclick="updateCartQty(${item.id}, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <button type="button" class="pos-btn-remove" onclick="removeFromCart(${item.id})">Hapus</button>
                </div>
            `;
            cartList.appendChild(li);
        });

        cartCountBadge.textContent = `${totalItems} Item`;
        
        const tax = 0; 
        const total = subtotal + tax;
        summarySubtotal.textContent = formatCurrency(subtotal);
        summaryTax.textContent = formatCurrency(tax);
        summaryTotal.textContent = formatCurrency(total);
        summaryTotal.dataset.value = total;
        
        updateChange();
    }

    function updateChange() {
        const total = parseFloat(summaryTotal.dataset.value || 0);
        const paid = parseFloat(paidAmountInput.value || 0);

        if (total > 0 && paid >= total) {
            changeAmountTxt.textContent = formatCurrency(paid - total);
            changeContainer.classList.add('show');
            if(cart.length > 0) btnCheckout.disabled = false;
        } else {
            changeContainer.classList.remove('show');
            btnCheckout.disabled = (paid > 0 && paid < total) || cart.length === 0;
        }
    }

    async function processCheckout(e) {
        e.preventDefault();
        if (cart.length === 0) return;
        const total = parseFloat(summaryTotal.dataset.value || 0);
        const paid = parseFloat(paidAmountInput.value || 0);
        const pm = document.getElementById('paymentMethod').value;
        const cn = document.getElementById('customerName').value;

        if (pm === 'Tunai' && paid < total) { alert('Jumlah uang bayar kurang dari total belanja.'); return; }
        if (isProcessing) return;
        isProcessing = true;
        
        const oldHtml = btnCheckout.innerHTML;
        btnCheckout.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';
        btnCheckout.disabled = true;

        try {
            const res = await fetch('{{ route("kasir.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({
                    items: cart.map(c => ({ id: c.id, quantity: c.quantity })),
                    payment_method: pm, paid_amount: paid, customer_name: cn, notes: ''
                })
            });
            const data = await res.json();
            if (data.success) { showSuccessModal(data); } else { alert('Gagal: ' + (data.message || 'Terjadi kesalahan')); }
        } catch (err) {
            alert('Terjadi kesalahan jaringan.');
        } finally {
            isProcessing = false;
            btnCheckout.innerHTML = oldHtml;
            updateChange();
        }
    }

    async function fetchMedicines() {
        const q = searchInput.value;
        const catId = categoryFilter.value;
        try {
            const res = await fetch(`{{ route('kasir.search') }}?q=${encodeURIComponent(q)}&category=${catId}`);
            const data = await res.json();
            renderProducts(data);
        } catch (err) { console.error(err); }
    }

    function renderProducts(medicines) {
        productGrid.innerHTML = '';
        if (medicines.length === 0) {
            productGrid.style.display = 'none';
            noProductMsg.classList.add('show');
            return;
        }
        productGrid.style.display = 'grid';
        noProductMsg.classList.remove('show');

        medicines.forEach(med => {
            let badge = '';
            if (med.current_stock <= 0) badge = '<span class="pos-badge-stock badge-danger">Habis</span>';
            else if (med.current_stock <= 10) badge = `<span class="pos-badge-stock badge-warn">Sisa ${med.current_stock}</span>`;
            else badge = `<span class="pos-badge-stock badge-ok">Stok: ${med.current_stock}</span>`;

            const div = document.createElement('div');
            div.className = 'pos-product-card';
            div.onclick = () => addToCart(med.id, med.name, med.selling_price, med.current_stock);
            div.innerHTML = `
                ${badge}
                <div class="pos-product-info">
                    <div class="pos-product-cat">${med.category ? med.category.name : 'Umum'}</div>
                    <h4 class="pos-product-name">${med.name}</h4>
                    <div class="pos-product-footer">
                        <span class="pos-product-price">${formatCurrency(med.selling_price)}</span>
                        <button class="pos-btn-add"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            `;
            productGrid.appendChild(div);
        });
    }

    function showSuccessModal(data) {
        document.getElementById('successInvoice').textContent = data.invoice;
        document.getElementById('successTotal').textContent = formatCurrency(summaryTotal.dataset.value || 0);
        document.getElementById('successPaid').textContent = formatCurrency(paidAmountInput.value || 0);
        document.getElementById('successChange').textContent = formatCurrency(data.change);
        document.getElementById('successModal').classList.add('show');
    }

    function resetCartAndCloseModal() {
        cart = []; renderCart();
        paidAmountInput.value = '';
        document.getElementById('customerName').value = '';
        document.getElementById('paymentMethod').value = 'Tunai';
        updateChange(); fetchMedicines();
        document.getElementById('successModal').classList.remove('show');
    }
    
    function printReceipt() { alert('Fitur cetak struk sedang dalam pengembangan.'); }
</script>
@endpush
