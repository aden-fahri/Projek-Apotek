<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineStock;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // =========================================================================
    // PUBLIC ROUTES
    // =========================================================================

    /**
     * Redirect ke dashboard sesuai role pengguna yang sedang login.
     */
    public function index()
    {
        $role = Auth::user()?->role;

        return match ($role) {
            'admin' => redirect()->route('dashboard.admin'),
            'kasir' => redirect()->route('dashboard.kasir'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Dashboard untuk Kasir.
     */
    public function kasir()
    {
        $userId = Auth::id();

        // --- Statistik Penjualan Hari Ini ---
        $salesToday     = $this->sumTransaksi(today(), $userId);
        $salesYesterday = $this->sumTransaksi(today()->subDay(), $userId);
        [$trendPenjualan, $trendPenjualanUp] = $this->hitungTrend($salesToday, $salesYesterday, 'vs kemarin');

        // --- Statistik Transaksi Hari Ini ---
        $trxToday     = $this->countTransaksi(today(), $userId);
        $trxYesterday = $this->countTransaksi(today()->subDay(), $userId);
        [$trendTransaksi, $trendTransaksiUp] = $this->hitungTrend($trxToday, $trxYesterday, 'vs kemarin');

        // --- Metrik Tambahan ---
        $avgTrx = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->avg('grand_total') ?? 0;

        $itemsSoldToday = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.transaction_date', today())
            ->sum('transaction_details.quantity');

        $resepServed = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.transaction_date', today())
            ->where('medicines.requires_prescription', true)
            ->count();

        $customersServed = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('transaction_date', today())
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->distinct()
            ->count('customer_name');

        // Fallback: jika nama pelanggan kosong, gunakan jumlah transaksi
        if ($customersServed === 0 && $trxToday > 0) {
            $customersServed = $trxToday;
        }

        // --- Stok & Kadaluwarsa ---
        $mendekatiKadaluwarsa = DB::table('v_expiring_medicines')->count();
        $stokRendah           = DB::table('v_medicine_stock_summary')->where('stock_status', 'Stok Rendah')->count();
        $totalStokQty         = DB::table('medicine_stocks')->where('status', 'available')->sum('quantity') ?? 0;

        // --- Transaksi Terakhir ---
        $transaksi = DB::table('transactions')
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'id'        => $t->invoice_number,
                'waktu'     => Carbon::parse($t->transaction_date)->format('H:i A'),
                'pelanggan' => $t->customer_name ?: 'Umum',
                'item'      => (int) DB::table('transaction_details')->where('transaction_id', $t->id)->sum('quantity'),
                'total'     => $this->rp($t->grand_total),
                'status'    => $t->status === 'completed' ? 'Selesai' : 'Dibatalkan',
            ])
            ->toArray();

        $data = [
            // Identitas
            'role'                 => 'kasir',
            'userName'             => Auth::user()->name,

            // Statistik utama
            'totalPenjualan'       => $this->rp($salesToday),
            'trendPenjualan'       => $trendPenjualan,
            'trendPenjualanUp'     => $trendPenjualanUp,
            'jumlahTransaksi'      => (string) $trxToday,
            'trendTransaksi'       => $trendTransaksi,
            'trendTransaksiUp'     => $trendTransaksiUp,
            'rataRataNilai'        => $this->rp($avgTrx),
            'obatTerjual'          => $itemsSoldToday . ' Item',
            'resepDilayani'        => $resepServed,
            'pelangganAktif'       => $customersServed,

            // Stok & kadaluwarsa
            'mendekatiKadaluwarsa' => $mendekatiKadaluwarsa,
            'itemPerluDiperiksa'   => $mendekatiKadaluwarsa,
            'stokRendah'           => $stokRendah,
            'segeraRestock'        => $stokRendah,
            'stokAman'             => $totalStokQty,
            'perluRestock'         => $stokRendah,
            'dalamPemesanan'       => DB::table('purchase_orders')->where('status', 'pending')->count(),

            // Daftar
            'topObat'              => $this->getTopObat($userId),
            'aktivitas'            => $this->getAktivitas($userId, limit: 4, defaultText: 'Sesi kasir aktif.'),
            'obatKadaluwarsa'      => $this->getObatKadaluwarsa(),
            'obatStokRendah'       => $this->getObatStokRendah(),
            'transaksi'            => $transaksi,

            // Chart
            'penjualanChart'       => $this->getPenjualanChart($userId),
            'distribusiObat'       => $this->getDistribusiObat(),
        ];

        return view('pages.dashboard-kasir', compact('data'));
    }

    /**
     * Dashboard untuk Admin.
     */
    public function admin()
    {
        // --- Obat & Stok ---
        $totalObat        = Medicine::where('is_active', true)->count();
        $newObatThisMonth = Medicine::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $totalStok        = MedicineStock::where('status', 'available')->sum('quantity') ?? 0;

        // --- Penjualan Hari Ini ---
        $salesToday     = $this->sumTransaksi(today());
        $salesYesterday = $this->sumTransaksi(today()->subDay());
        [$trendPenjualan, $trendPenjualanUp] = $this->hitungTrend($salesToday, $salesYesterday, 'dari kemarin');

        // --- Transaksi Bulan Ini ---
        $totalTrxMonth = DB::table('transactions')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();

        $avgTrx = DB::table('transactions')->where('status', 'completed')->avg('grand_total') ?? 0;

        // --- Master Data ---
        $supplierCount = Supplier::where('is_active', true)->count();
        $categoryCount = DB::table('categories')->count();

        // --- Kondisi Inventaris ---
        $lowStockCount  = DB::table('v_medicine_stock_summary')->where('stock_status', 'Stok Rendah')->count();
        $outStockCount  = DB::table('v_medicine_stock_summary')->where('stock_status', 'Habis')->count();
        $safeStockCount = $totalObat - $lowStockCount - $outStockCount;

        $inventarisAman     = $totalObat > 0 ? round(($safeStockCount / $totalObat) * 100) : 100;
        $inventarisMenengah = $totalObat > 0 ? round(($lowStockCount  / $totalObat) * 100) : 0;
        $inventarisRendah   = $totalObat > 0 ? round(($outStockCount  / $totalObat) * 100) : 0;

        // --- Pendapatan ---
        $revenueWeek = DB::table('transactions')
            ->whereBetween('transaction_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'completed')
            ->sum('grand_total');

        $revenueMonth = DB::table('transactions')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->where('status', 'completed')
            ->sum('grand_total');

        // --- Estimasi Laba Kotor Bulan Ini ---
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

        // --- Footer Stats ---
        $footerPenjualan  = $this->footerStat('transactions', 'grand_total', 'transaction_date');
        $footerPembelian  = $this->footerStat('purchase_orders', 'total_amount', 'order_date');
        $footerPertumbuhan = DB::table('transactions')
            ->where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->distinct()
            ->count('customer_name');

        [$footerPenjualanTrend,  $footerPenjualanTrendUp]  = $this->hitungFooterTrend('transactions',    'grand_total',  'transaction_date', $footerPenjualan);
        [$footerPembelianTrend,  $footerPembelianTrendUp]  = $this->hitungFooterTrend('purchase_orders', 'total_amount', 'order_date',       $footerPembelian);
        [$footerPertumbuhanTrend, $footerPertumbuhanTrendUp] = $this->hitungFooterTrendCount('transactions', 'transaction_date', $footerPertumbuhan);

        // --- Transaksi Terakhir ---
        $transaksi = DB::table('transactions')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select('transactions.*', 'users.name as kasir_name')
            ->orderBy('transactions.transaction_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'id'     => '#' . $t->invoice_number,
                'waktu'  => Carbon::parse($t->transaction_date)->format('H:i') . ' WIB',
                'kasir'  => $t->kasir_name ?? 'Kasir',
                'total'  => $this->rp($t->grand_total),
                'metode' => ucfirst($t->payment_method),
                'status' => $t->status === 'completed' ? 'Selesai' : 'Dibatalkan',
            ])
            ->toArray();

        $data = [
            // Identitas
            'role'     => 'admin',
            'userName' => Auth::user()->name,

            // Kartu statistik besar
            'totalObat'        => number_format($totalObat, 0, ',', '.'),
            'trendObat'        => "+{$newObatThisMonth} Item Baru Bulan Ini",
            'totalStok'        => number_format($totalStok, 0, ',', '.'),
            'stokNote'         => $lowStockCount > 0 ? "Perlu restock ({$lowStockCount} item)" : 'Tingkat stok optimal',
            'penjualanHariIni' => $this->rp($salesToday),
            'trendPenjualan'   => $trendPenjualan,
            'trendPenjualanUp' => $trendPenjualanUp,

            // Mini stat cards
            'totalTransaksi' => $totalTrxMonth,
            'rataRataNilai'  => $this->rp($avgTrx),
            'supplierAktif'  => $supplierCount,
            'kategoriObat'   => $categoryCount,

            // Kondisi inventaris
            'inventarisAman'     => $inventarisAman,
            'inventarisMenengah' => $inventarisMenengah,
            'inventarisRendah'   => $inventarisRendah,

            // Pendapatan
            'pendapatanMinggu' => $this->rp($revenueWeek),
            'pendapatanBulan'  => $this->rp($revenueMonth),
            'estimasiLaba'     => $this->rp($estLaba),

            // Daftar
            'topObat'   => $this->getTopObat(withNo: true),
            'aktivitas' => $this->getAktivitas(defaultText: 'Sistem aktif. Belum ada aktivitas yang tercatat.'),
            'transaksi' => $transaksi,

            // Chart
            'penjualanChart' => $this->getPenjualanChart(),
            'distribusiObat' => $this->getDistribusiObat(),

            // Footer
            'footerPenjualan'          => $this->rp($footerPenjualan),
            'footerPenjualanTrend'     => $footerPenjualanTrend,
            'footerPenjualanTrendUp'   => $footerPenjualanTrendUp,
            'footerPembelian'          => $this->rp($footerPembelian),
            'footerPembelianTrend'     => $footerPembelianTrend,
            'footerPembelianTrendUp'   => $footerPembelianTrendUp,
            'footerPertumbuhan'        => "{$footerPertumbuhan} Transaksi",
            'footerPertumbuhanTrend'   => $footerPertumbuhanTrend,
            'footerPertumbuhanTrendUp' => $footerPertumbuhanTrendUp,

            // Alert
            'lowStockCount'        => $lowStockCount,
            'mendekatiKadaluwarsa' => DB::table('v_expiring_medicines')->count(),
        ];

        return view('pages.dashboard-admin', compact('data'));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Format angka ke format Rupiah Indonesia (Rp 1.000.000).
     */
    private function rp(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    /**
     * Hitung total penjualan pada tanggal tertentu, opsional filter by user.
     */
    private function sumTransaksi($date, ?int $userId = null): float
    {
        return (float) DB::table('transactions')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->where('status', 'completed')
            ->whereDate('transaction_date', $date)
            ->sum('grand_total');
    }

    /**
     * Hitung jumlah transaksi pada tanggal tertentu, opsional filter by user.
     */
    private function countTransaksi($date, ?int $userId = null): int
    {
        return (int) DB::table('transactions')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->where('status', 'completed')
            ->whereDate('transaction_date', $date)
            ->count();
    }

    /**
     * Hitung trend persentase antara nilai sekarang vs sebelumnya.
     * Mengembalikan [string $label, bool $isUp].
     */
    private function hitungTrend(float $current, float $previous, string $suffix): array
    {
        if ($previous > 0) {
            $diff  = (($current - $previous) / $previous) * 100;
            $label = ($diff >= 0 ? '+' : '') . round($diff, 1) . "% {$suffix}";
            return [$label, $diff >= 0];
        }

        return ["0% {$suffix}", true];
    }

    /**
     * Hitung total footer stat (sum) bulan ini pada tabel tertentu.
     */
    private function footerStat(string $table, string $column, string $dateColumn): float
    {
        return (float) DB::table($table)
            ->where('status', 'completed')
            ->whereMonth($dateColumn, now()->month)
            ->whereYear($dateColumn, now()->year)
            ->sum($column);
    }

    /**
     * Hitung trend bulan ini vs bulan lalu untuk footer (berbasis sum).
     * Mengembalikan [string $trend, bool $isUp].
     */
    private function hitungFooterTrend(string $table, string $column, string $dateColumn, float $current): array
    {
        $previous = (float) DB::table($table)
            ->where('status', 'completed')
            ->whereMonth($dateColumn, now()->subMonth()->month)
            ->whereYear($dateColumn, now()->subMonth()->year)
            ->sum($column);

        if ($previous > 0) {
            $diff = (($current - $previous) / $previous) * 100;
            return [round(abs($diff), 1) . '%', $diff >= 0];
        }

        return ['0.0%', true];
    }

    /**
     * Hitung trend bulan ini vs bulan lalu untuk footer (berbasis count distinct).
     * Mengembalikan [string $trend, bool $isUp].
     */
    private function hitungFooterTrendCount(string $table, string $dateColumn, int $current): array
    {
        $previous = (int) DB::table($table)
            ->where('status', 'completed')
            ->whereMonth($dateColumn, now()->subMonth()->month)
            ->whereYear($dateColumn, now()->subMonth()->year)
            ->distinct()
            ->count('customer_name');

        if ($previous > 0) {
            $diff = (($current - $previous) / $previous) * 100;
            return [round(abs($diff), 1) . '%', $diff >= 0];
        }

        return ['0.0%', true];
    }

    /**
     * Data chart penjualan 7 hari terakhir, opsional filter by user (kasir).
     */
    private function getPenjualanChart(?int $userId = null): array
    {
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $chart    = [];

        for ($i = 6; $i >= 0; $i--) {
            $date  = today()->subDays($i);
            $nilai = (float) DB::table('transactions')
                ->when($userId, fn ($q) => $q->where('user_id', $userId))
                ->whereDate('transaction_date', $date)
                ->where('status', 'completed')
                ->sum('grand_total');

            $chart[] = ['hari' => $dayNames[$date->dayOfWeek], 'nilai' => $nilai];
        }

        return $chart;
    }

    /**
     * Data donut chart distribusi obat per kategori.
     */
    private function getDistribusiObat(): array
    {
        $colors     = ['#009688', '#26a69a', '#80cbc4', '#b2dfdb', '#e0f2f1'];
        $categories = DB::table('v_medicine_stock_summary')
            ->select('category_name', DB::raw('count(*) as count'))
            ->groupBy('category_name')
            ->orderBy('count', 'desc')
            ->get();

        $total  = $categories->sum('count');
        $result = [];

        foreach ($categories as $i => $cat) {
            if ($total > 0) {
                $result[] = [
                    'label'  => $cat->category_name ?: 'Lainnya',
                    'persen' => round(($cat->count / $total) * 100),
                    'warna'  => $colors[$i % count($colors)],
                ];
            }
        }

        return $result ?: [['label' => 'Belum ada', 'persen' => 100, 'warna' => '#009688']];
    }

    /**
     * Daftar top 5 obat terlaris.
     *
     * @param  int|null  $userId   Filter by kasir (null = semua)
     * @param  bool      $withNo   Sertakan nomor urut (untuk admin)
     */
    private function getTopObat(?int $userId = null, bool $withNo = false): array
    {
        $medicines = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('medicines',    'transaction_details.medicine_id',    '=', 'medicines.id')
            ->join('categories',   'medicines.category_id',              '=', 'categories.id')
            ->where('transactions.status', 'completed')
            ->when($userId, fn ($q) => $q->where('transactions.user_id', $userId))
            ->select('medicines.name', 'categories.name as kategori', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->groupBy('medicines.id', 'medicines.name', 'categories.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        if ($medicines->isEmpty()) {
            $medicines = Medicine::with('category')->limit(5)->get()->map(fn ($m) => (object) [
                'name'     => $m->name,
                'kategori' => $m->category->name ?? '-',
                'total_qty' => 0,
            ]);
        }

        return $medicines->values()->map(function ($m, $i) use ($withNo) {
            $row = ['nama' => $m->name, 'kategori' => $m->kategori, 'terjual' => (int) $m->total_qty];
            if ($withNo) {
                $row = array_merge(['no' => $i + 1], $row);
            }
            return $row;
        })->toArray();
    }

    /**
     * Daftar aktivitas terbaru dari activity_logs.
     *
     * @param  int|null  $userId       Filter by user (null = semua)
     * @param  int       $limit        Jumlah item
     * @param  string    $defaultText  Teks fallback jika tidak ada log
     */
    private function getAktivitas(?int $userId = null, int $limit = 4, string $defaultText = 'Sistem aktif.'): array
    {
        $logs = DB::table('activity_logs')
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($logs->isEmpty()) {
            return [['warna' => 'teal', 'text' => $defaultText, 'waktu' => 'Baru saja']];
        }

        return $logs->map(fn ($log) => [
            'warna' => $log->action === 'delete' ? 'orange' : 'teal',
            'text'  => $log->activity,
            'waktu' => Carbon::parse($log->created_at)->diffForHumans(),
        ])->toArray();
    }

    /**
     * Daftar obat mendekati kadaluwarsa (maks 3 item).
     */
    private function getObatKadaluwarsa(): array
    {
        return DB::table('v_expiring_medicines')
            ->limit(3)
            ->get()
            ->map(fn ($e) => [
                'nama'  => $e->medicine_name,
                'batch' => 'Batch: ' . $e->batch_number,
                'hari'  => $e->days_until_expiry,
            ])
            ->toArray();
    }

    /**
     * Daftar obat dengan stok rendah (maks 3 item).
     */
    private function getObatStokRendah(): array
    {
        return DB::table('v_medicine_stock_summary')
            ->where('stock_status', 'Stok Rendah')
            ->limit(3)
            ->get()
            ->map(fn ($ls) => [
                'nama'     => $ls->medicine_name,
                'kategori' => $ls->category_name ?? 'Lainnya',
                'sisa'     => $ls->total_stock,
                'persen'   => $ls->min_stock > 0 ? round(($ls->total_stock / $ls->min_stock) * 100) : 0,
            ])
            ->toArray();
    }
}
