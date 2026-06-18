<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKeluarExport implements
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
        protected ?string $supplierId = null
    ) {}

    public function title(): string
    {
        return 'Laporan Keluar';
    }

    public function query()
    {
        return PurchaseOrder::with('supplier')
            ->whereBetween('order_date', [$this->mulai, $this->sampai])
            ->where('status', '!=', 'cancelled')
            ->when($this->supplierId, fn($q) => $q->where('supplier_id', $this->supplierId))
            ->orderBy('order_date', 'desc');
    }

    public function headings(): array
    {
        return [
            ['LAPORAN KELUAR (UANG KELUAR / PEMBELIAN)'],
            ['Periode: ' . $this->mulai . ' s/d ' . $this->sampai],
            [],
            ['No.', 'Tanggal Pembelian', 'Nomor Invoice PO', 'Suplayer', 'Status', 'Total Pembelian'],
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            \Carbon\Carbon::parse($row->order_date)->translatedFormat('d M Y'),
            $row->invoice_number,
            $row->supplier?->name ?? '-',
            match($row->status) {
                'completed' => 'Selesai',
                'pending'   => 'Menunggu',
                'cancelled' => 'Dibatalkan',
                default     => ucfirst($row->status),
            },
            $row->total_amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_ACCOUNTING_IDR, // Total Pembelian
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
