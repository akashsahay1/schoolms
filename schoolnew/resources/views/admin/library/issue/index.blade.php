@extends('layouts.app')

@section('title', 'Book Issue')

@section('page-title', 'Library - Book Issue')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item">Library</li>
	<li class="breadcrumb-item active">Book Issue</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Book Issue Records</h5>
					<a href="{{ route('admin.library.issue.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Issue Book
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Book</th>
								<th>Student</th>
								<th>Issue Date</th>
								<th>Due Date</th>
								<th>Return Date</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($issues as $issue)
								<tr>
									<td>{{ $issues->firstItem() + $loop->index }}</td>
									<td><strong>{{ $issue->book->title }}</strong></td>
									<td>{{ $issue->student->full_name }}</td>
									<td>{{ $issue->issue_date->format('d M Y') }}</td>
									<td>{{ $issue->due_date->format('d M Y') }}</td>
									<td>{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
									<td>
										<span class="badge badge-light-{{ $issue->status === 'issued' ? 'warning' : 'success' }}">
											{{ ucfirst($issue->status) }}
										</span>
									</td>
									<td>
										@if($issue->status === 'issued')
											<button class="btn btn-sm btn-success" onclick="alert('Return functionality')">Return</button>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<p class="text-muted">No issue records found.</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($issues->hasPages())
					<div class="mt-3">{{ $issues->links() }}</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
