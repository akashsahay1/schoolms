<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransportFeeCollectionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $collections;

    public function __construct($collections)
    {
        $this->collections = $collections;
    }

    public function collection()
    {
        return $this->collections;
    }

    public function headings(): array
    {
        return [
            'Receipt No',
            'Student Name',
            'Admission No',
            'Route',
            'Month',
            'Amount',
            'Status',
            'Payment Date',
        ];
    }

    public function map($collection): array
    {
        return [
            $collection->receipt_number ?? '-',
            ($collection->student->first_name ?? '') . ' ' . ($collection->student->last_name ?? ''),
            $collection->student->admission_no ?? 'N/A',
            $collection->transportFee->route->title ?? '-',
            $collection->month ?? '-',
            number_format($collection->paid_amount, 2),
            ucfirst($collection->status),
            $collection->payment_date ? $collection->payment_date->format('d-m-Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }
}
