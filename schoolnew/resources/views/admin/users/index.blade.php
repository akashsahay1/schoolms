@extends('layouts.app')

@section('title', 'Users Management')

@section('page-title', 'Users Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Users</li>
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
					<h5>All Users</h5>
					<a href="{{ route('admin.users.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add New User
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-4">
							<input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
						</div>
						<div class="col-md-3">
							<select name="role" class="form-select">
								<option value="">All Roles</option>
								@foreach($roles as $role)
									<option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
										{{ $role->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['search', 'role']))
							<div class="col-md-2">
								<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
									<i data-feather="x" class="me-1"></i> Clear
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Users Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Role</th>
								<th>Created</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($users as $user)
								<tr>
									<td>{{ $users->firstItem() + $loop->index }}</td>
									<td>
										{{ $user->name }}
									</td>
									<td>{{ $user->email }}</td>
									<td>
										@foreach($user->roles as $role)
											<span class="badge bg-{{ $role->name === 'Super Admin' ? 'danger' : ($role->name === 'Admin' ? 'primary' : 'secondary') }}">
												{{ $role->name }}
											</span>
										@endforeach
									</td>
									<td>{{ $user->created_at->format('M d, Y') }}</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.users.show', $user) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.users.edit', $user) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											@if($user->id !== auth()->id())
												<form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline delete-form">
													@csrf
													@method('DELETE')
													<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $user->name }}">
														<svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
													</button>
												</form>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="users" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No users found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $users->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
