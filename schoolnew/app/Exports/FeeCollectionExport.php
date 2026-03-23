<?php

namespace App\Exports;

use App\Models\FeeCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FeeCollectionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $fromDate;
    protected $toDate;
    protected $classId;
    protected $feeTypeId;
    protected $studentId;

    public function __construct($fromDate, $toDate, $classId = null, $feeTypeId = null, $studentId = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->classId = $classId;
        $this->feeTypeId = $feeTypeId;
        $this->studentId = $studentId;
    }

    public function collection()
    {
        $query = FeeCollection::with(['student.schoolClass', 'feeStructure.feeType'])
            ->whereBetween('payment_date', [$this->fromDate, $this->toDate]);

        if ($this->classId) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
        }

        if ($this->feeTypeId) {
            $query->whereHas('feeStructure', fn($q) => $q->where('fee_type_id', $this->feeTypeId));
        }

        if ($this->studentId) {
            $query->where('student_id', $this->studentId);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Receipt No',
            'Date',
            'Student Name',
            'Class',
            'Fee Type',
            'Amount',
            'Status',
        ];
    }

    public function map($collection): array
    {
        return [
            $collection->receipt_no,
            $collection->payment_date->format('d-m-Y'),
            $collection->student->full_name ?? 'N/A',
            $collection->student->schoolClass->name ?? 'N/A',
            $collection->feeStructure->feeType->name ?? 'N/A',
            number_format($collection->paid_amount, 2),
            'Paid',
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
