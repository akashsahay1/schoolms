@extends('layouts.teacher-portal')

@section('title', 'Attendance Reports')
@section('page-title', 'Attendance Reports')

@section('breadcrumb')
<li class="breadcrumb-item active">Attendance Reports</li>
@endsection

@section('content')
<div class="row">
	<!-- Filters -->
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<form method="GET" class="row g-3 align-items-end">
					<div class="col-md-2">
						<label class="form-label">Class</label>
						<select name="class_id" id="class_id" class="form-select" required>
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
						<select name="section_id" id="section_id" class="form-select">
							<option value="">All Sections</option>
							@if($selectedClass)
								@foreach($selectedClass->sections as $section)
									<option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
										{{ $section->name }}
									</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Start Date</label>
						<input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
					</div>
					<div class="col-md-3">
						<label class="form-label">End Date</label>
						<input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-primary w-100">
							<i data-feather="filter" style="width: 14px; height: 14px;"></i> Generate Report
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Report -->
	@if($attendanceData->count() > 0)
		<div class="col-12">
			<div class="card">
				<div class="card-header pb-0">
					<h5 class="mb-0">
						Attendance Report: {{ $selectedClass->name ?? 'Class' }}
						@if($selectedSection)
							- Section {{ $selectedSection->name }}
						@endif
					</h5>
					<p class="text-muted mb-0">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr class="bg-light">
									<th>#</th>
									<th>Student Name</th>
									<th class="text-center bg-success text-white">Present</th>
									<th class="text-center bg-danger text-white">Absent</th>
									<th class="text-center bg-warning text-dark">Late</th>
									<th class="text-center bg-info text-white">Half Day</th>
									<th class="text-center">Total Days</th>
									<th class="text-center">Attendance %</th>
								</tr>
							</thead>
							<tbody>
								@foreach($attendanceData as $index => $data)
									@php
										$percentage = $data['total'] > 0 ? (($data['present'] + ($data['late'] * 0.5) + ($data['half_day'] * 0.5)) / $data['total']) * 100 : 0;
									@endphp
									<tr>
										<td>{{ $index + 1 }}</td>
										<td>
											<strong>{{ $data['student']->full_name ?? 'N/A' }}</strong>
											<br>
											<small class="text-muted">{{ $data['student']->admission_no ?? '' }}</small>
										</td>
										<td class="text-center">{{ $data['present'] }}</td>
										<td class="text-center">{{ $data['absent'] }}</td>
										<td class="text-center">{{ $data['late'] }}</td>
										<td class="text-center">{{ $data['half_day'] }}</td>
										<td class="text-center">{{ $data['total'] }}</td>
										<td class="text-center">
											<span class="badge {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}">
												{{ number_format($percentage, 1) }}%
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	@elseif(request('class_id'))
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<i data-feather="bar-chart-2" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
					<h5 class="text-muted">No Data Found</h5>
					<p class="text-muted mb-0">No attendance records found for the selected criteria.</p>
				</div>
			</div>
		</div>
	@endif
</div>

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#class_id').on('change', function() {
		var classId = jQuery(this).val();
		if (classId) {
			jQuery.get('{{ url("teacher/attendance/sections") }}/' + classId, function(data) {
				var options = '<option value="">All Sections</option>';
				jQuery.each(data, function(i, section) {
					options += '<option value="' + section.id + '">' + section.name + '</option>';
				});
				jQuery('#section_id').html(options);
			});
		}
	});
});
</script>
@endpush
@endsection
