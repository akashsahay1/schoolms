@extends('layouts.app')

@section('title', 'Staff Management')

@section('page-title', 'Staff Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Staff</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<!-- Success/Error Messages -->
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
					<h5>All Staff Members</h5>
					<a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add New Staff
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.staff.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Search name, ID, email..." value="{{ request('search') }}">
						</div>
						<div class="col-md-3">
							<select name="department_id" class="form-select">
								<option value="">All Departments</option>
								@foreach($departments as $department)
									<option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
										{{ $department->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
								<option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
								<option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['search', 'department_id', 'status']))
							<div class="col-md-1">
								<a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Staff Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Photo</th>
								<th>Staff ID</th>
								<th>Name</th>
								<th>Department</th>
								<th>Designation</th>
								<th>Phone</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($staff as $member)
								<tr>
									<td>{{ $staff->firstItem() + $loop->index }}</td>
									<td>
										<div class="avatar avatar-sm">
											@if($member->photo)
												<img src="{{ $member->photo_url }}" alt="{{ $member->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
											@else
												<div class="rounded-circle bg-{{ $member->gender == 'male' ? 'primary' : 'danger' }} d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; font-size: 16px;">
													{{ strtoupper(substr($member->first_name, 0, 1)) }}
												</div>
											@endif
										</div>
									</td>
									<td><strong>{{ $member->staff_id }}</strong></td>
									<td>{{ $member->full_name }}</td>
									<td>{{ $member->department->name ?? 'N/A' }}</td>
									<td>{{ $member->designation->name ?? 'N/A' }}</td>
									<td>{{ $member->phone }}</td>
									<td>
										<span class="badge badge-light-{{ $member->status == 'active' ? 'success' : ($member->status == 'inactive' ? 'secondary' : ($member->status == 'resigned' ? 'warning' : 'danger')) }}">
											{{ ucfirst($member->status) }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.staff.show', $member) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.staff.edit', $member) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.staff.destroy', $member) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $member->full_name }}">
													<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="users" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No staff members found.</p>
											<a href="{{ route('admin.staff.create') }}" class="btn btn-primary mt-3">Add First Staff Member</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $staff->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
