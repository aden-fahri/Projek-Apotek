<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use App\Models\Transaction;
use App\Models\StockReturn;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanLabaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize,
    WithTitle
{
    public function __construct(
        protected string $mulai,
        protected string $sampai,
        protected string $jenis = 'Semua'
    ) {}

    public function title(): string
    {
        return 'Buku Besar';
    }

    public function collection()
    {
        $bukuBesar = collect();

        // 1. Ambil data Penjualan (Masuk)
        if ($this->jenis === 'Semua' || $this->jenis === 'Jual Obat') {
            $penjualan = Transaction::with(['kasir', 'details.medicine'])->whereBetween('transaction_date', [$this->mulai . ' 00:00:00', $this->sampai . ' 23:59:59'])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($item) {
                    $rincianStr = $item->details->map(function($d) {
                        $med = $d->medicine ? $d->medicine->name : '-';
                        return "{$med} ({$d->quantity})";
                    })->implode(', ');

                    return [
                        'tanggal' => $item->transaction_date,
                        'referensi' => $item->invoice_number,
                        'jenis' => 'Jual Obat',
                        'keterangan' => 'Penjualan Kasir. Rincian: ' . $rincianStr,
                        'oleh' => $item->kasir ? $item->kasir->name : 'Sistem',
                        'debit' => $item->grand_total,
                        'kredit' => 0,
                    ];
                });
            $bukuBesar = $bukuBesar->concat($penjualan);
        }

        // 2. Ambil data Pembelian (Keluar)
        if ($this->jenis === 'Semua' || $this->jenis === 'Beli Obat') {
            $pembelian = PurchaseOrder::with(['supplier', 'user', 'details.medicine'])->whereBetween('order_date', [$this->mulai, $this->sampai])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->map(function ($item) {
                    $rincianStr = $item->details->map(function($d) {
                        $med = $d->medicine ? $d->medicine->name : '-';
                        return "{$med} ({$d->quantity})";
                    })->implode(', ');

                    return [
                        'tanggal' => \Carbon\Carbon::parse($item->order_date)->startOfDay(),
                        'referensi' => $item->invoice_number,
                        'jenis' => 'Beli Obat',
                        'keterangan' => 'Pembelian ke Supplier ' . ($item->supplier ? $item->supplier->name : '') . '. Rincian: ' . $rincianStr,
                        'oleh' => $item->user ? $item->user->name : 'Sistem',
                        'debit' => 0,
                        'kredit' => $item->total_amount,
                    ];
                });
            $bukuBesar = $bukuBesar->concat($pembelian);
        }

        // 3. Ambil data Retur (Masuk)
        if ($this->jenis === 'Semua' || $this->jenis === 'Retur Obat') {
            $retur = StockReturn::with(['supplier', 'user', 'details.medicine'])->whereBetween('return_date', [$this->mulai, $this->sampai])
                ->where('status', '!=', 'ditolak')
                ->get()
                ->map(function ($item) {
                    $rincianStr = $item->details->map(function($d) {
                        $med = $d->medicine ? $d->medicine->name : '-';
                        return "{$med} ({$d->quantity})";
                    })->implode(', ');

                    return [
                        'tanggal' => \Carbon\Carbon::parse($item->return_date)->startOfDay(),
                        'referensi' => $item->return_number,
                        'jenis' => 'Kembaliin Obat',
                        'keterangan' => 'Retur ke Supplier ' . ($item->supplier ? $item->supplier->name : '') . ': ' . $item->reason . '. Rincian: ' . $rincianStr,
                        'oleh' => $item->user ? $item->user->name : 'Sistem',
                        'debit' => $item->total_amount,
                        'kredit' => 0,
                    ];
                });
            $bukuBesar = $bukuBesar->concat($retur);
        }

        $bukuBesar = $bukuBesar->sortBy('tanggal')->values();

        $saldoAwalPenjualan = Transaction::where('transaction_date', '<', $this->mulai . ' 00:00:00')
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');
        $saldoAwalPembelian = PurchaseOrder::where('order_date', '<', $this->mulai)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        $saldoAwalRetur = StockReturn::where('return_date', '<', $this->mulai)
            ->where('status', '!=', 'ditolak')
            ->sum('total_amount');

        $saldoAwal = $saldoAwalPenjualan - $saldoAwalPembelian + $saldoAwalRetur;
        $saldoBerjalan = $saldoAwal;

        // Bikin list untuk di-export
        $exportData = collect();
        $exportData->push([
            'tanggal' => '',
            'referensi' => '',
            'jenis' => '',
            'keterangan' => 'SALDO AWAL SEBELUM ' . \Carbon\Carbon::parse($this->mulai)->format('d M Y'),
            'oleh' => '',
            'debit' => 0,
            'kredit' => 0,
            'saldo' => $saldoAwal,
        ]);

        foreach ($bukuBesar as $item) {
            $saldoBerjalan += $item['debit'] - $item['kredit'];
            $exportData->push([
                'tanggal' => \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y H:i'),
                'referensi' => $item['referensi'],
                'jenis' => $item['jenis'],
                'keterangan' => $item['keterangan'],
                'oleh' => $item['oleh'],
                'debit' => $item['debit'],
                'kredit' => $item['kredit'],
                'saldo' => $saldoBerjalan,
            ]);
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN BUKU BESAR (TRANSAKSI KAS)'],
            ['Periode: ' . $this->mulai . ' s/d ' . $this->sampai . ' | Filter: ' . $this->jenis],
            [],
            [
                'No.',
                'Tanggal',
                'No. Referensi',
                'Jenis',
                'Keterangan',
                'Oleh',
                'Masuk',
                'Keluar',
                'Saldo Akhir',
            ],
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        
        if ($row['tanggal'] == '') {
            return [
                '-',
                '',
                '',
                '',
                $row['keterangan'],
                '',
                '-',
                '-',
                $row['saldo']
            ];
        }

        $no++;
        return [
            $no,
            $row['tanggal'],
            $row['referensi'],
            $row['jenis'],
            $row['keterangan'],
            $row['oleh'],
            $row['debit'],
            $row['kredit'],
            $row['saldo'],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '"Rp "* #,##0_-', // Masuk
            'H' => '"Rp "* #,##0_-', // Keluar
            'I' => '"Rp "* #,##0_-', // Saldo
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
            4 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '0D9488']],
                'alignment' => ['horizontal' => 'center'],
            ],
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => '334155']]], // Baris Saldo Awal dibuat tebal
        ];
    }
}
