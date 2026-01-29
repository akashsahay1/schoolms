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
			<h5 class="mb-0">My Exam Schedule</h5>
			<a href="{{ route('teacher.exams.marks') }}" class="btn btn-primary">
				<i data-feather="edit" style="width: 14px; height: 14px;"></i> Enter Marks
			</a>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if($exams->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Exam</th>
									<th>Class</th>
									<th>Subject</th>
									<th>Date</th>
									<th>Time</th>
									<th>Total Marks</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($exams as $exam)
									<tr>
										<td><strong>{{ $exam->name }}</strong></td>
										<td>{{ $exam->schoolClass->name ?? 'N/A' }}</td>
										<td>{{ $exam->subject->name ?? 'N/A' }}</td>
										<td>{{ $exam->exam_date->format('M d, Y') }}</td>
										<td>
											@if($exam->start_time)
												{{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }}
												@if($exam->end_time)
													- {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}
												@endif
											@else
												-
											@endif
										</td>
										<td>{{ $exam->total_marks }}</td>
										<td>
											@if($exam->exam_date->isFuture())
												<span class="badge bg-info">Upcoming</span>
											@elseif($exam->exam_date->isToday())
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
