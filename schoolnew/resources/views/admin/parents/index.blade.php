@extends('layouts.app')

@section('title', 'Parents')

@section('page-title', 'Parents')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Parents</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<!-- Info Alert -->
		<div class="alert alert-info alert-dismissible fade show" role="alert">
			<i data-feather="info" class="me-2"></i>
			Parents are automatically created when adding students. To add or update parent information, please edit the respective student's record.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>

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
					<h5>All Parents/Guardians</h5>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.parents.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-4">
							<input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Search
							</button>
						</div>
						@if(request()->hasAny(['search']))
							<div class="col-md-1">
								<a href="{{ route('admin.parents.index') }}" class="btn btn-outline-secondary w-100" title="Clear Search">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Parents Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Father's Name</th>
								<th>Mother's Name</th>
								<th>Contact</th>
								<th>Email</th>
								<th>Children</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($parents as $parent)
								<tr>
									<td>{{ $parents->firstItem() + $loop->index }}</td>
									<td>{{ $parent->father_name ?? 'N/A' }}</td>
									<td>{{ $parent->mother_name ?? 'N/A' }}</td>
									<td>{{ $parent->primary_contact ?: 'N/A' }}</td>
									<td>{{ $parent->primary_email ?: 'N/A' }}</td>
									<td>
										@if($parent->students->count() > 0)
											<span class="badge bg-primary">{{ $parent->students->count() }} student(s)</span>
										@else
											<span class="text-muted">None</span>
										@endif
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.parents.show', $parent) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="users" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No parents found.</p>
											<p class="text-muted small">Parents are created automatically when you add students.</p>
											<a href="{{ route('admin.students.create') }}" class="btn btn-primary mt-3">Add a Student</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $parents->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
