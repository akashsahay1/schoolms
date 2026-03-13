@extends('layouts.teacher-portal')

@section('title', 'Exam Schedule')
@section('page-title', 'Exam Schedule')

@section('breadcrumb')
<li class="breadcrumb-item active">Exam Schedule</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">
				<i data-feather="clipboard" style="width: 18px; height: 18px;" class="me-2"></i>My Exam Schedule
			</h5>
			<a href="{{ route('teacher.exams.marks') }}" class="btn btn-primary">
				<i data-feather="edit" style="width: 14px; height: 14px;"></i> Enter Marks
			</a>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if($schedules->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover">
							<thead class="bg-light">
								<tr>
									<th>Exam</th>
									<th>Class</th>
									<th>Subject</th>
									<th>Date</th>
									<th>Time</th>
									<th>Full Marks</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($schedules as $schedule)
									<tr>
										<td><strong>{{ $schedule->exam->name ?? 'N/A' }}</strong></td>
										<td>{{ $schedule->schoolClass->name ?? 'N/A' }}</td>
										<td>{{ $schedule->subject->name ?? 'N/A' }}</td>
										<td>{{ $schedule->exam_date->format('M d, Y') }}</td>
										<td>
											@if($schedule->start_time)
												{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
												@if($schedule->end_time)
													- {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
												@endif
											@else
												-
											@endif
										</td>
										<td>{{ $schedule->full_marks }}</td>
										<td>
											@if($schedule->exam_date->isFuture())
												<span class="badge bg-info">Upcoming</span>
											@elseif($schedule->exam_date->isToday())
												<span class="badge bg-warning">Today</span>
											@else
												<span class="badge bg-success">Completed</span>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="file-text" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h5 class="text-muted">No Exams Scheduled</h5>
						<p class="text-muted mb-0">There are no exams scheduled for your subjects.</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
