@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('page-title', 'Attendance Reports')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Attendance Reports</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Attendance Report</h5>
					@if(!empty($attendanceData))
						<a href="{{ route('admin.reports.attendance.export', request()->all()) }}" class="btn btn-success">
							<i data-feather="download" class="me-1"></i> Export CSV
						</a>
					@endif
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.reports.attendance') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-2">
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
						<div class="col-md-2">
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
						<div class="col-md-2">
							<label class="form-label">Start Date <span class="text-danger">*</span></label>
							<input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
						</div>
						<div class="col-md-2">
							<label class="form-label">End Date <span class="text-danger">*</span></label>
							<input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
						</div>
						<div class="col-md-2">
							<label class="form-label">&nbsp;</label>
							<button type="submit" class="btn btn-primary w-100">
								<i data-feather="bar-chart-2" class="me-1"></i> Generate Report
							</button>
						</div>
					</div>
				</form>

				@if(!empty($stats) && !empty($attendanceData))
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
							<div class="card bg-light-info border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stats['total_days'] }}</h4>
									<p class="mb-0 small text-muted">Working Days</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-light-success border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1">{{ $stats['avg_attendance'] }}%</h4>
									<p class="mb-0 small text-muted">Avg Attendance</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-success border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1 text-white">{{ $stats['total_present'] }}</h4>
									<p class="mb-0 small text-white">Present</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-danger border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1 text-white">{{ $stats['total_absent'] }}</h4>
									<p class="mb-0 small text-white">Absent</p>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="card bg-warning border-0">
								<div class="card-body text-center py-3">
									<h4 class="mb-1 text-white">{{ $stats['total_late'] }}</h4>
									<p class="mb-0 small text-white">Late</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Attendance Table -->
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Admission No</th>
									<th>Student Name</th>
									<th class="text-center">Working Days</th>
									<th class="text-center">Present</th>
									<th class="text-center">Absent</th>
									<th class="text-center">Late</th>
									<th class="text-center">Attendance %</th>
								</tr>
							</thead>
							<tbody>
								@foreach($attendanceData as $index => $data)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>{{ $data['student']->admission_no }}</td>
										<td>
											<div class="d-flex align-items-center">
												<img src="{{ $data['student']->photo_url }}" alt="Photo" class="rounded-circle me-2" width="32" height="32">
												<span>{{ $data['student']->full_name }}</span>
											</div>
										</td>
										<td class="text-center">{{ $data['total_days'] }}</td>
										<td class="text-center">
											<span class="badge bg-success">{{ $data['present'] }}</span>
										</td>
										<td class="text-center">
											<span class="badge bg-danger">{{ $data['absent'] }}</span>
										</td>
										<td class="text-center">
											<span class="badge bg-warning">{{ $data['late'] }}</span>
										</td>
										<td class="text-center">
											<div class="d-flex align-items-center justify-content-center">
												<div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 100px;">
													<div class="progress-bar bg-{{ $data['percentage'] >= 75 ? 'success' : ($data['percentage'] >= 50 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $data['percentage'] }}%"></div>
												</div>
												<span class="{{ $data['percentage'] >= 75 ? 'text-success' : ($data['percentage'] >= 50 ? 'text-warning' : 'text-danger') }}">{{ $data['percentage'] }}%</span>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-5">
						<div class="text-muted">
							<i data-feather="calendar" style="width: 64px; height: 64px;"></i>
							<h5 class="mt-3">Select Filters to Generate Report</h5>
							<p class="mb-0">Choose a class and date range to view attendance report.</p>
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
