@extends('layouts.app')

@section('title', 'Exam Results')

@section('page-title', 'Exam Results')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.exams.index') }}">Exams</a></li>
	<li class="breadcrumb-item active">Results</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5>Exam Results</h5>
			</div>
			<div class="card-body">
				<!-- Filter Form -->
				<form method="GET" action="{{ route('admin.exams.results') }}" class="row g-3 mb-4">
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
					<div class="col-md-3">
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
					<div class="col-md-3">
						<label class="form-label">Select Section</label>
						<select name="section_id" class="form-select" id="section-select">
							<option value="">All Sections</option>
							@foreach($sections as $section)
								<option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
									{{ $section->name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">&nbsp;</label>
						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-primary">
								<i data-feather="search" class="me-1"></i> View Results
							</button>
							@if($results->count() > 0)
								<button type="button" class="btn btn-outline-secondary" onclick="window.print()">
									<i data-feather="printer"></i>
								</button>
							@endif
						</div>
					</div>
				</form>

				@if($selectedExam && $selectedClass)
					<!-- Print Header - Compact with Logo -->
					<div class="print-header">
						<div class="logo-section">
							@if(\App\Models\Setting::get('school_logo'))
								<img src="{{ asset('storage/' . \App\Models\Setting::get('school_logo')) }}" alt="" class="school-logo">
							@else
								<img src="{{ asset('assets/images/logo/logo-1.png') }}" alt="" class="school-logo" onerror="this.style.display='none'">
							@endif
						</div>
						<div class="school-info">
							<h1 class="school-name">{{ \App\Models\Setting::get('school_name', config('app.name', 'School Management System')) }}</h1>
							<p class="school-address">{{ \App\Models\Setting::get('school_address', '123 Education Street, Knowledge City') }} | Phone: {{ \App\Models\Setting::get('school_phone', '+91 1234567890') }}</p>
							<div class="report-title">EXAMINATION RESULT SHEET</div>
						</div>
					</div>

					<!-- Print Exam Info - Single Line -->
					<div class="print-exam-info">
						<span><strong>Exam:</strong> {{ $selectedExam->name }} ({{ $selectedExam->examType->name }})</span>
						<span><strong>Class:</strong> {{ $selectedClass->name }}@if($selectedSection) - {{ $selectedSection->name }}@endif</span>
						<span><strong>Session:</strong> {{ $selectedExam->academicYear->name ?? 'N/A' }}</span>
					</div>

					<!-- Print Summary Stats - Compact -->
					<div class="print-summary">
						<span class="stat-item">Total: <span class="stat-value">{{ $results->count() }}</span></span>
						<span class="stat-item">Pass: <span class="stat-value">{{ $results->where('result', 'Pass')->count() }}</span></span>
						<span class="stat-item">Fail: <span class="stat-value">{{ $results->where('result', 'Fail')->count() }}</span></span>
						<span class="stat-item">Avg: <span class="stat-value">{{ $results->count() > 0 ? round($results->avg('percentage'), 1) : 0 }}%</span></span>
						<span class="stat-item">Pass%: <span class="stat-value">{{ $results->count() > 0 ? round(($results->where('result', 'Pass')->count() / $results->count()) * 100, 0) : 0 }}%</span></span>
					</div>

					<!-- Results Summary (Screen Only) -->
					<div class="row mb-4 no-print">
						<div class="col-md-3">
							<div class="card bg-primary text-white">
								<div class="card-body text-center py-3">
									<h4 class="mb-0">{{ $results->count() }}</h4>
									<small>Total Students</small>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card bg-success text-white">
								<div class="card-body text-center py-3">
									<h4 class="mb-0">{{ $results->where('result', 'Pass')->count() }}</h4>
									<small>Passed</small>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card bg-danger text-white">
								<div class="card-body text-center py-3">
									<h4 class="mb-0">{{ $results->where('result', 'Fail')->count() }}</h4>
									<small>Failed</small>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="card bg-info text-white">
								<div class="card-body text-center py-3">
									<h4 class="mb-0">{{ $results->count() > 0 ? round($results->avg('percentage'), 2) : 0 }}%</h4>
									<small>Class Average</small>
								</div>
							</div>
						</div>
					</div>

					<!-- Results Header -->
					<div class="d-flex justify-content-between align-items-center mb-3">
						<div>
							<h6 class="mb-0">{{ $selectedExam->name }} - {{ $selectedClass->name }}
								@if($selectedSection) ({{ $selectedSection->name }}) @endif
							</h6>
							<small class="text-muted">{{ $selectedExam->examType->name }} | {{ $selectedExam->academicYear->name ?? '' }}</small>
						</div>
					</div>

					@if($results->count() > 0)
						<!-- Results Table -->
						<div class="table-responsive">
							<table class="table table-bordered table-hover" id="results-table">
								<thead class="table-light">
									<tr>
										<th>Rank</th>
										<th>Roll No</th>
										<th>Student Name</th>
										@foreach($subjects as $subject)
											<th class="text-center">{{ $subject->code ?? substr($subject->name, 0, 3) }}</th>
										@endforeach
										<th class="text-center">Total</th>
										<th class="text-center">%</th>
										<th class="text-center">Grade</th>
										<th class="text-center">Result</th>
									</tr>
								</thead>
								<tbody>
									@foreach($results as $result)
										<tr>
											<td class="text-center">
												@if($result['rank'] == 1)
													<span class="badge bg-warning text-dark">1st</span>
												@elseif($result['rank'] == 2)
													<span class="badge bg-secondary">2nd</span>
												@elseif($result['rank'] == 3)
													<span class="badge bg-dark">3rd</span>
												@else
													{{ $result['rank'] }}
												@endif
											</td>
											<td>{{ $result['student']->roll_no }}</td>
											<td>{{ $result['student']->full_name }}</td>
											@foreach($subjects as $subject)
												<td class="text-center">
													@if(isset($result['marks'][$subject->id]))
														{{ $result['marks'][$subject->id]->marks_obtained }}
														<small class="text-muted">/{{ $result['marks'][$subject->id]->full_marks }}</small>
													@else
														<span class="text-muted">-</span>
													@endif
												</td>
											@endforeach
											<td class="text-center fw-bold">{{ $result['total_marks'] }}/{{ $result['total_full_marks'] }}</td>
											<td class="text-center fw-bold">{{ $result['percentage'] }}%</td>
											<td class="text-center">
												<span class="badge badge-light-{{ $result['grade'] == 'F' ? 'danger' : ($result['grade'][0] == 'A' ? 'success' : ($result['grade'][0] == 'B' ? 'primary' : 'warning')) }}">
													{{ $result['grade'] }}
												</span>
											</td>
											<td class="text-center">
												<span class="badge badge-light-{{ $result['result'] == 'Pass' ? 'success' : 'danger' }}">
													{{ $result['result'] }}
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						<!-- Print Footer - Compact -->
						<div class="print-footer">
							<!-- Grade Legend - Inline -->
							<div class="grade-legend">
								<span><strong>Grades:</strong></span>
								<span>A+ (90%+)</span>
								<span>A (80-89%)</span>
								<span>B+ (70-79%)</span>
								<span>B (60-69%)</span>
								<span>C+ (50-59%)</span>
								<span>C (40-49%)</span>
								<span>D (33-39%)</span>
								<span>F (&lt;33%)</span>
								<span style="margin-left: 10px;"><strong>Pass Mark: 33%</strong></span>
							</div>

							<!-- Signature Section -->
							<div class="signature-section">
								<div class="signature-box">
									<div class="signature-line">Class Teacher</div>
								</div>
								<div class="signature-box">
									<div class="signature-line">Exam Controller</div>
								</div>
								<div class="signature-box">
									<div class="signature-line">Principal</div>
								</div>
							</div>

							<!-- Print Date -->
							<div class="print-date">
								Generated: {{ now()->format('d/m/Y h:i A') }}
							</div>
						</div>
					@else
						<div class="text-center py-5">
							<i data-feather="alert-circle" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
							<h5 class="text-muted">No Results Found</h5>
							<p class="text-muted">No marks have been entered for this exam and class combination.</p>
							<a href="{{ route('admin.exams.marks') }}" class="btn btn-primary">
								<i data-feather="edit-3" class="me-1"></i> Enter Marks
							</a>
						</div>
					@endif
				@else
					<div class="text-center py-5">
						<i data-feather="trending-up" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
						<h5 class="text-muted">Select Exam and Class</h5>
						<p class="text-muted">Choose the exam and class to view detailed results and analysis.</p>
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

		sectionSelect.html('<option value="">All Sections</option>');

		if (classId) {
			jQuery.get('/admin/exams/sections/' + classId, function(sections) {
				sections.forEach(function(section) {
					sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
				});
			});
		}
	});
});
</script>
@endpush

@push('styles')
<style>
/* Print Elements - Hidden on screen */
.print-header, .print-footer, .print-exam-info, .print-summary {
	display: none;
}

@media print {
	/* Hide screen elements */
	.sidebar-wrapper, .page-header, .breadcrumb, .card-header, form, .btn,
	.no-print, .alert, nav, .page-body-wrapper > .page-header {
		display: none !important;
	}

	/* Reset page */
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	body {
		font-family: Arial, sans-serif;
		font-size: 9pt;
		background: white !important;
		line-height: 1.3;
	}

	.page-body, .page-body-wrapper, .page-wrapper, .container-fluid {
		padding: 0 !important;
		margin: 0 !important;
	}

	.card {
		border: none !important;
		box-shadow: none !important;
		margin: 0 !important;
	}

	.card-body {
		padding: 0 !important;
	}

	/* Print Header - Compact */
	.print-header {
		display: flex !important;
		align-items: center;
		border-bottom: 2px solid #000;
		padding-bottom: 8px;
		margin-bottom: 8px;
	}

	.print-header .logo-section {
		width: 60px;
		margin-right: 15px;
	}

	.print-header .school-logo {
		width: 55px;
		height: 55px;
	}

	.print-header .school-info {
		flex: 1;
		text-align: center;
	}

	.print-header .school-name {
		font-size: 16pt;
		font-weight: bold;
		margin: 0;
		text-transform: uppercase;
	}

	.print-header .school-address {
		font-size: 8pt;
		margin: 2px 0;
	}

	.print-header .report-title {
		font-size: 11pt;
		font-weight: bold;
		margin-top: 5px;
		text-decoration: underline;
	}

	/* Exam Info - Single Line */
	.print-exam-info {
		display: flex !important;
		justify-content: space-between;
		padding: 5px 0;
		border-bottom: 1px solid #ccc;
		margin-bottom: 5px;
		font-size: 8pt;
	}

	/* Summary Stats - Compact Inline */
	.print-summary {
		display: flex !important;
		justify-content: flex-end;
		gap: 15px;
		padding: 4px 0;
		font-size: 8pt;
		border-bottom: 1px solid #000;
		margin-bottom: 5px;
	}

	.print-summary .stat-item {
		display: inline;
	}

	.print-summary .stat-value {
		font-weight: bold;
	}

	/* Hide screen summary cards */
	.row.mb-4.no-print {
		display: none !important;
	}

	/* Hide Results Header on screen */
	.d-flex.justify-content-between.align-items-center.mb-3 {
		display: none !important;
	}

	/* Table Styling - Compact */
	.table-responsive {
		overflow: visible !important;
	}

	.table {
		font-size: 8pt;
		border-collapse: collapse;
		width: 100%;
	}

	.table th, .table td {
		border: 1px solid #000 !important;
		padding: 3px 2px !important;
		text-align: center;
		vertical-align: middle;
	}

	.table thead th {
		background: #d0d0d0 !important;
		font-weight: bold;
		font-size: 7pt;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}

	.table tbody tr:nth-child(even) {
		background: #f5f5f5 !important;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}

	/* Compact marks display */
	.table td small {
		display: none;
	}

	/* Badge styling for print */
	.badge {
		border: none;
		padding: 1px 3px;
		font-weight: bold;
		font-size: 7pt;
		background: transparent !important;
		color: #000 !important;
	}

	.badge.bg-warning {
		background: #ffc107 !important;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}

	/* Print Footer - Compact */
	.print-footer {
		display: block !important;
		margin-top: 10px;
		font-size: 8pt;
	}

	.signature-section {
		display: flex;
		justify-content: space-between;
		margin-top: 25px;
	}

	.signature-box {
		text-align: center;
		width: 150px;
	}

	.signature-line {
		border-top: 1px solid #000;
		margin-top: 30px;
		padding-top: 3px;
		font-size: 8pt;
	}

	.print-date {
		text-align: right;
		font-size: 7pt;
		margin-top: 10px;
		color: #666;
	}

	/* Grade Legend - Inline Compact */
	.grade-legend {
		display: flex;
		gap: 10px;
		font-size: 7pt;
		padding: 3px 0;
		border-top: 1px dashed #999;
		margin-top: 8px;
		flex-wrap: wrap;
	}

	.grade-legend span {
		white-space: nowrap;
	}

	/* Page setup - Landscape A4 */
	@page {
		size: A4 landscape;
		margin: 8mm;
	}
}
</style>
@endpush
