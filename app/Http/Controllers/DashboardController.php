<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Default dashboard — redirects to Admin view as demo
     */
    public function index()
    {
        return redirect()->route('dashboard.admin');
    }

    /**
     * Dashboard view for Kasir role
     */
    public function kasir()
    {
        $data = [
            'role' => 'kasir',
            'userName' => 'Kasir',

            // Stat cards row 1
            'totalPenjualan'   => 'Rp 4.250k',
            'trendPenjualan'   => '+12% vs kemarin',
            'trendPenjualanUp' => true,

            'jumlahTransaksi'   => '84',
            'trendTransaksi'    => '+5% vs kemarin',
            'trendTransaksiUp'  => true,

            'mendekatiKadaluwarsa' => 12,
            'itemPerluDiperiksa'   => 12,

            'stokRendah'     => 5,
            'segeraRestock'  => 5,

            // Stat cards row 2
            'rataRataNilai'    => 'Rp 48.000',
            'obatTerjual'      => '342 Item',
            'resepDilayani'    => 28,
            'pelangganAktif'   => 76,

            // Inventory overview
            'stokAman'        => 1245,
            'perluRestock'    => 18,
            'dalamPemesanan'  => 45,

            // Top 5 obat terlaris
            'topObat' => [
                ['nama' => 'Amoxicillin 500mg', 'kategori' => 'Antibiotik',  'terjual' => 120],
                ['nama' => 'Paracetamol 500mg', 'kategori' => 'Analgesik',   'terjual' => 95],
                ['nama' => 'Vitamin C 1000mg',  'kategori' => 'Suplemen',    'terjual' => 82],
                ['nama' => 'Ibuprofen 400mg',   'kategori' => 'Analgesik',   'terjual' => 64],
                ['nama' => 'Cetirizine 10mg',   'kategori' => 'Antihistamin','terjual' => 50],
            ],

            // Aktivitas terbaru
            'aktivitas' => [
                ['ikon' => 'check',    'warna' => 'teal',   'text' => 'Transaksi #TRX-084 selesai',              'waktu' => '2 menit yang lalu'],
                ['ikon' => 'box',     'warna' => 'teal',   'text' => 'Stok masuk: Paracetamol Drop (50 botol)', 'waktu' => '15 menit yang lalu'],
                ['ikon' => 'warning', 'warna' => 'orange', 'text' => 'Peringatan: Stok Cefadroxil Sirup rendah','waktu' => '1 jam yang lalu'],
                ['ikon' => 'check',   'warna' => 'teal',   'text' => 'Transaksi #TRX-083 selesai',              'waktu' => '1 jam yang lalu'],
            ],

            // Obat mendekati kadaluwarsa
            'obatKadaluwarsa' => [
                ['nama' => 'Amoxicillin 500mg',  'batch' => 'Batch: B-10293', 'hari' => 10],
                ['nama' => 'Paracetamol Drop',   'batch' => 'Batch: P-88211', 'hari' => 45],
            ],

            // Obat stok rendah
            'obatStokRendah' => [
                ['nama' => 'Ibuprofen 400mg Tablet', 'kategori' => 'Analgesik',  'sisa' => 5,  'persen' => 15],
                ['nama' => 'Cefadroxil Sirup',       'kategori' => 'Antibiotik', 'sisa' => 2,  'persen' => 8],
            ],

            // 10 transaksi terakhir
            'transaksi' => [
                ['id' => 'TRX-084', 'waktu' => '10:45 AM', 'pelanggan' => 'Umum',          'item' => 3, 'total' => 'Rp 125.000', 'status' => 'Selesai'],
                ['id' => 'TRX-083', 'waktu' => '10:12 AM', 'pelanggan' => 'Tn. Budi',      'item' => 1, 'total' => 'Rp 45.000',  'status' => 'Selesai'],
                ['id' => 'TRX-082', 'waktu' => '09:50 AM', 'pelanggan' => 'Ny. Siti (Resep)', 'item' => 4, 'total' => 'Rp 320.000', 'status' => 'Selesai'],
            ],
        ];

        return view('pages.dashboard-kasir', compact('data'));
    }

    /**
     * Dashboard view for Admin role
     */
    public function admin()
    {
        $data = [
            'role' => 'admin',
            'userName' => 'Admin',

            // Stat cards row 1
            'totalObat'       => '1.245',
            'trendObat'       => '+3 Item Baru Bulan Ini',
            'totalStok'       => '15.820',
            'stokNote'        => 'Optimal level',
            'penjualanHariIni'=> 'Rp 8.750.000',
            'trendPenjualan'  => '+6% dari kemarin',
            'trendPenjualanUp'=> true,

            // Stat cards row 2
            'totalTransaksi'  => 156,
            'rataRataNilai'   => 'Rp 56.000',
            'supplierAktif'   => 18,
            'kategoriObat'    => 24,

            // Charts data (for JS)
            'penjualanChart' => [
                ['hari' => 'Sen', 'nilai' => 6200000],
                ['hari' => 'Sel', 'nilai' => 5100000],
                ['hari' => 'Rab', 'nilai' => 7800000],
                ['hari' => 'Kam', 'nilai' => 6500000],
                ['hari' => 'Jum', 'nilai' => 9200000],
                ['hari' => 'Sab', 'nilai' => 8400000],
                ['hari' => 'Min', 'nilai' => 7100000],
            ],

            'distribusiObat' => [
                ['label' => 'Analgesik',  'persen' => 40, 'warna' => '#009688'],
                ['label' => 'Antibiotik', 'persen' => 30, 'warna' => '#26a69a'],
                ['label' => 'Vitamin',    'persen' => 20, 'warna' => '#80cbc4'],
                ['label' => 'Lainnya',    'persen' => 10, 'warna' => '#b2dfdb'],
            ],

            // Kondisi inventaris
            'inventarisAman'     => 75,
            'inventarisMenengah' => 15,
            'inventarisRendah'   => 10,

            // Revenue
            'pendapatanMinggu' => 'Rp 45.2M',
            'pendapatanBulan'  => 'Rp 180.5M',
            'estimasiLaba'     => 'Rp 36.1M',

            // Top 5 obat terlaris
            'topObat' => [
                ['no' => 1, 'nama' => 'Amoxicillin 500mg', 'kategori' => 'Antibiotik',  'terjual' => 320],
                ['no' => 2, 'nama' => 'Paracetamol 500mg', 'kategori' => 'Analgesik',   'terjual' => 285],
                ['no' => 3, 'nama' => 'Vitamin C 1000mg',  'kategori' => 'Suplemen',    'terjual' => 210],
                ['no' => 4, 'nama' => 'Omeprazole 20mg',   'kategori' => 'Antasida',    'terjual' => 190],
                ['no' => 5, 'nama' => 'Ibuprofen 400mg',   'kategori' => 'Analgesik',   'terjual' => 160],
            ],

            // Aktivitas terbaru
            'aktivitas' => [
                ['warna' => 'teal',   'text' => 'Admin Utama menambahkan stok baru: Amoxil 500mg (50 box)',  'waktu' => '16 menit yang lalu'],
                ['warna' => 'teal',   'text' => 'Kasir 1 memproses transaksi #TRX-8932 senilai Rp 450.000', 'waktu' => '41 menit yang lalu'],
                ['warna' => 'orange', 'text' => 'Peringatan sistem: Stok Paracetamol hampir habis (Sisa: 5 strip)', 'waktu' => '3 jam yang lalu'],
            ],

            // 10 transaksi terakhir
            'transaksi' => [
                ['id' => '#TRX-8905', 'waktu' => '10:45 WIB', 'kasir' => 'Sri Kasir',  'total' => 'Rp 125.000', 'metode' => 'Qris',        'status' => 'Selesai'],
                ['id' => '#TRX-8904', 'waktu' => '10:53 WIB', 'kasir' => 'Budi Kasir', 'total' => 'Rp 45.000',  'metode' => 'Tunai',        'status' => 'Selesai'],
                ['id' => '#TRX-8903', 'waktu' => '10:11 WIB', 'kasir' => 'Sri Kasir',  'total' => 'Rp 350.000', 'metode' => 'Kartu Kredit', 'status' => 'Selesai'],
            ],

            // Footer stats
            'footerPenjualan'    => 'Rp 245.5M',
            'footerPembelian'    => 'Rp 120.2M',
            'footerPertumbuhan'  => '450 Baru',
            'trendFooterPenjualanUp' => true,
            'trendFooterPembelianDown' => true,
        ];

        return view('pages.dashboard-admin', compact('data'));
>>>>>>> e56d070 (feat: Add dashboard admin and kasir views and layouts)
    }
}
