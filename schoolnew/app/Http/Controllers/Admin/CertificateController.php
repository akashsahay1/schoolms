<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Staff;
use App\Models\ExamMark;
use App\Models\Exam;
use App\Models\Setting;
use App\Services\StudentLifecycleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Download Transfer Certificate PDF.
     */
    public function transferCertificate(Student $student)
    {
        if (!in_array($student->status, ['transferred', 'expelled', 'inactive', 'graduated'])) {
            return back()->with('error', 'Transfer Certificate can only be generated for students who have left the school.');
        }

        $student->load(['schoolClass', 'section.classTeacher', 'academicYear', 'parent']);

        $school = $this->getSchoolData();
        $school = array_merge($school, $this->getClassTeacherData($student));

        // Dynamic TC number: TC/{year}/{student_id padded}
        $tcYear = $student->leaving_date ? $student->leaving_date->format('Y') : date('Y');
        $tcNumber = 'TC/' . $tcYear . '/' . str_pad($student->id, 4, '0', STR_PAD_LEFT);

        // Dynamic result text
        if ($student->status === 'graduated') {
            $resultText = 'Passed from ' . ($student->schoolClass->name ?? 'the school');
        } elseif ($student->status === 'transferred') {
            $resultText = 'Studied up to ' . ($student->schoolClass->name ?? 'the enrolled class');
        } else {
            $resultText = 'Was enrolled in ' . ($student->schoolClass->name ?? 'the school');
        }

        $pdf = Pdf::loadView('admin.certificates.transfer-certificate', compact('student', 'school', 'tcNumber', 'resultText'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'TC_' . $student->admission_no . '_' . date('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Download Marksheet PDF.
     */
    public function marksheet(Student $student, Request $request)
    {
        $student->load(['schoolClass', 'section.classTeacher', 'academicYear']);

        // Use session-aware result fetching
        $results = StudentLifecycleService::getSessionResults($student, $request->exam_id);

        if (!$results['has_data']) {
            return back()->with('error', $results['exam']
                ? 'No marks available for this student in ' . $results['exam']->name . '.'
                : 'No exam data found for this student in session ' . ($student->academicYear?->name ?? '') . '.'
            );
        }

        $exam = Exam::with(['examType', 'academicYear'])->find($results['exam']->id);
        $marks = $results['marks'];
        $totalMarks = $results['total_marks'];
        $totalFullMarks = $results['total_full'];
        $percentage = $results['percentage'];
        $grade = $results['grade'];
        $result = $results['result'];

        $school = $this->getSchoolData();
        $school = array_merge($school, $this->getClassTeacherData($student));

        $data = compact('student', 'exam', 'marks', 'totalMarks', 'totalFullMarks', 'percentage', 'grade', 'result', 'school');

        $pdf = Pdf::loadView('admin.certificates.marksheet', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Marksheet_' . $student->admission_no . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $exam->name) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Get school data from settings.
     */
    private function getSchoolData(): array
    {
        $signaturePath = Setting::get('principal_signature_image');
        $stampPath = Setting::get('school_stamp');
        $logoPath = Setting::get('school_logo');
        $examControllerSigPath = Setting::get('exam_controller_signature_image');

        return [
            'name' => Setting::get('school_name', config('app.name')),
            'address' => Setting::get('school_address', ''),
            'phone' => Setting::get('school_phone', ''),
            'email' => Setting::get('school_email', ''),
            'tagline' => Setting::get('school_tagline', ''),
            'logo' => $logoPath,
            'logo_url' => $logoPath ? public_path('storage/' . $logoPath) : null,
            'principal' => Setting::get('principal_name', ''),
            'signature_url' => $signaturePath ? public_path('storage/' . $signaturePath) : null,
            'stamp_url' => $stampPath ? public_path('storage/' . $stampPath) : null,
            'exam_controller' => Setting::get('exam_controller_name', ''),
            'exam_controller_signature_url' => $examControllerSigPath ? public_path('storage/' . $examControllerSigPath) : null,
        ];
    }

    /**
     * Get the class teacher data from the student's section assignment.
     */
    private function getClassTeacherData(Student $student): array
    {
        $classTeacher = $student->section?->classTeacher;

        if (!$classTeacher) {
            return [
                'class_teacher' => '',
                'class_teacher_signature_url' => null,
            ];
        }

        // Find the staff record linked to this user to get the signature
        $staff = Staff::where('user_id', $classTeacher->id)->first();

        return [
            'class_teacher' => $classTeacher->full_name,
            'class_teacher_signature_url' => $staff?->signature_url,
        ];
    }

}
