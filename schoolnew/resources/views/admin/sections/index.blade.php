@extends('layouts.app')

@section('title', 'Sections Management')

@section('page-title', 'Sections Management')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item active">Sections</li>
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
					<h5>All Sections</h5>
					<a href="{{ route('admin.sections.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Add New Section
					</a>
				</div>
			</div>
			<div class="card-body">
				<!-- Filters -->
				<form method="GET" action="{{ route('admin.sections.index') }}" class="mb-4">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Search section name, room..." value="{{ request('search') }}">
						</div>
						<div class="col-md-3">
							<select name="class_id" class="form-select">
								<option value="">All Classes</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
										{{ $class->name }}
									</option>
								@endforeach
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
						@if(request()->hasAny(['search', 'class_id', 'status']))
							<div class="col-md-1">
								<a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary w-100" title="Clear Filters">
									<i data-feather="x"></i>
								</a>
							</div>
						@endif
					</div>
				</form>

				<!-- Sections Table -->
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Section Name</th>
								<th>Class</th>
								<th>Room No</th>
								<th>Capacity</th>
								<th>Students</th>
								<th>Class Teacher</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($sections as $section)
								<tr>
									<td>{{ $sections->firstItem() + $loop->index }}</td>
									<td><strong>{{ $section->name }}</strong></td>
									<td>{{ $section->schoolClass->name ?? 'N/A' }}</td>
									<td>{{ $section->room_no ?? '-' }}</td>
									<td>{{ $section->capacity ?? 'No limit' }}</td>
									<td>
										<span class="badge bg-primary">{{ $section->students->count() }} students</span>
									</td>
									<td>{{ $section->classTeacher->name ?? 'Not assigned' }}</td>
									<td>
										<span class="badge bg-{{ $section->is_active ? 'success' : 'secondary' }}">
											{{ $section->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.sections.show', $section) }}" title="View">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
											<a class="square-white" href="{{ route('admin.sections.edit', $section) }}" title="Edit">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
											</a>
											<form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-inline delete-form">
												@csrf
												@method('DELETE')
												<button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $section->name }}">
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
											<i data-feather="layers" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No sections found.</p>
											<a href="{{ route('admin.sections.create') }}" class="btn btn-primary mt-3">Add First Section</a>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-center mt-4">
					{{ $sections->withQueryString()->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
