@extends('layouts.teacher-portal')

@section('title', 'My Homework')
@section('page-title', 'My Homework')

@section('breadcrumb')
<li class="breadcrumb-item active">My Homework</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">Assigned Homework</h5>
			<div>
				<a href="{{ route('teacher.homework.submissions') }}" class="btn btn-outline-primary me-2">
					<i data-feather="check-square" style="width: 14px; height: 14px;"></i> Review Submissions
				</a>
				<a href="{{ route('teacher.homework.create') }}" class="btn btn-primary">
					<i data-feather="plus" style="width: 14px; height: 14px;"></i> Assign New
				</a>
			</div>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if($homework->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Title</th>
									<th>Class</th>
									<th>Subject</th>
									<th>Assigned</th>
									<th>Due Date</th>
									<th>Submissions</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($homework as $hw)
									<tr>
										<td>
											<strong>{{ Str::limit($hw->title, 30) }}</strong>
										</td>
										<td>
											{{ $hw->schoolClass->name ?? 'N/A' }}
											@if($hw->section)
												- {{ $hw->section->name }}
											@endif
										</td>
										<td>{{ $hw->subject->name ?? 'N/A' }}</td>
										<td>{{ $hw->assigned_date ? $hw->assigned_date->format('M d') : $hw->created_at->format('M d') }}</td>
										<td>
											@if($hw->submission_date->isPast())
												<span class="text-danger">{{ $hw->submission_date->format('M d, Y') }}</span>
											@else
												{{ $hw->submission_date->format('M d, Y') }}
											@endif
										</td>
										<td>
											<span class="badge bg-info">{{ $hw->submissions_count ?? 0 }}</span>
										</td>
										<td>
											@if($hw->submission_date->isPast())
												<span class="badge bg-secondary">Closed</span>
											@else
												<span class="badge bg-success">Active</span>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="mt-4">
						{{ $homework->links() }}
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="book" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h5 class="text-muted">No Homework Assigned</h5>
						<p class="text-muted mb-3">You haven't assigned any homework yet.</p>
						<a href="{{ route('teacher.homework.create') }}" class="btn btn-primary">Assign Homework</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
