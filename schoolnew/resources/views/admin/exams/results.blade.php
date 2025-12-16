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
					<!-- Results Summary -->
					<div class="row mb-4">
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
@media print {
	.sidebar-wrapper, .page-header, .breadcrumb, .card-header, form, .btn {
		display: none !important;
	}
	.card {
		border: none !important;
		box-shadow: none !important;
	}
	.table {
		font-size: 10px;
	}
}
</style>
@endpush
