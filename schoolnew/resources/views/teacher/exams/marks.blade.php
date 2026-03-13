@extends('layouts.teacher-portal')

@section('title', 'Enter Marks')
@section('page-title', 'Enter Marks')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('teacher.exams.schedule') }}">Exams</a></li>
<li class="breadcrumb-item active">Enter Marks</li>
@endsection

@section('content')
<div class="row">
	<!-- Exam Selection -->
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<form method="GET" class="row g-3 align-items-end">
					<div class="col-md-9">
						<label class="form-label">Select Exam</label>
						<select name="schedule_id" id="schedule_selector" class="form-select" required>
							<option value="">Select an exam to enter marks</option>
							@foreach($schedules as $schedule)
								<option value="{{ $schedule->id }}" {{ request('schedule_id') == $schedule->id ? 'selected' : '' }}>
									{{ $schedule->exam->name ?? '' }} - {{ $schedule->schoolClass->name ?? '' }} - {{ $schedule->subject->name ?? '' }}
									({{ $schedule->exam_date->format('M d, Y') }})
								</option>
							@endforeach
						</select>
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

	<!-- Marks Entry Form -->
	@if($selectedSchedule && $students->count() > 0)
		<div class="col-12">
			<div class="card">
				<div class="card-header pb-0 border-0">
					<h5 class="mb-0">
						<i data-feather="edit-3" style="width: 18px; height: 18px;" class="me-2"></i>{{ $selectedSchedule->exam->name ?? '' }} - {{ $selectedSchedule->subject->name ?? '' }}
					</h5>
					<p class="text-muted mb-0">
						{{ $selectedSchedule->schoolClass->name ?? '' }}
						| Full Marks: {{ $selectedSchedule->full_marks }}
						| Pass Marks: {{ $selectedSchedule->pass_marks }}
					</p>
				</div>
				<div class="card-body">
					<form action="{{ route('teacher.exams.marks.store') }}" method="POST">
						@csrf
						<input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">

						<div class="table-responsive">
							<table class="table table-hover">
								<thead class="bg-light">
									<tr>
										<th style="width: 50px;">#</th>
										<th>Student</th>
										<th>Roll No.</th>
										<th style="width: 150px;">Marks (out of {{ $selectedSchedule->full_marks }})</th>
									</tr>
								</thead>
								<tbody>
									@foreach($students as $index => $student)
										@php
											$existingMark = $existingMarks->get($student->id);
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
											<td>{{ $student->roll_no ?? '-' }}</td>
											<td>
												<input type="number" name="marks[{{ $student->id }}]" class="form-control" value="{{ $existingMark ? $existingMark->marks_obtained : '' }}" min="0" max="{{ $selectedSchedule->full_marks }}" step="0.5" placeholder="Enter marks">
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						<div class="mt-4">
							<button type="submit" class="btn btn-primary">
								<i data-feather="save" style="width: 14px; height: 14px;"></i> Save Marks
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@elseif($selectedSchedule)
		<div class="col-12">
			<div class="card">
				<div class="card-body text-center py-5">
					<i data-feather="users" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
					<h5 class="text-muted">No Students Found</h5>
					<p class="text-muted mb-0">There are no students in the selected class.</p>
				</div>
			</div>
		</div>
	@endif
</div>

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('#schedule_selector').on('change', function() {
		jQuery(this).closest('form').submit();
	});
});
</script>
@endpush
@endsection
