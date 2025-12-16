@extends('layouts.app')

@section('title', 'Report Cards')

@section('page-title', 'Report Cards')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Exams</a></li>
	<li class="breadcrumb-item active">Report Cards</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5>Report Card Generation</h5>
			</div>
			<div class="card-body">
				<!-- Filter Form -->
				<form method="GET" action="{{ route('admin.exams.report-cards') }}" class="row g-3 mb-4" id="report-form">
					<div class="col-md-3">
						<label class="form-label">Select Exam <span class="text-danger">*</span></label>
						<select name="exam_id" class="form-select" id="exam-select" required>
							<option value="">Choose Exam</option>
							@foreach($exams as $exam)
								<option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
									{{ $exam->name }} ({{ $exam->examType->name }})
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Select Class <span class="text-danger">*</span></label>
						<select name="class_id" class="form-select" id="class-select" required>
							<option value="">Choose Class</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
									{{ $class->name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Select Section <span class="text-danger">*</span></label>
						<select name="section_id" class="form-select" id="section-select" required>
							<option value="">Choose Section</option>
							@foreach($sections as $section)
								<option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
									{{ $section->name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Select Student <span class="text-danger">*</span></label>
						<select name="student_id" class="form-select" id="student-select" required>
							<option value="">Choose Student</option>
							@foreach($students as $student)
								<option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
									{{ $student->roll_no }} - {{ $student->full_name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">&nbsp;</label>
						<button type="submit" class="btn btn-primary d-block w-100">
							<i data-feather="file-text" class="me-1"></i> Generate
						</button>
					</div>
				</form>

				@if($reportCardData)
					<!-- Print Button -->
					<div class="d-flex justify-content-end mb-3 no-print">
						<button type="button" class="btn btn-outline-primary" onclick="window.print()">
							<i data-feather="printer" class="me-1"></i> Print Report Card
						</button>
					</div>

					<!-- Report Card -->
					<div class="report-card border rounded p-4" id="report-card">
						<!-- School Header -->
						<div class="text-center mb-4 border-bottom pb-3">
							<h2 class="mb-1">{{ config('app.name', 'School Management System') }}</h2>
							<p class="text-muted mb-1">Excellence in Education</p>
							<h4 class="text-primary">REPORT CARD</h4>
							<p class="mb-0">{{ $reportCardData['exam']->name }} - {{ $reportCardData['exam']->academicYear->name ?? '' }}</p>
						</div>

						<!-- Student Information -->
						<div class="row mb-4">
							<div class="col-md-6">
								<table class="table table-sm table-borderless mb-0">
									<tr>
										<td width="40%"><strong>Student Name:</strong></td>
										<td>{{ $reportCardData['student']->full_name }}</td>
									</tr>
									<tr>
										<td><strong>Admission No:</strong></td>
										<td>{{ $reportCardData['student']->admission_no }}</td>
									</tr>
									<tr>
										<td><strong>Class:</strong></td>
										<td>{{ $reportCardData['student']->schoolClass->name ?? 'N/A' }} - {{ $reportCardData['student']->section->name ?? 'N/A' }}</td>
									</tr>
									<tr>
										<td><strong>Roll No:</strong></td>
										<td>{{ $reportCardData['student']->roll_no }}</td>
									</tr>
								</table>
							</div>
							<div class="col-md-6">
								<table class="table table-sm table-borderless mb-0">
									<tr>
										<td width="40%"><strong>Father's Name:</strong></td>
										<td>{{ $reportCardData['student']->parent->father_name ?? 'N/A' }}</td>
									</tr>
									<tr>
										<td><strong>Mother's Name:</strong></td>
										<td>{{ $reportCardData['student']->parent->mother_name ?? 'N/A' }}</td>
									</tr>
									<tr>
										<td><strong>Date of Birth:</strong></td>
										<td>{{ $reportCardData['student']->date_of_birth?->format('d M, Y') ?? 'N/A' }}</td>
									</tr>
									<tr>
										<td><strong>Exam Type:</strong></td>
										<td>{{ $reportCardData['exam']->examType->name }}</td>
									</tr>
								</table>
							</div>
						</div>

						<!-- Marks Table -->
						<div class="table-responsive mb-4">
							<table class="table table-bordered">
								<thead class="table-light">
									<tr>
										<th>S.N.</th>
										<th>Subject</th>
										<th class="text-center">Full Marks</th>
										<th class="text-center">Marks Obtained</th>
										<th class="text-center">Percentage</th>
										<th class="text-center">Grade</th>
									</tr>
								</thead>
								<tbody>
									@forelse($reportCardData['subjects'] as $index => $subjectResult)
										<tr>
											<td>{{ $index + 1 }}</td>
											<td>{{ $subjectResult['subject']->name }}</td>
											<td class="text-center">{{ $subjectResult['full_marks'] }}</td>
											<td class="text-center">{{ $subjectResult['marks_obtained'] }}</td>
											<td class="text-center">{{ $subjectResult['percentage'] }}%</td>
											<td class="text-center">
												<span class="badge badge-light-{{ $subjectResult['grade'] == 'F' ? 'danger' : ($subjectResult['grade'][0] == 'A' ? 'success' : 'primary') }}">
													{{ $subjectResult['grade'] }}
												</span>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="6" class="text-center text-muted">No marks found for this student.</td>
										</tr>
									@endforelse
								</tbody>
								<tfoot class="table-light">
									<tr>
										<td colspan="2"><strong>Total</strong></td>
										<td class="text-center"><strong>{{ $reportCardData['total_full_marks'] }}</strong></td>
										<td class="text-center"><strong>{{ $reportCardData['total_marks'] }}</strong></td>
										<td class="text-center"><strong>{{ $reportCardData['percentage'] }}%</strong></td>
										<td class="text-center">
											<span class="badge badge-light-{{ $reportCardData['grade'] == 'F' ? 'danger' : ($reportCardData['grade'][0] == 'A' ? 'success' : 'primary') }} fs-6">
												{{ $reportCardData['grade'] }}
											</span>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>

						<!-- Result Summary -->
						<div class="row mb-4">
							<div class="col-md-6">
								<div class="card {{ $reportCardData['result'] == 'Pass' ? 'bg-success' : 'bg-danger' }} text-white">
									<div class="card-body text-center py-3">
										<h3 class="mb-0">{{ $reportCardData['result'] }}</h3>
										<small>Overall Result</small>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="card bg-primary text-white">
									<div class="card-body text-center py-3">
										<h3 class="mb-0">{{ $reportCardData['percentage'] }}%</h3>
										<small>Overall Percentage</small>
									</div>
								</div>
							</div>
						</div>

						<!-- Grade Scale -->
						<div class="mb-4">
							<h6>Grade Scale</h6>
							<div class="d-flex flex-wrap gap-2">
								<span class="badge bg-success">A+ (90-100%)</span>
								<span class="badge bg-success">A (80-89%)</span>
								<span class="badge bg-primary">B+ (70-79%)</span>
								<span class="badge bg-primary">B (60-69%)</span>
								<span class="badge bg-warning text-dark">C+ (50-59%)</span>
								<span class="badge bg-warning text-dark">C (40-49%)</span>
								<span class="badge bg-secondary">D (33-39%)</span>
								<span class="badge bg-danger">F (Below 33%)</span>
							</div>
						</div>

						<!-- Signatures -->
						<div class="row mt-5 pt-4 border-top">
							<div class="col-4 text-center">
								<div class="border-top border-dark pt-2 mx-4">
									<small>Class Teacher</small>
								</div>
							</div>
							<div class="col-4 text-center">
								<div class="border-top border-dark pt-2 mx-4">
									<small>Parent/Guardian</small>
								</div>
							</div>
							<div class="col-4 text-center">
								<div class="border-top border-dark pt-2 mx-4">
									<small>Principal</small>
								</div>
							</div>
						</div>

						<div class="text-center mt-4">
							<small class="text-muted">Generated on: {{ now()->format('d M, Y h:i A') }}</small>
						</div>
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="award" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
						<h5 class="text-muted">Generate Report Card</h5>
						<p class="text-muted">Select exam, class, section, and student to generate a detailed report card.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#class-select').on('change', function() {
		var classId = jQuery(this).val();
		var sectionSelect = jQuery('#section-select');
		var studentSelect = jQuery('#student-select');

		sectionSelect.html('<option value="">Choose Section</option>');
		studentSelect.html('<option value="">Choose Student</option>');

		if (classId) {
			jQuery.get('/admin/exams/sections/' + classId, function(sections) {
				sections.forEach(function(section) {
					sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
				});
			});
		}
	});

	jQuery('#section-select').on('change', function() {
		var classId = jQuery('#class-select').val();
		var sectionId = jQuery(this).val();
		var studentSelect = jQuery('#student-select');

		studentSelect.html('<option value="">Choose Student</option>');

		if (classId && sectionId) {
			jQuery.get('/admin/exams/students/' + classId + '/' + sectionId, function(students) {
				students.forEach(function(student) {
					studentSelect.append('<option value="' + student.id + '">' + student.roll_no + ' - ' + student.first_name + ' ' + (student.last_name || '') + '</option>');
				});
			});
		}
	});
});
</script>
@endpush

@push('styles')
<style>
@media print {
	.sidebar-wrapper, .page-header, .breadcrumb, .card-header, form, .no-print, .btn {
		display: none !important;
	}
	.card {
		border: none !important;
		box-shadow: none !important;
	}
	.report-card {
		border: 2px solid #000 !important;
	}
	body {
		padding: 0 !important;
		margin: 0 !important;
	}
	.page-body {
		padding: 0 !important;
		margin: 0 !important;
	}
}
</style>
@endpush
