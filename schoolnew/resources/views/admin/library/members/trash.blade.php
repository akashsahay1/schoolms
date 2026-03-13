@extends('layouts.app')

@section('title', 'Trashed Library Members')

@section('page-title', 'Trashed Library Members')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.library.members.index') }}">Library Members</a></li>
	<li class="breadcrumb-item active">Trash</li>
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

		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Trashed Members ({{ $trashedCount }})</h5>
					<a href="{{ route('admin.library.members.index') }}" class="btn btn-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to Members
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Search -->
				<form method="GET" action="{{ route('admin.library.members.trash') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-4">
							<input type="text" name="search" class="form-control" placeholder="Search by member ID..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Search
							</button>
						</div>
					</div>
				</form>

				<!-- Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Member ID</th>
								<th>Name</th>
								<th>Type</th>
								<th>Status</th>
								<th>Deleted At</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($members as $member)
								<tr>
									<td>{{ $members->firstItem() + $loop->index }}</td>
									<td><span class="fw-semibold">{{ $member->member_id }}</span></td>
									<td>{{ $member->member_name }}</td>
									<td>
										<span class="badge bg-light-{{ $member->member_type === 'Student' ? 'info' : 'primary' }}">
											{{ $member->member_type }}
										</span>
									</td>
									<td>
										@if($member->status === 'active')
											<span class="badge bg-success">Active</span>
										@elseif($member->status === 'expired')
											<span class="badge bg-warning">Expired</span>
										@else
											<span class="badge bg-danger">Suspended</span>
										@endif
									</td>
									<td>{{ $member->deleted_at->format('M d, Y H:i') }}</td>
									<td>
										<div class="d-flex gap-2">
											<form action="{{ route('admin.library.members.restore', $member->id) }}" method="POST" class="d-inline">
												@csrf
												<button type="submit" class="btn btn-sm btn-success" title="Restore">
													<i data-feather="rotate-ccw"></i>
												</button>
											</form>
											<form action="{{ route('admin.library.members.force-delete', $member->id) }}" method="POST" class="d-inline delete-permanently-form">
												@csrf
												@method('DELETE')
												<button type="button" class="btn btn-sm btn-danger delete-permanently" title="Delete Permanently" data-name="{{ $member->member_id }}">
													<i data-feather="trash-2"></i>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="trash-2" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">Trash is empty.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $members->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery(document).on('click', '.delete-permanently', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var itemName = jQuery(this).data('name');

		Swal.fire({
			title: 'Delete Permanently?',
			html: `You are about to permanently delete <strong>${itemName}</strong>.<br><small class="text-danger">This action cannot be undone.</small>`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, delete permanently',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) {
				form.submit();
			}
		});
	});

	if (typeof feather !== 'undefined') {
		feather.replace();
	}
});
</script>
@endpush
