<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanMasukExport implements
    FromQuery,
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
        protected ?string $kasirId = null,
        protected ?string $metodePembayaran = null
    ) {}

    public function title(): string
    {
        return 'Laporan Masuk';
    }

    public function query()
    {
        return Transaction::with('kasir')
            ->whereBetween('transaction_date', [$this->mulai, $this->sampai])
            ->where('status', '!=', 'cancelled')
            ->when($this->kasirId, fn($q) => $q->where('user_id', $this->kasirId))
            ->when($this->metodePembayaran, fn($q) => $q->where('payment_method', $this->metodePembayaran))
            ->orderBy('transaction_date', 'desc');
    }

    public function headings(): array
    {
        return [
            ['LAPORAN MASUK (UANG MASUK / PENJUALAN)'],
            ['Periode: ' . $this->mulai . ' s/d ' . $this->sampai],
            [],
            ['No.', 'Tanggal & Waktu', 'Nomor Invoice', 'Kasir', 'Metode Pembayaran', 'Status', 'Total Penjualan'],
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            \Carbon\Carbon::parse($row->transaction_date)->translatedFormat('d M Y, H:i'),
            $row->invoice_number,
            $row->kasir?->name ?? '-',
            $row->payment_method,
            match($row->status) {
                'selesai'   => 'Selesai',
                'cancelled' => 'Dibatalkan',
                default     => ucfirst($row->status),
            },
            $row->grand_total,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '"Rp "* #,##0_-', // Total Penjualan
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
        ];
    }
}
