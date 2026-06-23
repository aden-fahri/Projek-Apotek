<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanKeluarExport;
use App\Exports\LaporanLabaExport;
use App\Exports\LaporanMasukExport;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\StockReturnDetail;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // =========================================================================
    // F-17: LAPORAN MASUK (Uang Masuk / Penjualan dari Kasir)
    // =========================================================================
    public function masuk(Request $request)
    {
        // Nilai default: bulan ini
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $kasirId = $request->get('kasir_id');
        $metodePembayaran = $request->get('metode_pembayaran');

        // Query data transaksi penjualan
        $query = Transaction::with('kasir')
            ->whereBetween('transaction_date', [$mulai, $sampai])
            ->where('status', '!=', 'cancelled');

        if ($kasirId) {
            $query->where('user_id', $kasirId);
        }

        if ($metodePembayaran) {
            $query->where('payment_method', $metodePembayaran);
        }

        $transaksi = $query->orderBy('transaction_date', 'desc')->paginate(15)->withQueryString();

        // Hitung total uang masuk dalam periode
        $totalMasuk = Transaction::whereBetween('transaction_date', [$mulai, $sampai])
            ->where('status', '!=', 'cancelled')
            ->when($kasirId, fn($q) => $q->where('user_id', $kasirId))
            ->when($metodePembayaran, fn($q) => $q->where('payment_method', $metodePembayaran))
            ->sum('grand_total');

        // Hitung total bulan lalu untuk perbandingan
        $totalBulanLalu = Transaction::whereBetween('transaction_date', [
            now()->subMonth()->startOfMonth()->toDateString(),
            now()->subMonth()->endOfMonth()->toDateString(),
        ])->where('status', '!=', 'cancelled')->sum('grand_total');

        $persentasePerubahan = $totalBulanLalu > 0
            ? round((($totalMasuk - $totalBulanLalu) / $totalBulanLalu) * 100, 1)
            : 0;

        // Data untuk dropdown filter
        $daftarKasir = User::where('role', 'kasir')->where('is_active', true)->get();
        $daftarMetode = ['Tunai', 'QRIS', 'Transfer'];

        return view('admin.report.masuk', compact(
            'transaksi',
            'totalMasuk',
            'persentasePerubahan',
            'daftarKasir',
            'daftarMetode',
            'mulai',
            'sampai',
            'kasirId',
            'metodePembayaran'
        ));
    }

    // Export Excel Laporan Masuk
    public function exportMasuk(Request $request)
    {
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $kasirId = $request->get('kasir_id');
        $metodePembayaran = $request->get('metode_pembayaran');

        $namaFile = 'laporan-masuk-' . $mulai . '-sd-' . $sampai . '.xlsx';
        return Excel::download(
            new LaporanMasukExport($mulai, $sampai, $kasirId, $metodePembayaran),
            $namaFile
        );
    }

    // =========================================================================
    // F-18: LAPORAN KELUAR (Uang Keluar / Pembelian ke Supplier)
    // =========================================================================
    public function keluar(Request $request)
    {
        // Nilai default: bulan ini
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $supplierId = $request->get('supplier_id');

        // Query data pembelian dari supplier
        $query = PurchaseOrder::with('supplier')
            ->whereBetween('order_date', [$mulai, $sampai])
            ->where('status', '!=', 'cancelled');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $pembelian = $query->orderBy('order_date', 'desc')->paginate(15)->withQueryString();

        // Hitung total uang keluar dalam periode
        $totalKeluar = PurchaseOrder::whereBetween('order_date', [$mulai, $sampai])
            ->where('status', '!=', 'cancelled')
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->sum('total_amount');

        // Total bulan lalu untuk perbandingan
        $totalBulanLalu = PurchaseOrder::whereBetween('order_date', [
            now()->subMonth()->startOfMonth()->toDateString(),
            now()->subMonth()->endOfMonth()->toDateString(),
        ])->where('status', '!=', 'cancelled')->sum('total_amount');

        $persentasePerubahan = $totalBulanLalu > 0
            ? round((($totalKeluar - $totalBulanLalu) / $totalBulanLalu) * 100, 1)
            : 0;

        // Data untuk dropdown filter
        $daftarSupplier = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('admin.report.keluar', compact(
            'pembelian',
            'totalKeluar',
            'persentasePerubahan',
            'daftarSupplier',
            'mulai',
            'sampai',
            'supplierId'
        ));
    }

    // Export Excel Laporan Keluar
    public function exportKeluar(Request $request)
    {
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $supplierId = $request->get('supplier_id');

        $namaFile = 'laporan-keluar-' . $mulai . '-sd-' . $sampai . '.xlsx';
        return Excel::download(
            new LaporanKeluarExport($mulai, $sampai, $supplierId),
            $namaFile
        );
    }

    // =========================================================================
    // F-19: BUKU BESAR (Laporan Komprehensif)
    // =========================================================================
    public function laba(Request $request)
    {
        // Nilai default: bulan ini
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $filterJenis = $request->get('jenis', 'Semua'); // filter jenis

        $bukuBesar = collect();

        // 1. Ambil data Penjualan (Masuk)
        if ($filterJenis === 'Semua' || $filterJenis === 'Jual Obat') {
            $penjualan = Transaction::with(['kasir', 'details.medicine'])->whereBetween('transaction_date', [$mulai . ' 00:00:00', $sampai . ' 23:59:59'])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => $item->transaction_date,
                        'referensi' => $item->invoice_number,
                        'jenis' => 'Jual Obat',
                        'keterangan' => 'Penjualan Kasir',
                        'oleh' => $item->kasir ? $item->kasir->name : 'Sistem',
                        'debit' => $item->grand_total,
                        'kredit' => 0,
                        'rincian' => $item->details->map(function($d) {
                            return [
                                'nama_obat' => $d->medicine ? $d->medicine->name : '-',
                                'qty' => $d->quantity,
                                'harga' => $d->price,
                                'subtotal' => $d->subtotal,
                            ];
                        })->toArray(),
                    ];
                });
            $bukuBesar = $bukuBesar->concat($penjualan);
        }

        // 2. Ambil data Pembelian (Keluar)
        if ($filterJenis === 'Semua' || $filterJenis === 'Beli Obat') {
            $pembelian = PurchaseOrder::with(['supplier', 'user', 'details.medicine'])->whereBetween('order_date', [$mulai, $sampai])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => \Carbon\Carbon::parse($item->order_date)->startOfDay(),
                        'referensi' => $item->invoice_number,
                        'jenis' => 'Beli Obat',
                        'keterangan' => 'Pembelian ke Supplier ' . ($item->supplier ? $item->supplier->name : ''),
                        'oleh' => $item->user ? $item->user->name : 'Sistem',
                        'debit' => 0,
                        'kredit' => $item->total_amount,
                        'rincian' => $item->details->map(function($d) {
                            return [
                                'nama_obat' => $d->medicine ? $d->medicine->name : '-',
                                'qty' => $d->quantity,
                                'harga' => $d->purchase_price,
                                'subtotal' => $d->subtotal,
                            ];
                        })->toArray(),
                    ];
                });
            $bukuBesar = $bukuBesar->concat($pembelian);
        }

        // 3. Ambil data Retur (Masuk)
        if ($filterJenis === 'Semua' || $filterJenis === 'Retur Obat') {
            $retur = \App\Models\StockReturn::with(['supplier', 'user', 'details.medicine'])->whereBetween('return_date', [$mulai, $sampai])
                ->where('status', '!=', 'ditolak')
                ->get()
                ->map(function ($item) {
                    return [
                        'tanggal' => \Carbon\Carbon::parse($item->return_date)->startOfDay(),
                        'referensi' => $item->return_number,
                        'jenis' => 'Retur Obat',
                        'keterangan' => 'Retur ke Supplier ' . ($item->supplier ? $item->supplier->name : '') . ': ' . $item->reason,
                        'oleh' => $item->user ? $item->user->name : 'Sistem',
                        'debit' => $item->total_amount,
                        'kredit' => 0,
                        'rincian' => $item->details->map(function($d) {
                            return [
                                'nama_obat' => $d->medicine ? $d->medicine->name : '-',
                                'qty' => $d->quantity,
                                'harga' => $d->purchase_price,
                                'subtotal' => $d->subtotal,
                            ];
                        })->toArray(),
                    ];
                });
            $bukuBesar = $bukuBesar->concat($retur);
        }
        
        // Urutkan berdasarkan tanggal
        $bukuBesar = $bukuBesar->sortBy('tanggal')->values();

        // Hitung Saldo Awal (sebelum tanggal mulai)
        $saldoAwalPenjualan = Transaction::where('transaction_date', '<', $mulai . ' 00:00:00')
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');
        
        $saldoAwalPembelian = PurchaseOrder::where('order_date', '<', $mulai)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $saldoAwalRetur = \App\Models\StockReturn::where('return_date', '<', $mulai)
            ->where('status', '!=', 'ditolak')
            ->sum('total_amount');

        $saldoAwal = $saldoAwalPenjualan - $saldoAwalPembelian + $saldoAwalRetur;

        // Hitung Saldo Berjalan (Hanya dihitung jika filterJenis == Semua, jika dipisah saldo berjalan tidak akurat)
        // Kita tetap hitung saldo berjalan sesuai baris yang ada
        $saldoBerjalan = $saldoAwal;
        $bukuBesar = $bukuBesar->map(function ($item) use (&$saldoBerjalan) {
            $saldoBerjalan += $item['debit'] - $item['kredit'];
            $item['saldo'] = $saldoBerjalan;
            return $item;
        });

        // Summary untuk periode ini
        $totalDebit = $bukuBesar->sum('debit');
        $totalKredit = $bukuBesar->sum('kredit');

        return view('admin.report.laba', compact(
            'bukuBesar',
            'saldoAwal',
            'totalDebit',
            'totalKredit',
            'saldoBerjalan',
            'mulai',
            'sampai',
            'filterJenis'
        ));
    }

    // Export Excel Laporan Laba
    public function exportLaba(Request $request)
    {
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $jenis = $request->get('jenis', 'Semua');

        $namaFile = 'laporan-buku-besar-' . $mulai . '-sd-' . $sampai . '.xlsx';
        return Excel::download(
            new LaporanLabaExport($mulai, $sampai, $jenis),
            $namaFile
        );
    }
}
