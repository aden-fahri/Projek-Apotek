<?php

namespace App\Exports;

use App\Models\TransactionDetail;
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
        protected string $sampai
    ) {}

    public function title(): string
    {
        return 'Laporan Laba';
    }

    public function collection()
    {
        return TransactionDetail::join('medicines', 'transaction_details.medicine_id', '=', 'medicines.id')
            ->with(['medicine.unit', 'medicine.category'])
            ->whereHas('transaction', function ($q) {
                $q->whereBetween('transactions.transaction_date', [$this->mulai, $this->sampai])
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
            ->groupBy('transaction_details.medicine_id')
            ->orderByDesc('total_laba')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['LAPORAN LABA KOTOR & BERSIH'],
            ['Periode: ' . $this->mulai . ' s/d ' . $this->sampai],
            [],
            [
                'No.',
                'Nama Obat',
                'Kategori',
                'Satuan',
                'Jumlah Terjual',
                'HPP Rata-rata',
                'Harga Jual Rata-rata',
                'Total Penjualan',
                'Total HPP',
                'Total Laba',
            ],
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->medicine?->name ?? '-',
            $row->medicine?->category?->name ?? '-',
            $row->medicine?->unit?->abbreviation ?? '-',
            $row->total_qty,
            $row->avg_hpp,
            $row->avg_jual,
            $row->total_penjualan,
            $row->total_hpp,
            $row->total_laba,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp "* #,##0_-', // HPP Rata-rata
            'G' => '"Rp "* #,##0_-', // Harga Jual Rata-rata
            'H' => '"Rp "* #,##0_-', // Total Penjualan
            'I' => '"Rp "* #,##0_-', // Total HPP
            'J' => '"Rp "* #,##0_-', // Total Laba
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
