@extends('layouts.portal')

@section('title', 'Report Card')
@section('page-title', 'My Report Card')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.exams') }}">Exams</a></li>
    <li class="breadcrumb-item active">Report Card</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Exam Selection -->
        <div class="col-12 d-print-none">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Select Exam</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('portal.exams.report-card') }}" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Select Exam</label>
                            <select name="exam_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Exam --</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ $exam->examType->name ?? '' }}) - {{ $exam->start_date->format('M Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">View Report Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($selectedExam && $reportCardData)
            <!-- Report Card -->
            <div class="col-12">
                <div class="card" id="report-card">
                    <div class="card-header pb-0 d-print-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Report Card</h5>
                            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                <i class="fa fa-print me-1"></i> Print Report Card
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- School Header -->
                        <div class="text-center mb-4 report-header">
                            <h3 class="mb-1 text-primary">{{ config('app.name', 'School Management System') }}</h3>
                            <p class="text-muted mb-0">Excellence in Education</p>
                            <hr>
                            <h4 class="mb-0">{{ $selectedExam->name }}</h4>
                            <small class="text-muted">{{ $selectedExam->examType->name ?? '' }} - {{ $selectedExam->academicYear->name ?? '' }}</small>
                        </div>

                        <!-- Student Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Student Name:</strong></td>
                                        <td>{{ $reportCardData['student']->first_name }} {{ $reportCardData['student']->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Admission No:</strong></td>
                                        <td>{{ $reportCardData['student']->admission_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Roll No:</strong></td>
                                        <td>{{ $reportCardData['student']->roll_no ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Class & Section:</strong></td>
                                        <td>{{ $reportCardData['student']->schoolClass->name ?? '-' }} - {{ $reportCardData['student']->section->name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Father's Name:</strong></td>
                                        <td>{{ $reportCardData['student']->parent->father_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Mother's Name:</strong></td>
                                        <td>{{ $reportCardData['student']->parent->mother_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date of Birth:</strong></td>
                                        <td>{{ $reportCardData['student']->date_of_birth ? $reportCardData['student']->date_of_birth->format('d M, Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Exam Period:</strong></td>
                                        <td>{{ $selectedExam->start_date->format('d M') }} - {{ $selectedExam->end_date->format('d M, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Marks Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 30%;">Subject</th>
                                        <th style="width: 12%;">Full Marks</th>
                                        <th style="width: 12%;">Marks Obtained</th>
                                        <th style="width: 12%;">Percentage</th>
                                        <th style="width: 10%;">Grade</th>
                                        <th style="width: 19%;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportCardData['subjects'] as $index => $subject)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $subject['subject']->name }}</strong></td>
                                            <td class="text-center">{{ number_format($subject['full_marks'], 0) }}</td>
                                            <td class="text-center {{ $subject['percentage'] < 33 ? 'text-danger' : '' }}">{{ number_format($subject['marks_obtained'], 0) }}</td>
                                            <td class="text-center">{{ $subject['percentage'] }}%</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $subject['grade'] == 'F' ? 'danger' : ($subject['grade'] == 'A+' || $subject['grade'] == 'A' ? 'success' : 'primary') }}">
                                                    {{ $subject['grade'] }}
                                                </span>
                                            </td>
                                            <td>{{ $subject['remarks'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Grand Total:</th>
                                        <th class="text-center">{{ number_format($reportCardData['total_full_marks'], 0) }}</th>
                                        <th class="text-center">{{ number_format($reportCardData['total_marks'], 0) }}</th>
                                        <th class="text-center"><strong>{{ $reportCardData['percentage'] }}%</strong></th>
                                        <th class="text-center">
                                            <span class="badge bg-{{ $reportCardData['grade'] == 'F' ? 'danger' : ($reportCardData['grade'] == 'A+' || $reportCardData['grade'] == 'A' ? 'success' : 'primary') }} fs-6">
                                                {{ $reportCardData['grade'] }}
                                            </span>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Result Summary -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card border {{ $reportCardData['result'] == 'Pass' ? 'border-success' : 'border-danger' }}">
                                    <div class="card-body p-3">
                                        <h5 class="mb-2">Performance Summary</h5>
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td><strong>Total Marks:</strong></td>
                                                <td>{{ number_format($reportCardData['total_marks'], 0) }} / {{ number_format($reportCardData['total_full_marks'], 0) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Percentage:</strong></td>
                                                <td>{{ $reportCardData['percentage'] }}%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Grade:</strong></td>
                                                <td><span class="badge bg-{{ $reportCardData['grade'] == 'F' ? 'danger' : 'success' }} fs-6">{{ $reportCardData['grade'] }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Result:</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $reportCardData['result'] == 'Pass' ? 'success' : 'danger' }} fs-6">
                                                        {{ $reportCardData['result'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Class Rank:</strong></td>
                                                <td><strong>{{ $reportCardData['rank'] }}</strong> out of {{ $reportCardData['total_students'] }} students</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body p-3">
                                        <h5 class="mb-2">Grading Scale</h5>
                                        <table class="table table-sm table-bordered mb-0">
                                            <tr><td>A+ (90-100%)</td><td>Excellent</td></tr>
                                            <tr><td>A (80-89%)</td><td>Very Good</td></tr>
                                            <tr><td>B+ (70-79%)</td><td>Good</td></tr>
                                            <tr><td>B (60-69%)</td><td>Satisfactory</td></tr>
                                            <tr><td>C+ (50-59%)</td><td>Average</td></tr>
                                            <tr><td>C (40-49%)</td><td>Below Average</td></tr>
                                            <tr><td>D (33-39%)</td><td>Needs Improvement</td></tr>
                                            <tr><td>F (Below 33%)</td><td>Fail</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="row mt-5 pt-4 signatures">
                            <div class="col-4 text-center">
                                <div class="border-top pt-2">
                                    <strong>Class Teacher</strong>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="border-top pt-2">
                                    <strong>Principal</strong>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="border-top pt-2">
                                    <strong>Parent/Guardian</strong>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4 text-muted">
                            <small>Generated on: {{ now()->format('d M, Y h:i A') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request('exam_id'))
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#file-text') }}"></use>
                        </svg>
                        <h6 class="mt-3 text-muted">Report Card Not Available</h6>
                        <p class="text-muted">Your report card for this exam has not been generated yet.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    @media print {
        .sidebar-wrapper, .page-header, .breadcrumb, .btn, .d-print-none { display: none !important; }
        .page-body { margin: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .container-fluid { padding: 0 !important; }
        body { background: white !important; }
        #report-card { margin: 0 !important; }
        .report-header { margin-top: 0 !important; }
        .signatures { margin-top: 80px !important; }
    }
</style>
@endpush
@endsection
