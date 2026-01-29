@extends('layouts.teacher-portal')

@section('title', 'Mark Attendance')
@section('page-title', 'Mark Attendance')

@section('breadcrumb')
<li class="breadcrumb-item active">Mark Attendance</li>
@endsection

@section('content')
<div class="row">
	<!-- Selection Card -->
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<form method="GET" class="row g-3 align-items-end">
					<div class="col-md-3">
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
					<div class="col-md-3">
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
						<label class="form-label">Date</label>
						<input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ now()->format('Y-m-d') }}">
					</div>
					<div class="col-md-3">
						<button type="submit" class="btn btn-primary w-100">
							<i data-feather="search" style="width: 14px; height: 14px;"></i> Load Students
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Attendance Form -->
	@if($students->count() > 0)
		<div class="col-12">
			<div class="card">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between align-items-center">
						<h5 class="mb-0">
							{{ $selectedClass->name ?? 'Class' }}
							@if($selectedSection)
								- Section {{ $selectedSection->name }}
							@endif
							| {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
						</h5>
						<div>
							<button type="button" class="btn btn-sm btn-success mark-all" data-status="present">Mark All Present</button>
							<button type="button" class="btn btn-sm btn-danger mark-all" data-status="absent">Mark All Absent</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form action="{{ route('teacher.attendance.store') }}" method="POST">
						@csrf
						<input type="hidden" name="class_id" value="{{ request('class_id') }}">
						<input type="hidden" name="section_id" value="{{ request('section_id') }}">
						<input type="hidden" name="date" value="{{ $date }}">

						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th style="width: 50px;">#</th>
										<th>Student</th>
										<th>Roll No.</th>
										<th style="width: 300px;">Attendance Status</th>
									</tr>
								</thead>
								<tbody>
									@foreach($students as $index => $student)
										@php
											$existingStatus = $existingAttendance->get($student->id)?->status ?? 'present';
										@endphp
										<tr>
											<td>{{ $index + 1 }}</td>
											<td>
												<div class="d-flex align-items-center">
													@if($student->photo)
														<img src="{{ asset('storage/' . $student->photo) }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
													@else
														<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
															{{ strtoupper(substr($student->first_name, 0, 1)) }}
														</div>
													@endif
													<strong>{{ $student->full_name }}</strong>
												</div>
											</td>
											<td>{{ $student->roll_number ?? '-' }}</td>
											<td>
												<div class="btn-group" role="group">
													<input type="radio" class="btn-check attendance-radio" name="attendance[{{ $student->id }}]" id="present_{{ $student->id }}" value="present" {{ $existingStatus == 'present' ? 'checked' : '' }}>
													<label class="btn btn-outline-success" for="present_{{ $student->id }}">Present</label>

													<input type="radio" class="btn-check attendance-radio" name="attendance[{{ $student->id }}]" id="absent_{{ $student->id }}" value="absent" {{ $existingStatus == 'absent' ? 'checked' : '' }}>
													<label class="btn btn-outline-danger" for="absent_{{ $student->id }}">Absent</label>

													<input type="radio" class="btn-check attendance-radio" name="attendance[{{ $student->id }}]" id="late_{{ $student->id }}" value="late" {{ $existingStatus == 'late' ? 'checked' : '' }}>
													<label class="btn btn-outline-warning" for="late_{{ $student->id }}">Late</label>

													<input type="radio" class="btn-check attendance-radio" name="attendance[{{ $student->id }}]" id="half_{{ $student->id }}" value="half_day" {{ $existingStatus == 'half_day' ? 'checked' : '' }}>
													<label class="btn btn-outline-info" for="half_{{ $student->id }}">Half Day</label>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						<div class="mt-4">
							<button type="submit" class="btn btn-primary">
								<i data-feather="save" style="width: 14px; height: 14px;"></i> Save Attendance
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@elseif(request('class_id'))
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<i data-feather="users" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
					<h5 class="text-muted">No Students Found</h5>
					<p class="text-muted mb-0">There are no students in the selected class/section.</p>
				</div>
			</div>
		</div>
	@endif
</div>

@push('scripts')
<script>
jQuery(document).ready(function() {
	// Load sections when class changes
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
		} else {
			jQuery('#section_id').html('<option value="">All Sections</option>');
		}
	});

	// Mark all buttons
	jQuery('.mark-all').on('click', function() {
		var status = jQuery(this).data('status');
		jQuery('input.attendance-radio[value="' + status + '"]').prop('checked', true);
	});
});
</script>
@endpush
@endsection
