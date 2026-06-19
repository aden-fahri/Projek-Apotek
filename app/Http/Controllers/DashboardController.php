<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Default dashboard — redirects to Admin or Kasir view based on role
     */
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('dashboard.admin');
            } elseif (Auth::user()->role === 'kasir') {
                return redirect()->route('dashboard.kasir');
            }
        }
        return redirect()->route('login');
    }

    /**
     * Dashboard view for Kasir role
     */
    public function kasir()
    {
        $userId = Auth::id();

        // 1. Total penjualan hari ini oleh kasir ini
        $salesToday = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('transaction_date', today())
            ->sum('grand_total');

        $salesYesterday = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('transaction_date', today()->subDay())
            ->sum('grand_total');

        if ($salesYesterday > 0) {
            $diffSales = (($salesToday - $salesYesterday) / $salesYesterday) * 100;
            $trendPenjualan = ($diffSales >= 0 ? '+' : '') . round($diffSales, 1) . '% vs kemarin';
            $trendPenjualanUp = $diffSales >= 0;
        } else {
            $trendPenjualan = '+0% vs kemarin';
            $trendPenjualanUp = true;
        }

        // 2. Jumlah transaksi hari ini oleh kasir ini
        $trxToday = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('transaction_date', today())
            ->count();

        $trxYesterday = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('transaction_date', today()->subDay())
            ->count();

        if ($trxYesterday > 0) {
            $diffTrx = (($trxToday - $trxYesterday) / $trxYesterday) * 100;
            $trendTransaksi = ($diffTrx >= 0 ? '+' : '') . round($diffTrx, 1) . '% vs kemarin';
            $trendTransaksiUp = $diffTrx >= 0;
        } else {
            $trendTransaksi = '+0% vs kemarin';
            $trendTransaksiUp = true;
        }

        // 3. Rata-rata nilai transaksi oleh kasir ini
        $avgTrx = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->avg('grand_total') ?? 0;

        // 4. Jumlah obat terjual hari ini
        $itemsSoldToday = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.transaction_date', today())
            ->sum('transaction_details.quantity');

        // 5. Resep dilayani hari ini
        $resepServed = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.transaction_date', today())
            ->where('medicines.requires_prescription', true)
            ->count();

        // 6. Pelanggan unik yang dilayani hari ini
        $customersServed = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('transaction_date', today())
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->count('customer_name');

        if ($customersServed == 0 && $trxToday > 0) {
            $customersServed = $trxToday; // Fallback jika nama pelanggan kosong (umum)
        }

        // 7. Data Expired & Stok Rendah
        $mendekatiKadaluwarsa = DB::table('v_expiring_medicines')->count();
        $stokRendah = DB::table('v_medicine_stock_summary')->where('stock_status', 'Stok Rendah')->count();
        $totalStokQty = DB::table('medicine_stocks')->where('status', 'available')->sum('quantity') ?? 0;

        // 8. Top 5 Obat Terlaris oleh kasir ini
        $topMedicines = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->where('transactions.status', 'completed')
            ->where('transactions.user_id', $userId)
            ->select('medicines.name', 'categories.name as kategori', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->groupBy('medicines.id', 'medicines.name', 'categories.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        $topObat = [];
        foreach ($topMedicines as $m) {
            $topObat[] = [
                'nama' => $m->name,
                'kategori' => $m->kategori,
                'terjual' => (int)$m->total_qty
            ];
        }
        if (empty($topObat)) {
            $meds = Medicine::with('category')->limit(5)->get();
            foreach ($meds as $m) {
                $topObat[] = [
                    'nama' => $m->name,
                    'kategori' => $m->category->name ?? '-',
                    'terjual' => 0
                ];
            }
        }

        // 9. Aktivitas terbaru kasir ini
        $logs = DB::table('activity_logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        $aktivitas = [];
        foreach ($logs as $log) {
            $aktivitas[] = [
                'ikon' => $log->action === 'create' ? 'check' : ($log->action === 'delete' ? 'warning' : 'box'),
                'warna' => $log->action === 'delete' ? 'orange' : 'teal',
                'text' => $log->activity,
                'waktu' => \Carbon\Carbon::parse($log->created_at)->diffForHumans()
            ];
        }
        if (empty($aktivitas)) {
            $aktivitas[] = [
                'ikon' => 'check',
                'warna' => 'teal',
                'text' => 'Sesi kasir aktif.',
                'waktu' => 'Baru saja'
            ];
        }

        // 10. Obat mendekati kadaluwarsa list
        $obatKadaluwarsa = [];
        $expiring = DB::table('v_expiring_medicines')->limit(3)->get();
        foreach ($expiring as $e) {
            $obatKadaluwarsa[] = [
                'nama' => $e->medicine_name,
                'batch' => 'Batch: ' . $e->batch_number,
                'hari' => $e->days_until_expiry
            ];
        }

        // 11. Obat stok rendah list
        $obatStokRendah = [];
        $lowStocks = DB::table('v_medicine_stock_summary')
            ->where('stock_status', 'Stok Rendah')
            ->limit(3)
            ->get();
        foreach ($lowStocks as $ls) {
            $percent = $ls->min_stock > 0 ? round(($ls->total_stock / $ls->min_stock) * 100) : 0;
            $obatStokRendah[] = [
                'nama' => $ls->medicine_name,
                'kategori' => $ls->category_name ?? 'Lainnya',
                'sisa' => $ls->total_stock,
                'persen' => $percent
            ];
        }

        // 12. 10 transaksi terakhir kasir ini
        $latestTransactions = DB::table('transactions')
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        $transaksi = [];
        foreach ($latestTransactions as $t) {
            // Count items
            $itemCount = DB::table('transaction_details')->where('transaction_id', $t->id)->sum('quantity');
            $transaksi[] = [
                'id' => $t->invoice_number,
                'waktu' => \Carbon\Carbon::parse($t->transaction_date)->format('H:i A'),
                'pelanggan' => $t->customer_name ?: 'Umum',
                'item' => (int)$itemCount,
                'total' => 'Rp ' . number_format($t->grand_total, 0, ',', '.'),
                'status' => $t->status === 'completed' ? 'Selesai' : 'Dibatalkan'
            ];
        }

        $data = [
            'role'                 => 'kasir',
            'userName'             => Auth::user()->name,
            'totalPenjualan'       => 'Rp ' . number_format($salesToday / 1000, 0, ',', '.') . 'k',
            'trendPenjualan'       => $trendPenjualan,
            'trendPenjualanUp'     => $trendPenjualanUp,
            'jumlahTransaksi'      => (string)$trxToday,
            'trendTransaksi'       => $trendTransaksi,
            'trendTransaksiUp'     => $trendTransaksiUp,
            'mendekatiKadaluwarsa' => $mendekatiKadaluwarsa,
            'itemPerluDiperiksa'   => $mendekatiKadaluwarsa,
            'stokRendah'           => $stokRendah,
            'segeraRestock'        => $stokRendah,
            'rataRataNilai'        => 'Rp ' . number_format($avgTrx, 0, ',', '.'),
            'obatTerjual'          => $itemsSoldToday . ' Item',
            'resepDilayani'        => $resepServed,
            'pelangganAktif'       => $customersServed,
            'stokAman'             => $totalStokQty,
            'perluRestock'         => $stokRendah,
            'dalamPemesanan'       => DB::table('purchase_orders')->where('status', 'pending')->count(),
            'topObat'              => $topObat,
            'aktivitas'            => $aktivitas,
            'obatKadaluwarsa'      => $obatKadaluwarsa,
            'obatStokRendah'       => $obatStokRendah,
            'transaksi'            => $transaksi,
        ];

        return view('pages.dashboard-kasir', compact('data'));
    }

    /**
     * Dashboard view for Admin role
     */
    public function admin()
    {
        // 1. Total jenis obat aktif
        $totalObat = Medicine::where('is_active', true)->count();
        $newObatThisMonth = Medicine::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 2. Total stok
        $totalStok = MedicineStock::where('status', 'available')->sum('quantity') ?? 0;

        // 3. Penjualan Hari Ini (Admin melihat seluruh penjualan)
        $salesToday = DB::table('transactions')
            ->where('status', 'completed')
            ->whereDate('transaction_date', today())
            ->sum('grand_total');

        $salesYesterday = DB::table('transactions')
            ->where('status', 'completed')
            ->whereDate('transaction_date', today()->subDay())
            ->sum('grand_total');

        if ($salesYesterday > 0) {
            $diffSales = (($salesToday - $salesYesterday) / $salesYesterday) * 100;
            $trendPenjualan = ($diffSales >= 0 ? '+' : '') . round($diffSales, 1) . '% dari kemarin';
            $trendPenjualanUp = $diffSales >= 0;
        } else {
            $trendPenjualan = '0% dari kemarin';
            $trendPenjualanUp = true;
        }

        // 4. Total Transaksi (Bulan Ini)
        $totalTrxMonth = DB::table('transactions')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();

        // Rata-rata nilai transaksi
        $avgTrx = DB::table('transactions')
            ->where('status', 'completed')
            ->avg('grand_total') ?? 0;

        $supplierCount = Supplier::where('is_active', true)->count();
        $categoryCount = DB::table('categories')->count();

        // 5. Chart Penjualan 7 Hari Terakhir
        $penjualanChart = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $val = DB::table('transactions')
                ->whereDate('transaction_date', $date)
                ->where('status', 'completed')
                ->sum('grand_total');
            $penjualanChart[] = [
                'hari' => $dayNames[$date->dayOfWeek],
                'nilai' => (float)$val
            ];
        }

        // 6. Donut Chart Distribusi Obat per Kategori
        $categoriesCount = DB::table('v_medicine_stock_summary')
            ->select('category_name', DB::raw('count(*) as count'))
            ->groupBy('category_name')
            ->orderBy('count', 'desc')
            ->get();
        $totalMedCount = $categoriesCount->sum('count');
        $distribusiObat = [];
        $colors = ['#009688', '#26a69a', '#80cbc4', '#b2dfdb', '#e0f2f1'];
        $idx = 0;
        foreach ($categoriesCount as $cat) {
            if ($totalMedCount > 0) {
                $distribusiObat[] = [
                    'label' => $cat->category_name ?: 'Lainnya',
                    'persen' => round(($cat->count / $totalMedCount) * 100),
                    'warna' => $colors[$idx % count($colors)]
                ];
                $idx++;
            }
        }
        if (empty($distribusiObat)) {
            $distribusiObat[] = ['label' => 'Belum ada', 'persen' => 100, 'warna' => '#009688'];
        }

        // 7. Kondisi inventaris
        $lowStockCount = DB::table('v_medicine_stock_summary')->where('stock_status', 'Stok Rendah')->count();
        $outStockCount = DB::table('v_medicine_stock_summary')->where('stock_status', 'Habis')->count();
        $safeStockCount = $totalObat - $lowStockCount - $outStockCount;

        $inventarisAman = $totalObat > 0 ? round(($safeStockCount / $totalObat) * 100) : 100;
        $inventarisMenengah = $totalObat > 0 ? round(($lowStockCount / $totalObat) * 100) : 0;
        $inventarisRendah = $totalObat > 0 ? round(($outStockCount / $totalObat) * 100) : 0;

        // 8. Pendapatan Minggu & Bulan
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $revenueWeek = DB::table('transactions')->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])->where('status', 'completed')->sum('grand_total');
        $revenueMonth = DB::table('transactions')->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->where('status', 'completed')->sum('grand_total');

        // Estimasi laba kotor bulan ini
        $totalSalesVal = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->whereYear('transactions.transaction_date', now()->year)
            ->sum('transaction_details.subtotal');

        $totalCostVal = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->where('transactions.status', 'completed')
            ->whereMonth('transactions.transaction_date', now()->month)
            ->whereYear('transactions.transaction_date', now()->year)
            ->select(DB::raw('SUM(transaction_details.quantity * medicines.purchase_price) as cost'))
            ->first()->cost ?? 0;

        $estLaba = $totalSalesVal - $totalCostVal;

        // 9. Top 5 Obat Terlaris (All time)
        $topMedicines = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->where('transactions.status', 'completed')
            ->select('medicines.name', 'categories.name as kategori', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->groupBy('medicines.id', 'medicines.name', 'categories.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        $topObat = [];
        $no = 1;
        foreach ($topMedicines as $m) {
            $topObat[] = [
                'no' => $no++,
                'nama' => $m->name,
                'kategori' => $m->kategori,
                'terjual' => (int)$m->total_qty
            ];
        }
        if (empty($topObat)) {
            $meds = Medicine::with('category')->limit(5)->get();
            foreach ($meds as $m) {
                $topObat[] = [
                    'no' => $no++,
                    'nama' => $m->name,
                    'kategori' => $m->category->name ?? '-',
                    'terjual' => 0
                ];
            }
        }

        // 10. Aktivitas terbaru
        $logs = DB::table('activity_logs')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $aktivitas = [];
        foreach ($logs as $log) {
            $aktivitas[] = [
                'warna' => $log->action === 'delete' ? 'orange' : 'teal',
                'text' => $log->activity,
                'waktu' => \Carbon\Carbon::parse($log->created_at)->diffForHumans()
            ];
        }
        if (empty($aktivitas)) {
            $aktivitas[] = [
                'warna' => 'teal',
                'text' => 'Sistem aktif. Belum ada aktivitas yang tercatat.',
                'waktu' => 'Baru saja'
            ];
        }

        // 11. 10 Transaksi Terakhir
        $latestTransactions = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->orderBy('transactions.transaction_date', 'desc')
            ->select('transactions.*', 'users.name as kasir_name')
            ->limit(10)
            ->get();

        $transaksi = [];
        foreach ($latestTransactions as $t) {
            $transaksi[] = [
                'id' => '#' . $t->invoice_number,
                'waktu' => \Carbon\Carbon::parse($t->transaction_date)->format('H:i') . ' WIB',
                'kasir' => $t->kasir_name ?? 'Kasir',
                'total' => 'Rp ' . number_format($t->grand_total, 0, ',', '.'),
                'metode' => ucfirst($t->payment_method),
                'status' => $t->status === 'completed' ? 'Selesai' : 'Dibatalkan'
            ];
        }

        // 12. Footer stats
        $footerPenjualan = DB::table('transactions')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('grand_total');

        $footerPembelian = DB::table('purchase_orders')
            ->where('status', 'completed')
            ->whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->sum('total_amount');

        $footerPertumbuhan = DB::table('transactions')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->distinct()
            ->count('customer_name');

        $data = [
            'role'              => 'admin',
            'userName'          => Auth::user()->name,
            'totalObat'         => number_format($totalObat, 0, ',', '.'),
            'trendObat'         => "+{$newObatThisMonth} Item Baru Bulan Ini",
            'totalStok'         => number_format($totalStok, 0, ',', '.'),
            'stokNote'          => $lowStockCount > 0 ? "Perlu restock ({$lowStockCount} item)" : "Tingkat stok optimal",
            'penjualanHariIni'  => 'Rp ' . number_format($salesToday, 0, ',', '.'),
            'trendPenjualan'    => $trendPenjualan,
            'trendPenjualanUp'  => $trendPenjualanUp,
            'totalTransaksi'    => $totalTrxMonth,
            'rataRataNilai'     => 'Rp ' . number_format($avgTrx, 0, ',', '.'),
            'supplierAktif'     => $supplierCount,
            'kategoriObat'      => $categoryCount,
            'penjualanChart'    => $penjualanChart,
            'distribusiObat'    => $distribusiObat,
            'inventarisAman'     => $inventarisAman,
            'inventarisMenengah' => $inventarisMenengah,
            'inventarisRendah'   => $inventarisRendah,
            'pendapatanMinggu' => $revenueWeek >= 1000000 ? 'Rp ' . round($revenueWeek / 1000000, 1) . 'jt' : 'Rp ' . number_format($revenueWeek / 1000, 0) . 'rb',
            'pendapatanBulan'  => $revenueMonth >= 1000000 ? 'Rp ' . round($revenueMonth / 1000000, 1) . 'jt' : 'Rp ' . number_format($revenueMonth / 1000, 0) . 'rb',
            'estimasiLaba'     => $estLaba >= 1000000 ? 'Rp ' . round($estLaba / 1000000, 1) . 'jt' : 'Rp ' . number_format($estLaba / 1000, 0) . 'rb',
            'topObat'          => $topObat,
            'aktivitas'        => $aktivitas,
            'transaksi'        => $transaksi,
            'footerPenjualan'  => $footerPenjualan >= 1000000 ? 'Rp ' . round($footerPenjualan / 1000000, 1) . 'jt' : 'Rp ' . number_format($footerPenjualan / 1000, 0) . 'rb',
            'footerPembelian'  => $footerPembelian >= 1000000 ? 'Rp ' . round($footerPembelian / 1000000, 1) . 'jt' : 'Rp ' . number_format($footerPembelian / 1000, 0) . 'rb',
            'footerPertumbuhan' => "{$footerPertumbuhan} Transaksi",
        ];

        return view('pages.dashboard-admin', compact('data'));
    }
}
