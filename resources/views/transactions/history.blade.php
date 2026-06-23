@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.kasir')
@section('title', 'Riwayat Transaksi')

@push('styles')
<style>
/* ── Page ── */
.riwayat-wrap { padding: 4px 8px; }
.page-heading { margin-bottom: 24px; }
.page-heading h1 { font-size: 26px; font-weight: 700; color: #1E293B; margin: 0; }
.page-heading p  { font-size: 14px; color: #64748B; margin: 4px 0 0; }

/* ── Alerts ── */
.alert { padding: 12px 18px; border-radius: 10px; font-size: 14px; font-weight: 500;
         display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
.alert-error   { background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; }
.alert button  { background: none; border: none; font-size: 18px; cursor: pointer; color: inherit; }

/* ── Panel ── */
.panel { background: #fff; border: 1px solid #E2E8F0; border-radius: 16px; overflow: hidden; margin-bottom: 30px; }

/* ── Toolbar ── */
.toolbar { padding: 18px 24px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #E2E8F0; }
.search-wrap { position: relative; flex: 1; }
.search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 14px; }
.search-wrap input { width: 100%; height: 40px; padding: 0 16px 0 40px; border: 1px solid #E2E8F0;
                     border-radius: 8px; font-size: 14px; font-family: inherit; outline: none;
                     color: #1E293B; background: #F8FAFC; box-sizing: border-box; }
.search-wrap input:focus { border-color: #0D9488; background: #fff; box-shadow: 0 0 0 3px rgba(13,148,136,.1); }
.toolbar-btn { height: 40px; padding: 0 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
               font-family: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
               border: 1px solid #E2E8F0; background: #fff; color: #475569; text-decoration: none; transition: .2s; }
.toolbar-btn:hover { border-color: #0D9488; color: #0D9488; }
.toolbar-btn.primary { background: #0D9488; color: #fff; border-color: #0D9488; }
.toolbar-btn.primary:hover { background: #0F766E; }

/* ── Table ── */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #F8FAFC; padding: 14px 20px; font-size: 12px; font-weight: 700;
                 text-transform: uppercase; color: #64748B; border-bottom: 1px solid #E2E8F0; letter-spacing: .4px; }
.data-table td { padding: 16px 20px; font-size: 14px; color: #1E293B; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
.data-table tbody tr:hover { background: #FAFBFD; }
.invoice-no { font-weight: 700; color: #0F766E; font-size: 13px; }
.badge-lunas  { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px;
                font-size: 12px; font-weight: 600; background: #DCFCE7; color: #15803D; }
.badge-batal  { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px;
                font-size: 12px; font-weight: 600; background: #FEE2E2; color: #B91C1C; }
.btn-view { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center;
            justify-content: center; background: #0D9488; color: #fff; border: none; cursor: pointer; transition: .2s; }
.btn-view:hover { background: #0F766E; }
.btn-cancel { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center;
              justify-content: center; background: #EF4444; color: #fff; border: none; cursor: pointer; transition: .2s; }

/* ── Empty ── */
.empty-row td { text-align: center; padding: 48px; color: #94A3B8; }
.empty-row i  { font-size: 36px; display: block; margin-bottom: 10px; }

/* ── Pagination ── */
.panel-foot { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;
              border-top: 1px solid #E2E8F0; }
.panel-foot span { font-size: 13px; color: #64748B; }
.pagi { display: flex; gap: 4px; }
.pagi a, .pagi span { height: 32px; min-width: 32px; padding: 0 10px; border-radius: 6px; display: inline-flex;
                       align-items: center; justify-content: center; font-size: 13px; font-weight: 500;
                       border: 1px solid #E2E8F0; background: #fff; color: #1E293B; text-decoration: none; }
.pagi .active { background: #0D9488; color: #fff; border-color: #0D9488; }
.pagi .disabled { color: #CBD5E1; pointer-events: none; }

/* ── Modal Overlay ── */
.modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.45);
                 backdrop-filter: blur(4px); z-index: 9999; display: none;
                 align-items: center; justify-content: center; padding: 16px; }
.modal-overlay.show { display: flex; }

/* ── Modal Card ── */
.modal-card { background: #fff; border-radius: 16px; width: 100%; max-width: 480px;
              box-shadow: 0 25px 50px rgba(0,0,0,.15); max-height: 90vh;
              display: flex; flex-direction: column; overflow: hidden;
              animation: modalIn .25s ease; }
@keyframes modalIn { from { opacity:0; transform:scale(.95); } to { opacity:1; transform:scale(1); } }

@media print {
    .modal-close,
    .btn-cetak { display: none !important; }
}

/* Modal Header */
.modal-header { padding: 20px 24px; display: flex; align-items: center; gap: 14px; position: relative; }
.modal-icon { width: 48px; height: 48px; background: #0D9488; border-radius: 12px;
              display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-icon i { color: #fff; font-size: 20px; }
.modal-title-wrap h3 { font-size: 17px; font-weight: 700; color: #1E293B; margin: 0; }
.modal-title-wrap p  { font-size: 12px; color: #64748B; margin: 2px 0 0; }
.modal-close { position: absolute; right: 20px; top: 20px; width: 30px; height: 30px;
               border: none; background: none; cursor: pointer; color: #94A3B8; font-size: 18px;
               border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.modal-close:hover { background: #F1F5F9; color: #1E293B; }

/* Info Grid */
.modal-body { padding: 0 20px 20px; overflow-y: auto; flex: 1; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
.info-cell { border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px 14px;
             display: flex; align-items: flex-start; gap: 10px; }
.info-cell-icon { width: 32px; height: 32px; border-radius: 8px; background: #F0FDFA;
                  display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.info-cell-icon i { color: #0D9488; font-size: 13px; }
.info-cell-text label { font-size: 10px; font-weight: 700; text-transform: uppercase;
                        letter-spacing: .5px; color: #94A3B8; display: block; margin-bottom: 3px; }
.info-cell-text span  { font-size: 14px; font-weight: 600; color: #1E293B; }

/* Rincian header */
.rincian-header { background: #0D9488; color: #fff; font-size: 12px; font-weight: 700;
                  text-transform: uppercase; letter-spacing: .6px; padding: 9px 14px;
                  border-radius: 8px 8px 0 0; margin-bottom: 0; }

/* Items table */
.items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; border: 1px solid #E2E8F0; border-top: none; border-radius: 0 0 8px 8px; overflow: hidden; }
.items-table th { padding: 10px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase;
                  color: #64748B; background: #F8FAFC; border-bottom: 1px solid #E2E8F0; text-align: left; }
.items-table td { padding: 12px; font-size: 13px; color: #1E293B; border-bottom: 1px solid #F1F5F9; }
.items-table tr:last-child td { border-bottom: none; }
.med-icon { width: 30px; height: 30px; border-radius: 50%; background: #F0FDFA;
            display: inline-flex; align-items: center; justify-content: center; margin-right: 8px; flex-shrink: 0; }
.med-icon i { color: #0D9488; font-size: 12px; }
.med-cell { display: flex; align-items: center; }

/* Bottom section */
.modal-bottom { display: flex; gap: 16px; align-items: flex-start; margin-top: 4px; padding-top: 14px; border-top: 1px solid #E2E8F0; }
.thank-you { flex: 1; display: flex; align-items: flex-start; gap: 10px; }
.thank-you i { color: #0D9488; font-size: 18px; margin-top: 2px; }
.thank-you-text p { margin: 0; font-size: 12px; color: #475569; line-height: 1.5; }
.thank-you-text .brand { font-weight: 700; color: #0D9488; }
.summary-col { min-width: 190px; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 5px; }
.sum-row .label { color: #64748B; }
.sum-row .val   { font-weight: 600; color: #1E293B; }
.sum-row.total  { border-top: 1px solid #E2E8F0; padding-top: 7px; margin-top: 5px; }
.sum-row.total .label { font-weight: 700; color: #1E293B; font-size: 14px; }
.sum-row.total .val   { font-weight: 700; color: #0D9488; font-size: 14px; }

/* Print button */
.btn-cetak { margin-top: 16px; height: 38px; padding: 0 18px; border-radius: 8px; border: 1.5px solid #0D9488;
             background: #fff; color: #0D9488; font-size: 13px; font-weight: 700;
             font-family: inherit; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: .2s; }
.btn-cetak:hover { background: #0D9488; color: #fff; }
</style>
@endpush

@section('content')
<div class="riwayat-wrap">

    {{-- Heading --}}
    <div class="page-heading">
        <h1>Riwayat Transaksi</h1>
        <p>Daftar transaksi penjualan</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success" id="alert-ok">
            <span><i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}</span>
            <button onclick="document.getElementById('alert-ok').remove()">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" id="alert-err">
            <span><i class="fa-solid fa-triangle-exclamation" style="margin-right:6px;"></i>{{ session('error') }}</span>
            <button onclick="document.getElementById('alert-err').remove()">&times;</button>
        </div>
    @endif

    {{-- Panel --}}
    <div class="panel">

        {{-- Toolbar --}}
        <div class="toolbar">
            <form action="{{ route('riwayat-transaksi') }}" method="GET" style="display:contents;">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Cari invoice / pelanggan..."
                           value="{{ request('search') }}">
                </div>
                <button type="submit" class="toolbar-btn primary">
                    <i class="fa-solid fa-sliders"></i> Filter
                </button>
                <a href="{{ route('riwayat-transaksi') }}" class="toolbar-btn">
                    <i class="fa-solid fa-rotate"></i> Reset
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal & Waktu</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th style="text-align:right;">Total Pembayaran</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:center;width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="invoice-no">{{ $trx->invoice_number }}</td>
                        <td style="color:#64748B;font-size:13px;">{{ $trx->transaction_date->format('d M Y H:i') }}</td>
                        <td style="font-weight:600;">{{ $trx->kasir->name ?? 'System' }}</td>
                        <td style="color:#64748B;">{{ $trx->customer_name ?: '-' }}</td>
                        <td><span style="font-size:12px;color:#475569;text-transform:capitalize;">{{ $trx->payment_method }}</span></td>
                        <td style="text-align:right;font-weight:700;">Rp {{ number_format($trx->grand_total,0,',','.') }}</td>
                        <td style="text-align:center;">
                            @if($trx->status === 'completed')
                                <span class="badge-lunas">Lunas</span>
                            @else
                                <span class="badge-batal">Batal</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <button class="btn-view" title="Lihat Detail"
                                    onclick="openModal({{ json_encode($trx) }}, {{ json_encode($trx->details->load('medicine')) }}, '{{ addslashes($trx->kasir->name ?? 'System') }}')">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @if(Auth::user()->role === 'admin' && $trx->status !== 'cancelled')
                                    <form action="{{ route('riwayat-transaksi.cancel', $trx->id) }}" method="POST"
                                          onsubmit="return confirm('Batalkan transaksi {{ $trx->invoice_number }}?')" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn-cancel" title="Batalkan">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="8">
                            <i class="fa-regular fa-folder-open"></i>
                            <p style="margin:0;font-weight:600;">Tidak Ada Transaksi</p>
                            <p style="margin:4px 0 0;font-size:12px;">Belum ada riwayat transaksi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer pagination --}}
        @if($transactions->total() > 0)
        <div class="panel-foot">
            <span>Menampilkan {{ number_format($transactions->firstItem() ?: 0, 0, ',', '.') }} - {{ number_format($transactions->lastItem() ?: 0, 0, ',', '.') }} dari {{ number_format($transactions->total(), 0, ',', '.') }} data</span>
            @if($transactions->hasPages())
            <div class="pagi">
                @if($transactions->onFirstPage())
                    <span class="disabled"><i class="fa-solid fa-angle-left"></i></span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}"><i class="fa-solid fa-angle-left"></i></a>
                @endif
                @foreach($transactions->links()->elements as $element)
                    @if(is_string($element))
                        <span class="disabled">{{ $element }}</span>
                    @endif
                    @if(is_array($element))
                        @foreach($element as $page => $url)
                            @if($page == $transactions->currentPage())
                                <span class="active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
                @if($transactions->hasMorePages())
                    <a href="{{ $transactions->nextPageUrl() }}"><i class="fa-solid fa-angle-right"></i></a>
                @else
                    <span class="disabled"><i class="fa-solid fa-angle-right"></i></span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- ===== MODAL ===== --}}
<div id="trxModal" class="modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="modal-card">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-icon"><i class="fa-solid fa-file-invoice"></i></div>
            <div class="modal-title-wrap">
                <h3 id="mInvoice">Invoice #INV-000</h3>
                <p>Detail riwayat transaksi</p>
            </div>
            <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        {{-- Body --}}
        <div class="modal-body">
            {{-- Info Grid --}}
            <div class="info-grid">
                <div class="info-cell">
                    <div class="info-cell-icon"><i class="fa-regular fa-clock"></i></div>
                    <div class="info-cell-text">
                        <label>Waktu Transaksi</label>
                        <span id="mDate">-</span>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-icon"><i class="fa-regular fa-user"></i></div>
                    <div class="info-cell-text">
                        <label>Petugas Kasir</label>
                        <span id="mKasir">-</span>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-icon"><i class="fa-solid fa-user-group"></i></div>
                    <div class="info-cell-text">
                        <label>Pelanggan</label>
                        <span id="mCustomer">-</span>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-cell-icon"><i class="fa-regular fa-credit-card"></i></div>
                    <div class="info-cell-text">
                        <label>Metode Pembayaran</label>
                        <span id="mMethod">-</span>
                    </div>
                </div>
            </div>

            {{-- Rincian --}}
            <div class="rincian-header">Rincian Pembelian</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Obat</th>
                        <th style="text-align:center;">QTY</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="mItems"></tbody>
            </table>

            {{-- Bottom: thank-you + summary --}}
            <div class="modal-bottom">
                <div class="thank-you">
                    <i class="fa-solid fa-print"></i>
                    <div class="thank-you-text">
                        <p>Terima kasih telah berbelanja<br>di <span class="brand" id="mPharmacyName">{{ \App\Models\PharmacySetting::getSetting()->pharmacy_name }}</span></p>
                        <p style="margin-top:4px;">Semoga lekas sembuh <span style="color:#EF4444;">♡</span></p>
                    </div>
                </div>
                <div class="summary-col">
                    <div class="sum-row"><span class="label">Subtotal</span><span class="val" id="mSubtotal">Rp 0</span></div>
                    <div class="sum-row"><span class="label">Pajak</span><span class="val" id="mTax">Rp 0</span></div>
                    <div class="sum-row total"><span class="label">Total Belanja</span><span class="val" id="mTotal">Rp 0</span></div>
                    <div class="sum-row" style="margin-top:6px;"><span class="label">Diterima</span><span class="val" id="mPaid">Rp 0</span></div>
                    <div class="sum-row"><span class="label">Kembalian</span><span class="val" id="mChange">Rp 0</span></div>
                </div>
            </div>

            <button class="btn-cetak" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak Invoice
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('trxModal');

function rp(v) {
    return 'Rp ' + parseFloat(v || 0).toLocaleString('id-ID', {minimumFractionDigits:0, maximumFractionDigits:0});
}

function openModal(trx, items, kasir) {
    document.getElementById('mInvoice').textContent  = 'Invoice #' + trx.invoice_number;
    document.getElementById('mKasir').textContent    = kasir;
    document.getElementById('mCustomer').textContent = trx.customer_name || '-';
    document.getElementById('mMethod').textContent   = trx.payment_method;

    const d = new Date(trx.transaction_date);
    document.getElementById('mDate').textContent =
        d.toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'}) + ' ' +
        d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}).replace('.', '.');

    const tbody = document.getElementById('mItems');
    tbody.innerHTML = '';
    let sub = 0;
    items.forEach(item => {
        sub += parseFloat(item.subtotal || 0);
        tbody.innerHTML += `
        <tr>
            <td>
                <div class="med-cell">
                    <div class="med-icon"><i class="fa-solid fa-capsules"></i></div>
                    <div>
                        <div style="font-weight:600;font-size:13px;">${item.medicine.name}</div>
                        <div style="font-size:11px;color:#94A3B8;">${item.medicine.code}</div>
                    </div>
                </div>
            </td>
            <td style="text-align:center;">${new Intl.NumberFormat('id-ID').format(item.quantity)}</td>
            <td style="text-align:right;">${rp(item.price)}</td>
            <td style="text-align:right;font-weight:600;">${rp(item.subtotal)}</td>
        </tr>`;
    });

    document.getElementById('mSubtotal').textContent = rp(sub);
    document.getElementById('mTax').textContent      = rp(trx.tax || 0);
    document.getElementById('mTotal').textContent    = rp(trx.grand_total);
    document.getElementById('mPaid').textContent     = rp(trx.paid_amount);
    document.getElementById('mChange').textContent   = rp(trx.change_amount);

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    modal.classList.remove('show');
    document.body.style.overflow = '';
}
</script>
@endpush
