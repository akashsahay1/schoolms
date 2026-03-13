@extends('layouts.app')

@section('title', 'Exam Reports')

@section('page-title', 'Exam Reports')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Exam Reports</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Exam Results Report</h5>
					@if($examResults->isNotEmpty())
						<a href="{{ route('admin.reports.exams.export', request()->all()) }}" class="btn btn-success">
							<i data-feather="download" class="me-1"></i> Export CSV
						</a>
					@endif
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.reports.exams') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<label class="form-label">Exam <span class="text-danger">*</span></label>
							<select name="exam_id" class="form-select" required>
								<option value="">Select Exam</option>
								@foreach($exams as $exam)
									<option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
										{{ $exam->name }} ({{ $exam->start_date?->format('M Y') }})
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<label class="form-label">Class <span class="text-danger">*</span></label>
							<select name="class_id" class="form-select" required>
								<option value="">Select Class</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
										{{ $class->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<label class="form-label">Section</label>
							<select name="section_id" class="form-select">
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
							<button type="submit" class="btn btn-primary w-100">
								<i data-feather="bar-chart-2" class="me-1"></i> Generate Report
							</button>
						</div>
					</div>
				</form>

				@if($examResults->isNotEmpty())
					<!-- Statistics Cards -->
					<div class="row mb-4">
						<div class="col-md-2">
							<div class="card bg-light-primary border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stats['total_students'] }}</h4>
									<p class="mb-0 small text-muted">Total Students</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-light-success border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stats['highest'] }}%</h4>
									<p class="mb-0 small text-muted">Highest</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-light-danger border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stats['lowest'] }}%</h4>
									<p class="mb-0 small text-muted">Lowest</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-light-info border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stats['average'] }}%</h4>
									<p class="mb-0 small text-muted">Average</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-success border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1 text-white">{{ $stats['pass_count'] }}</h4>
									<p class="mb-0 small text-white">Passed</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-danger border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1 text-white">{{ $stats['fail_count'] }}</h4>
									<p class="mb-0 small text-white">Failed</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Grade Distribution -->
					@if(!empty($gradeDistribution))
					<div class="row mb-4">
						<div class="col-12">
							<div class="d-flex gap-2 flex-wrap">
								@foreach(['A+' => 'success', 'A' => 'success', 'B+' => 'info', 'B' => 'info', 'C+' => 'warning', 'C' => 'warning', 'D' => 'secondary', 'F' => 'danger'] as $grade => $color)
									<span class="badge bg-{{ $color }} fs-6 px-3 py-2">
										Grade {{ $grade }}: {{ $gradeDistribution[$grade] ?? 0 }}
									</span>
								@endforeach
							</div>
						</div>
					</div>
					@endif

					<!-- Results Table -->
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th class="text-center">Rank</th>
									<th>Admission No</th>
									<th>Student Name</th>
									<th class="text-center">Marks Obtained</th>
									<th class="text-center">Max Marks</th>
									<th class="text-center">Percentage</th>
									<th class="text-center">Grade</th>
								</tr>
							</thead>
							<tbody>
								@foreach($examResults as $result)
									<tr>
										<td class="text-center">
											@if($result['rank'] <= 3)
												<span class="badge bg-{{ $result['rank'] == 1 ? 'warning' : ($result['rank'] == 2 ? 'secondary' : 'info') }} fs-6">
													{{ $result['rank'] }}
												</span>
											@else
												{{ $result['rank'] }}
											@endif
										</td>
										<td>{{ $result['student']->admission_no }}</td>
										<td>
											<div class="d-flex align-items-center">
												<img src="{{ $result['student']->photo_url }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
												<span>{{ $result['student']->full_name }}</span>
											</div>
										</td>
										<td class="text-center fw-semibold">{{ $result['total_marks'] }}</td>
										<td class="text-center text-muted">{{ $result['max_marks'] }}</td>
										<td class="text-center">
											<div class="d-flex align-items-center justify-content-center">
												<div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 80px;">
													<div class="progress-bar bg-{{ $result['percentage'] >= 75 ? 'success' : ($result['percentage'] >= 50 ? 'warning' : ($result['percentage'] >= 33 ? 'info' : 'danger')) }}" style="width: {{ $result['percentage'] }}%"></div>
												</div>
												<span class="fw-semibold">{{ $result['percentage'] }}%</span>
											</div>
										</td>
										<td class="text-center">
											<span class="badge bg-{{ in_array($result['grade'], ['A+', 'A']) ? 'success' : (in_array($result['grade'], ['B+', 'B']) ? 'info' : (in_array($result['grade'], ['C+', 'C']) ? 'warning' : ($result['grade'] == 'D' ? 'secondary' : 'danger'))) }} fs-6">
												{{ $result['grade'] }}
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-5">
						<div class="text-muted">
							<i data-feather="file-text" style="width: 64px; height: 64px;"></i>
							<h5 class="mt-3">Select Filters to Generate Report</h5>
							<p class="mb-0">Choose an exam and class to view results report.</p>
						</div>
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
	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
