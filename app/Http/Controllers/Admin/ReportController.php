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
    // F-19: LAPORAN LABA (Kotor & Bersih)
    // =========================================================================
    public function laba(Request $request)
    {
        // Nilai default: bulan ini
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());
        $cariObat = $request->get('cari_obat');

        // ---------------------------------------------------------------
        // Hitung Total Penjualan & HPP dalam periode (untuk summary)
        // ---------------------------------------------------------------
        $summaryPenjualan = TransactionDetail::join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->whereHas('transaction', function ($q) use ($mulai, $sampai) {
                $q->whereBetween('transactions.transaction_date', [$mulai, $sampai])
                  ->where('transactions.status', '!=', 'cancelled');
            })
            ->selectRaw('
                SUM(transaction_details.quantity * transaction_details.price) as total_penjualan,
                SUM(transaction_details.quantity * transaction_details.purchase_price) as total_hpp
            ')
            ->first();

        $totalPenjualan = $summaryPenjualan->total_penjualan ?? 0;
        $totalHpp       = $summaryPenjualan->total_hpp ?? 0;
        $labaKotor      = $totalPenjualan - $totalHpp;

        // ---------------------------------------------------------------
        // Hitung Total Return (pengurang laba bersih)
        // ---------------------------------------------------------------
        $totalReturn = StockReturnDetail::whereHas('stockReturn', function ($q) use ($mulai, $sampai) {
                $q->whereBetween('return_date', [$mulai, $sampai])
                  ->where('status', '!=', 'ditolak');
            })
            ->selectRaw('SUM(quantity * purchase_price) as total_return')
            ->value('total_return') ?? 0;

        $labaBersih = $labaKotor - $totalReturn;

        // ---------------------------------------------------------------
        // Breakdown per Obat (untuk tabel rincian)
        // ---------------------------------------------------------------
        $rincianQuery = TransactionDetail::join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->with(['medicine.unit', 'medicine.category'])
            ->whereHas('transaction', function ($q) use ($mulai, $sampai) {
                $q->whereBetween('transactions.transaction_date', [$mulai, $sampai])
                  ->where('transactions.status', '!=', 'cancelled');
            })
            ->selectRaw('
                transaction_details.medicine_id,
                SUM(transaction_details.quantity) as total_qty,
                AVG(transaction_details.purchase_price) as avg_hpp,
                AVG(transaction_details.price) as avg_jual,
                SUM(transaction_details.quantity * transaction_details.price) as total_penjualan,
                SUM(transaction_details.quantity * transaction_details.purchase_price) as total_hpp,
                SUM(transaction_details.quantity * (transaction_details.price - transaction_details.purchase_price)) as total_laba
            ')
            ->groupBy('transaction_details.medicine_id');

        if ($cariObat) {
            $rincianQuery->whereHas('medicine', function ($q) use ($cariObat) {
                $q->where('name', 'like', "%{$cariObat}%");
            });
        }

        $rincianPerObat = $rincianQuery->orderByDesc('total_laba')->paginate(15)->withQueryString();

        // ---------------------------------------------------------------
        // Perbandingan dengan bulan lalu
        // ---------------------------------------------------------------
        $labaKotorBulanLalu = TransactionDetail::join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->whereHas('transaction', function ($q) {
                $q->whereBetween('transactions.transaction_date', [
                    now()->subMonth()->startOfMonth()->toDateString(),
                    now()->subMonth()->endOfMonth()->toDateString(),
                ])->where('transactions.status', '!=', 'cancelled');
            })
            ->selectRaw('SUM(transaction_details.quantity * (transaction_details.price - transaction_details.purchase_price)) as laba')
            ->value('laba') ?? 0;

        $pctLabaKotor = $labaKotorBulanLalu > 0
            ? round((($labaKotor - $labaKotorBulanLalu) / $labaKotorBulanLalu) * 100, 1)
            : 0;

        return view('admin.report.laba', compact(
            'totalPenjualan',
            'totalHpp',
            'labaKotor',
            'totalReturn',
            'labaBersih',
            'rincianPerObat',
            'pctLabaKotor',
            'mulai',
            'sampai',
            'cariObat'
        ));
    }

    // Export Excel Laporan Laba
    public function exportLaba(Request $request)
    {
        $mulai = $request->get('mulai_tanggal', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai_tanggal', now()->endOfMonth()->toDateString());

        $namaFile = 'laporan-laba-' . $mulai . '-sd-' . $sampai . '.xlsx';
        return Excel::download(
            new LaporanLabaExport($mulai, $sampai),
            $namaFile
        );
    }
}
