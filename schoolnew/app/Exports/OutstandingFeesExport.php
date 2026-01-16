<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\FeeCollection;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OutstandingFeesExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $classId;
    protected $hidePaid;

    public function __construct($classId = null, $hidePaid = false)
    {
        $this->classId = $classId;
        $this->hidePaid = $hidePaid;
    }

    public function array(): array
    {
        $activeYear = AcademicYear::getActive();

        $query = Student::with(['schoolClass', 'section'])
            ->where('status', 'active');

        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }

        $students = $query->orderBy('first_name')->get();
        $data = [];

        foreach ($students as $student) {
            $feeStructures = FeeStructure::where('class_id', $student->class_id)
                ->where('is_active', true)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->get();

            $totalFee = $feeStructures->sum('amount');

            $paidAmount = FeeCollection::where('student_id', $student->id)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('paid_amount');

            $discountAmount = FeeCollection::where('student_id', $student->id)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->sum('discount_amount');

            $outstanding = max(0, $totalFee - $paidAmount - $discountAmount);

            if ($this->hidePaid && $outstanding <= 0) {
                continue;
            }

            $data[] = [
                $student->full_name,
                $student->admission_no,
                $student->schoolClass->name ?? 'N/A',
                $student->section->name ?? 'N/A',
                number_format($totalFee, 2),
                number_format($paidAmount, 2),
                number_format($discountAmount, 2),
                number_format($outstanding, 2),
                $totalFee > 0 ? round(($paidAmount / $totalFee) * 100, 1) . '%' : '0%',
            ];
        }

        // Sort by outstanding amount descending
        usort($data, fn($a, $b) => floatval(str_replace(',', '', $b[7])) <=> floatval(str_replace(',', '', $a[7])));

        return $data;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Admission No',
            'Class',
            'Section',
            'Total Fee',
            'Paid Amount',
            'Discount',
            'Outstanding',
            'Paid %',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C65911']
                ],
            ],
        ];
    }
}
