@extends('layouts.app')

@section('title', 'Subjects Management')

@section('page-title', 'Subjects Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Subjects</li>
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
					<h5>All Subjects</h5>
					<a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add New Subject
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.subjects.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Search name or code..." value="{{ request('search') }}">
						</div>
						<div class="col-md-2">
							<select name="type" class="form-select">
								<option value="">All Types</option>
								<option value="theory" {{ request('type') == 'theory' ? 'selected' : '' }}>Theory</option>
								<option value="practical" {{ request('type') == 'practical' ? 'selected' : '' }}>Practical</option>
								<option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Both</option>
							</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">All Status</option>
								<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-outline-primary w-100">
								<i data-feather="search" class="me-1"></i> Filter
							</button>
						</div>
						@if(request()->hasAny(['search', 'type', 'status']))
							<div class="col-md-1">
								<a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Subjects Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Code</th>
								<th>Subject Name</th>
								<th>Type</th>
								<th>Full Marks</th>
								<th>Pass Marks</th>
								<th>Classes</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($subjects as $subject)
								<tr>
									<td>{{ $subjects->firstItem() + $loop->index }}</td>
									<td><strong>{{ $subject->code }}</strong></td>
									<td>
										{{ $subject->name }}
										@if($subject->is_optional)
											<span class="badge bg-warning ms-1">Optional</span>
										@endif
									</td>
									<td>
										<span class="badge bg-{{ $subject->type == 'theory' ? 'primary' : ($subject->type == 'practical' ? 'info' : 'success') }}">
											{{ ucfirst($subject->type) }}
										</span>
									</td>
									<td>{{ $subject->full_marks }}</td>
									<td>{{ $subject->pass_marks }}</td>
									<td>
										<span class="badge bg-secondary">{{ $subject->classes->count() }} classes</span>
									</td>
									<td>
										<span class="badge bg-{{ $subject->is_active ? 'success' : 'secondary' }}">
											{{ $subject->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.subjects.show', $subject) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.subjects.edit', $subject) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $subject->name }}">
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
											<i data-feather="book" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No subjects found.</p>
											<a href="{{ route('admin.subjects.create') }}" class="btn btn-primary mt-3">Add First Subject</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $subjects->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
