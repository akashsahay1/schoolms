<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'Name',
            'Gender',
            'Date of Birth',
            'Class',
            'Section',
            'Email',
            'Phone',
            'Status',
            'Admission Date',
        ];
    }

    public function map($student): array
    {
        return [
            $student->admission_no,
            $student->full_name ?? $student->name,
            ucfirst($student->gender ?? ''),
            $student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : '',
            $student->schoolClass->name ?? 'N/A',
            $student->section->name ?? 'N/A',
            $student->email,
            $student->phone,
            ucfirst($student->status),
            $student->admission_date ? $student->admission_date->format('d-m-Y') : '',
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
