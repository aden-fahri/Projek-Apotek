@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.kasir')

@section('title', 'Riwayat Transaksi')

@push('styles')
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-light: #14B8A6;
            --background: #F5F0E8;
            --surface: #FAF8F4;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;
            --white: #FFFFFF;
            --border: #E2E8F0;
        }

        .history-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4px 8px;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .header-title-area {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .header-title {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 26px;
            color: var(--text-primary);
            margin: 0;
        }

        .header-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        /* Alert Section */
        .alert-container {
            margin-bottom: 20px;
        }

        .alert-box {
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background-color: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .alert-error {
            background-color: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert-close {
            background: none;
            border: none;
            color: currentColor;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        /* Main Panel Card */
        .panel-card {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 30px;
        }

        /* Filters Section */
        .filter-section {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            background-color: var(--white);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            min-width: 200px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .form-input {
            height: 42px;
            padding: 0 16px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background-color: var(--white);
            color: var(--text-primary);
            font-family: 'Quicksand', sans-serif;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            min-width: 220px;
        }

        .btn {
            height: 42px;
            padding: 0 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Quicksand', sans-serif;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #E2E8F0;
            color: #475569;
        }

        .btn-secondary:hover {
            background-color: #CBD5E1;
        }

        /* Table Section */
        .table-responsive {
            overflow-x: auto;
            background-color: var(--white);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            background-color: #F8FAFC;
            padding: 16px 24px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border);
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 18px 24px;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background-color: #FAFBFD;
        }

        .invoice-cell {
            font-weight: 700;
            color: var(--primary-dark);
            font-family: monospace;
            font-size: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-success {
            background-color: #DCFCE7;
            color: #15803D;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        .badge-payment {
            background-color: #F1F5F9;
            color: #475569;
            border: 1px solid #E2E8F0;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            color: var(--white);
        }

        .btn-view {
            background-color: var(--primary);
        }

        .btn-view:hover {
            background-color: var(--primary-dark);
        }

        .btn-cancel {
            background-color: var(--danger);
        }

        .btn-cancel:hover {
            background-color: #DC2626;
        }

        /* Empty State */
        .empty-state {
            padding: 48px 24px;
            text-align: center;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 40px;
            color: #CBD5E1;
            margin-bottom: 12px;
        }

        /* Panel Footer Pagination */
        .panel-footer {
            padding: 18px 24px;
            background-color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
        }

        .footer-info {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .pagination {
            display: flex;
            gap: 4px;
        }

        .page-link {
            height: 34px;
            padding: 0 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background-color: var(--white);
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-link:hover:not(.disabled) {
            border-color: var(--primary);
            color: var(--primary);
        }

        .page-link.active {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .page-link.disabled {
            color: #94A3B8;
            background-color: #F8FAFC;
            cursor: not-allowed;
        }

        /* Detail Modal */
        .detail-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
            padding: 16px;
        }

        .detail-overlay.show {
            display: flex;
            opacity: 1;
        }

        .detail-card {
            background-color: var(--white);
            border-radius: 16px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.25s ease;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .detail-overlay.show .detail-card {
            transform: scale(1);
        }

        .detail-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #F8FAFC;
        }

        .detail-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .close-btn:hover {
            color: var(--danger);
            background-color: #F1F5F9;
        }

        .detail-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            background-color: #FAFBFD;
            padding: 16px;
            border-radius: 12px;
            border: 1px dashed var(--border);
        }

        .grid-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .grid-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .grid-val {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .detail-table th {
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .detail-table td {
            padding: 12px;
            font-size: 14px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border);
        }

        .detail-summary {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: flex-end;
            border-top: 1px solid var(--border);
            padding-top: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            width: 240px;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-dark);
            border-top: 1px solid var(--border);
            padding-top: 8px;
            margin-top: 4px;
        }

        .summary-label {
            color: var(--text-secondary);
        }

        .summary-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .summary-row.total .summary-value {
            color: var(--primary-dark);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-buttons {
                width: 100%;
            }
            .filter-buttons .btn {
                flex: 1;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
            .panel-footer {
                flex-direction: column;
                gap: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="history-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-title-area">
                <h1 class="header-title">Riwayat Transaksi</h1>
                <p class="header-subtitle">Daftar transaksi penjualan obat yang telah dilakukan</p>
            </div>
        </div>

        <!-- Notification Alerts -->
        <div class="alert-container">
            @if(session('success'))
                <div class="alert-box alert-success" id="success-alert">
                    <span><i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>{{ session('success') }}</span>
                    <button class="alert-close" onclick="document.getElementById('success-alert').remove()">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-box alert-error" id="error-alert">
                    <span><i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i>{{ session('error') }}</span>
                    <button class="alert-close" onclick="document.getElementById('error-alert').remove()">&times;</button>
                </div>
            @endif
        </div>

        <!-- Main Content Panel -->
        <div class="panel-card">
            <!-- Filter Bar -->
            <div class="filter-section">
                <form action="{{ route('riwayat-transaksi') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label class="form-label" for="start_date">Mulai Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-input" value="{{ request('start_date') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="end_date">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-input" value="{{ request('end_date') }}">
                    </div>

                    <div class="filter-buttons">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('riwayat-transaksi') }}" class="btn btn-secondary" style="flex: 1;">
                            <i class="fa-solid fa-arrows-rotate"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Area -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Pelanggan</th>
                            <th>Metode Bayar</th>
                            <th style="text-align: right;">Total Transaksi</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td class="invoice-cell">{{ $trx->invoice_number }}</td>
                                <td>{{ $trx->transaction_date->format('d M Y H:i') }}</td>
                                <td>
                                    <span style="font-weight: 600;">{{ $trx->kasir->name ?? 'System' }}</span>
                                </td>
                                <td>{{ $trx->customer_name ?: '-' }}</td>
                                <td>
                                    <span class="badge badge-payment">{{ $trx->payment_method }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--text-primary);">
                                    Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    @if($trx->status === 'completed')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-danger">Batal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons" style="justify-content: center;">
                                        <!-- View Detail Button -->
                                        <button class="btn-icon btn-view" title="Detail Transaksi"
                                            onclick="openDetailModal({{ json_encode($trx) }}, {{ json_encode($trx->details->load('medicine')) }}, '{{ $trx->kasir->name ?? 'System' }}')">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        <!-- Cancel Transaksi (Admin Only) -->
                                        @if(Auth::user()->role === 'admin' && $trx->status !== 'cancelled')
                                            <form action="{{ route('riwayat-transaksi.cancel', $trx->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi {{ $trx->invoice_number }}? Tindakan ini akan mengembalikan stok obat.')"
                                                style="margin: 0; display: inline;">
                                                @csrf
                                                <button type="submit" class="btn-icon btn-cancel" title="Batalkan Transaksi">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <i class="fa-regular fa-folder-open"></i>
                                    <p style="margin: 0; font-weight: 600; font-size: 14px;">Tidak Ada Transaksi</p>
                                    <p style="margin: 4px 0 0 0; font-size: 12px; color: var(--text-secondary);">Belum ada riwayat transaksi dalam database atau filter tanggal tidak cocok.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Panel Footer Pagination -->
            @if($transactions->hasPages() || $transactions->total() > 0)
                <div class="panel-footer">
                    <span class="footer-info">
                        Menampilkan {{ $transactions->firstItem() ?: 0 }} - {{ $transactions->lastItem() ?: 0 }} dari {{ $transactions->total() }} transaksi
                    </span>

                    @if($transactions->hasPages())
                        <div class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($transactions->onFirstPage())
                                <span class="page-link disabled"><i class="fa-solid fa-angle-left"></i></span>
                            @else
                                <a href="{{ $transactions->previousPageUrl() }}" class="page-link"><i class="fa-solid fa-angle-left"></i></a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($transactions->links()->elements as $element)
                                @if (is_string($element))
                                    <span class="page-link disabled">{{ $element }}</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $transactions->currentPage())
                                            <span class="page-link active">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($transactions->hasMorePages())
                                <a href="{{ $transactions->nextPageUrl() }}" class="page-link"><i class="fa-solid fa-angle-right"></i></a>
                            @else
                                <span class="page-link disabled"><i class="fa-solid fa-angle-right"></i></span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ===== MODAL DETAIL TRANSAKSI ===== --}}
    <div id="detailModal" class="detail-overlay">
        <div class="detail-card">
            <div class="detail-header">
                <h3 id="detailInvoice">Invoice #INV-00000</h3>
                <button onclick="closeDetailModal()" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="detail-body">
                <!-- Metadata Grid -->
                <div class="detail-grid">
                    <div class="grid-item">
                        <span class="grid-label">Waktu Transaksi</span>
                        <span class="grid-val" id="detailDate">-</span>
                    </div>
                    <div class="grid-item">
                        <span class="grid-label">Petugas Kasir</span>
                        <span class="grid-val" id="detailKasir">-</span>
                    </div>
                    <div class="grid-item">
                        <span class="grid-label">Pelanggan</span>
                        <span class="grid-val" id="detailCustomer">-</span>
                    </div>
                    <div class="grid-item">
                        <span class="grid-label">Metode Pembayaran</span>
                        <span class="grid-val" id="detailMethod">-</span>
                    </div>
                </div>

                <!-- Items Table -->
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Obat</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detailItemsBody">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>

                <!-- Summary Panel -->
                <div class="detail-summary">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value" id="summarySubtotal">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Pajak</span>
                        <span class="summary-value" id="summaryTax">Rp 0</span>
                    </div>
                    <div class="summary-row total">
                        <span class="summary-label">Total Belanja</span>
                        <span class="summary-value" id="summaryTotal">Rp 0</span>
                    </div>
                    <div class="summary-row" style="margin-top: 8px;">
                        <span class="summary-label">Diterima</span>
                        <span class="summary-value" id="summaryPaid">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Kembalian</span>
                        <span class="summary-value" id="summaryChange">Rp 0</span>
                    </div>
                </div>

                <!-- Notes -->
                <div id="detailNotesContainer" style="margin-top: 24px; padding: 12px; background-color: #F8FAFC; border-radius: 8px; border-left: 4px solid var(--primary); display: none;">
                    <span style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 4px;">Catatan</span>
                    <span style="font-size: 13px; color: var(--text-primary); font-style: italic;" id="detailNotes"></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const modal = document.getElementById('detailModal');

        function formatRupiah(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function openDetailModal(trx, details, kasirName) {
            // Populate invoice header & basic info
            document.getElementById('detailInvoice').innerText = 'Invoice #' + trx.invoice_number;
            
            // Format date
            const trxDate = new Date(trx.transaction_date);
            const formattedDate = trxDate.toLocaleDateString('id-ID', { 
                day: '2-digit', 
                month: 'long', 
                year: 'numeric' 
            }) + ' ' + trxDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            
            document.getElementById('detailDate').innerText = formattedDate;
            document.getElementById('detailKasir').innerText = kasirName;
            document.getElementById('detailCustomer').innerText = trx.customer_name || '-';
            document.getElementById('detailMethod').innerText = trx.payment_method;

            // Populate table details
            const itemsBody = document.getElementById('detailItemsBody');
            itemsBody.innerHTML = '';
            
            let subtotal = 0;
            details.forEach(item => {
                const row = document.createElement('tr');
                
                const nameCell = document.createElement('td');
                nameCell.innerHTML = `<span style="font-weight: 600; color: var(--text-primary);">${item.medicine.name}</span><br><span style="font-size: 11px; color: var(--text-secondary);">${item.medicine.code}</span>`;
                
                const qtyCell = document.createElement('td');
                qtyCell.style.textAlign = 'center';
                qtyCell.innerText = item.quantity;
                
                const priceCell = document.createElement('td');
                priceCell.style.textAlign = 'right';
                priceCell.innerText = formatRupiah(item.price);
                
                const subtotalCell = document.createElement('td');
                subtotalCell.style.textAlign = 'right';
                subtotalCell.style.fontWeight = '600';
                subtotalCell.innerText = formatRupiah(item.subtotal);
                
                row.appendChild(nameCell);
                row.appendChild(qtyCell);
                row.appendChild(priceCell);
                row.appendChild(subtotalCell);
                itemsBody.appendChild(row);
                
                subtotal += parseFloat(item.subtotal);
            });

            // Populate summary
            document.getElementById('summarySubtotal').innerText = formatRupiah(subtotal);
            document.getElementById('summaryTax').innerText = formatRupiah(trx.tax || 0);
            document.getElementById('summaryTotal').innerText = formatRupiah(trx.grand_total);
            document.getElementById('summaryPaid').innerText = formatRupiah(trx.paid_amount);
            document.getElementById('summaryChange').innerText = formatRupiah(trx.change_amount);

            // Populate notes if available
            const notesContainer = document.getElementById('detailNotesContainer');
            if (trx.notes && trx.notes.trim() !== '') {
                document.getElementById('detailNotes').innerText = trx.notes;
                notesContainer.style.display = 'block';
            } else {
                notesContainer.style.display = 'none';
            }

            // Show Modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside the box
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeDetailModal();
            }
        });
    </script>
@endpush
