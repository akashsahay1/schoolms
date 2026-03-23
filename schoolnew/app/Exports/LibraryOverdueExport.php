<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LibraryOverdueExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Book Title',
            'Student Name',
            'Issue Date',
            'Due Date',
            'Overdue Days',
            'Calculated Fine',
        ];
    }

    public function map($record): array
    {
        return [
            $record->book->title ?? 'N/A',
            $record->student->full_name ?? $record->student->name ?? 'N/A',
            $record->issue_date ? $record->issue_date->format('d-m-Y') : '',
            $record->due_date ? $record->due_date->format('d-m-Y') : '',
            $record->overdue_days ?? 0,
            $record->calculated_fine ?? 0,
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
