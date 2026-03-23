<?php

namespace App\Exports;

use App\Models\FeeCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyCollectionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return FeeCollection::with(['student.schoolClass', 'feeStructure.feeType'])
            ->whereDate('payment_date', $this->date)
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Receipt No',
            'Time',
            'Student Name',
            'Class',
            'Fee Type',
            'Amount',
            'Status',
        ];
    }

    public function map($collection): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $collection->receipt_no,
            $collection->created_at->format('h:i A'),
            $collection->student->full_name ?? 'N/A',
            $collection->student->schoolClass->name ?? 'N/A',
            $collection->feeStructure->feeType->name ?? 'N/A',
            number_format($collection->paid_amount, 2),
            'Paid',
        ];
    }

    public function title(): string
    {
        return 'Daily Collection - ' . \Carbon\Carbon::parse($this->date)->format('d-m-Y');
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
