@extends('layouts.teacher-portal')

@section('title', 'My Leave Applications')
@section('page-title', 'My Leave Applications')

@section('breadcrumb')
<li class="breadcrumb-item active">My Leave Applications</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">Leave Applications</h5>
			<a href="{{ route('teacher.leaves.create') }}" class="btn btn-primary">
				<i data-feather="plus" style="width: 14px; height: 14px;"></i> Apply for Leave
			</a>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if($leaves->count() > 0)
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Leave Type</th>
									<th>From</th>
									<th>To</th>
									<th>Days</th>
									<th>Applied On</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($leaves as $leave)
									<tr>
										<td>
											<strong>{{ $leave->leaveType->name ?? 'N/A' }}</strong>
										</td>
										<td>{{ $leave->from_date->format('M d, Y') }}</td>
										<td>{{ $leave->to_date->format('M d, Y') }}</td>
										<td>{{ $leave->from_date->diffInDays($leave->to_date) + 1 }} days</td>
										<td>{{ $leave->applied_at ? $leave->applied_at->format('M d, Y') : $leave->created_at->format('M d, Y') }}</td>
										<td>
											@switch($leave->status)
												@case('pending')
													<span class="badge bg-warning">Pending</span>
													@break
												@case('approved')
													<span class="badge bg-success">Approved</span>
													@break
												@case('rejected')
													<span class="badge bg-danger">Rejected</span>
													@break
												@case('cancelled')
													<span class="badge bg-secondary">Cancelled</span>
													@break
											@endswitch
										</td>
										<td>
											<a href="{{ route('teacher.leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">
												<i data-feather="eye" style="width: 14px; height: 14px;"></i>
											</a>
											@if($leave->status == 'pending')
												<form action="{{ route('teacher.leaves.cancel', $leave) }}" method="POST" class="d-inline">
													@csrf
													<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this leave application?')">
														<i data-feather="x" style="width: 14px; height: 14px;"></i>
													</button>
												</form>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="mt-4">
						{{ $leaves->links() }}
					</div>
				@else
					<div class="text-center py-5">
						<i data-feather="file-text" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
						<h5 class="text-muted">No Leave Applications</h5>
						<p class="text-muted mb-3">You haven't submitted any leave applications yet.</p>
						<a href="{{ route('teacher.leaves.create') }}" class="btn btn-primary">Apply for Leave</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
