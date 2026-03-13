@extends('layouts.app')

@section('title', 'View Library Member')

@section('page-title', 'View Library Member')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.library.members.index') }}">Library Members</a></li>
	<li class="breadcrumb-item active">{{ $member->member_id }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="row">
			<!-- Member Details Card -->
			<div class="col-md-4">
				<div class="card">
					<div class="card-header">
						<div class="d-flex justify-content-between align-items-center">
							<h5>Member Details</h5>
							<div class="d-flex gap-2">
								<a href="{{ route('admin.library.members.card', $member) }}" class="btn btn-sm btn-outline-primary" target="_blank">
									<i data-feather="printer" class="me-1"></i> Print Card
								</a>
								<a href="{{ route('admin.library.members.edit', $member) }}" class="btn btn-sm btn-primary">
									<i data-feather="edit" class="me-1"></i> Edit
								</a>
							</div>
						</div>
					</div>
					<div class="card-body text-center">
						@if($member->memberable)
							<img src="{{ $member->memberable->photo_url ?? asset('assets/images/user/user.png') }}" alt="Photo" class="rounded-circle mb-3" width="100" height="100">
							<h5 class="mb-1">{{ $member->member_name }}</h5>
							<p class="text-muted mb-2">
								<span class="badge bg-light-{{ $member->member_type === 'Student' ? 'info' : 'primary' }}">{{ $member->member_type }}</span>
							</p>
							<p class="fw-semibold mb-3">{{ $member->member_id }}</p>
						@endif

						<div class="border-top pt-3">
							@if($member->status === 'active')
								<span class="badge bg-success fs-6">Active</span>
							@elseif($member->status === 'expired')
								<span class="badge bg-warning fs-6">Expired</span>
							@else
								<span class="badge bg-danger fs-6">Suspended</span>
							@endif
						</div>
					</div>
					<ul class="list-group list-group-flush">
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Membership Start</span>
							<span>{{ $member->membership_start->format('M d, Y') }}</span>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Membership End</span>
							<span class="{{ $member->is_expired ? 'text-danger' : '' }}">
								{{ $member->membership_end?->format('M d, Y') ?? 'Lifetime' }}
							</span>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Books Borrowed</span>
							<span>{{ $member->current_books_count }}/{{ $member->max_books_allowed }}</span>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Total Fines</span>
							<span>{{ number_format($member->total_fines, 2) }}</span>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Outstanding</span>
							<span class="{{ $member->outstanding_fines > 0 ? 'text-danger' : 'text-success' }}">
								{{ number_format($member->outstanding_fines, 2) }}
							</span>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Can Borrow</span>
							<span>
								@if($member->can_borrow)
									<i data-feather="check-circle" class="text-success"></i>
								@else
									<i data-feather="x-circle" class="text-danger"></i>
								@endif
							</span>
						</li>
					</ul>
				</div>

				<!-- Renew Membership -->
				@if($member->status !== 'active' || $member->is_expired)
					<div class="card">
						<div class="card-header">
							<h5>Renew Membership</h5>
						</div>
						<div class="card-body">
							<form action="{{ route('admin.library.members.renew', $member) }}" method="POST">
								@csrf
								<div class="mb-3">
									<label for="membership_end" class="form-label">New End Date <span class="text-danger">*</span></label>
									<input type="date" class="form-control" id="membership_end" name="membership_end" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
								</div>
								<button type="submit" class="btn btn-success w-100">
									<i data-feather="refresh-cw" class="me-1"></i> Renew Membership
								</button>
							</form>
						</div>
					</div>
				@endif
			</div>

			<!-- Book Issue History -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<h5>Book Issue History</h5>
					</div>
					<div class="card-body">
						@if($bookIssues->count() > 0)
							<div class="table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Book</th>
											<th>Issue Date</th>
											<th>Due Date</th>
											<th>Return Date</th>
											<th>Status</th>
											<th>Fine</th>
										</tr>
									</thead>
									<tbody>
										@foreach($bookIssues as $issue)
											<tr>
												<td>
													<div>
														<span class="fw-semibold">{{ $issue->book->title ?? 'N/A' }}</span>
														<br>
														<small class="text-muted">{{ $issue->book->author ?? '' }}</small>
													</div>
												</td>
												<td>{{ $issue->issue_date->format('M d, Y') }}</td>
												<td>{{ $issue->due_date->format('M d, Y') }}</td>
												<td>
													@if($issue->return_date)
														{{ $issue->return_date->format('M d, Y') }}
													@else
														<span class="text-muted">-</span>
													@endif
												</td>
												<td>
													@if($issue->status === 'issued')
														@if($issue->is_overdue)
															<span class="badge bg-danger">Overdue</span>
														@else
															<span class="badge bg-info">Issued</span>
														@endif
													@elseif($issue->status === 'returned')
														<span class="badge bg-success">Returned</span>
													@else
														<span class="badge bg-secondary">{{ ucfirst($issue->status) }}</span>
													@endif
												</td>
												<td>
													@if($issue->fine_amount > 0)
														<span class="text-danger">{{ number_format($issue->fine_amount, 2) }}</span>
													@else
														<span class="text-muted">-</span>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@else
							<div class="text-center py-4">
								<div class="text-muted">
									<i data-feather="book-open" style="width: 48px; height: 48px;"></i>
									<p class="mt-2 mb-0">No book issues found.</p>
								</div>
							</div>
						@endif
					</div>
				</div>

				<!-- Notes -->
				@if($member->notes)
					<div class="card">
						<div class="card-header">
							<h5>Notes</h5>
						</div>
						<div class="card-body">
							<p class="mb-0">{{ $member->notes }}</p>
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
