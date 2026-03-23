<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\FeeCollection;
use App\Models\FeeStructure;
use App\Models\RouteAssignment;
use App\Models\Student;
use App\Models\TransportFee;
use App\Models\TransportFeeCollection;

class StudentFeeService
{
    /**
     * Get combined fee stats for a student (academic + transport).
     */
    public static function getStudentFeeStats(int $studentId, ?int $academicYearId = null): array
    {
        $student = Student::find($studentId);
        if (!$student) {
            return self::emptyStats();
        }

        $academicYearId = $academicYearId ?? AcademicYear::getActive()?->id;
        if (!$academicYearId) {
            return self::emptyStats();
        }

        // Academic fees (class-based)
        $academic = self::getAcademicFees($student, $academicYearId);

        // Transport fees (route-based)
        $transport = self::getTransportFees($student, $academicYearId);

        return [
            'academic_fees' => $academic['total'],
            'academic_paid' => $academic['paid'],
            'academic_discount' => $academic['discount'],
            'transport_fees' => $transport['total'],
            'transport_paid' => $transport['paid'],
            'total_fees' => $academic['total'] + $transport['total'],
            'total_paid' => $academic['paid'] + $transport['paid'],
            'total_discount' => $academic['discount'] + $transport['discount'],
            'total_due' => max(0, ($academic['total'] + $transport['total']) - ($academic['paid'] + $transport['paid']) - ($academic['discount'] + $transport['discount'])),
            'has_transport' => $transport['has_assignment'],
            'route_name' => $transport['route_name'],
        ];
    }

    /**
     * Get academic (class-based) fee breakdown.
     */
    private static function getAcademicFees(Student $student, int $academicYearId): array
    {
        $structures = FeeStructure::where('class_id', $student->class_id)
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->get();

        $structureIds = $structures->pluck('id')->toArray();

        $collections = FeeCollection::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('fee_structure_id', $structureIds)
            ->get();

        $total = $structures->sum('amount');
        $paid = 0;
        $discount = 0;

        foreach ($structures as $structure) {
            $payments = $collections->where('fee_structure_id', $structure->id);
            $paid += $payments->sum('paid_amount');
            $discount += $payments->sum('discount_amount');
        }

        return [
            'total' => $total,
            'paid' => $paid,
            'discount' => $discount,
        ];
    }

    /**
     * Get transport (route-based) fee breakdown.
     */
    private static function getTransportFees(Student $student, int $academicYearId): array
    {
        $assignment = RouteAssignment::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->with('route')
            ->first();

        if (!$assignment) {
            return [
                'total' => 0,
                'paid' => 0,
                'discount' => 0,
                'has_assignment' => false,
                'route_name' => null,
            ];
        }

        // Get transport fee for this route
        $transportFee = TransportFee::where('transport_route_id', $assignment->transport_route_id)
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->first();

        if (!$transportFee) {
            return [
                'total' => 0,
                'paid' => 0,
                'discount' => 0,
                'has_assignment' => true,
                'route_name' => $assignment->route->route_name ?? null,
            ];
        }

        // Get all generated collections for this student + fee
        $collections = TransportFeeCollection::where('student_id', $student->id)
            ->where('transport_fee_id', $transportFee->id)
            ->get();

        $total = $collections->sum(function ($c) {
            return $c->amount + $c->fine - $c->discount;
        });
        $paid = $collections->sum('paid_amount');
        $discount = $collections->sum('discount');

        return [
            'total' => $total,
            'paid' => $paid,
            'discount' => $discount,
            'has_assignment' => true,
            'route_name' => $assignment->route->route_name ?? null,
        ];
    }

    private static function emptyStats(): array
    {
        return [
            'academic_fees' => 0,
            'academic_paid' => 0,
            'academic_discount' => 0,
            'transport_fees' => 0,
            'transport_paid' => 0,
            'total_fees' => 0,
            'total_paid' => 0,
            'total_discount' => 0,
            'total_due' => 0,
            'has_transport' => false,
            'route_name' => null,
        ];
    }
}
