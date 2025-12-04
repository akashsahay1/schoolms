@extends('layouts.app')

@section('title', $class->name . ' - Class Details')

@section('page-title', 'Class Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
	<li class="breadcrumb-item active">{{ $class->name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-8">
		<!-- Class Information -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">{{ $class->name }}</h5>
					<div>
						<a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-primary btn-sm">
							<i data-feather="edit" class="me-1"></i> Edit
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Class Name</td>
								<td><strong>{{ $class->name }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Numeric Name</td>
								<td>{{ $class->numeric_name ?? '-' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Academic Year</td>
								<td>{{ $class->academicYear->name ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Pass Mark</td>
								<td>{{ $class->pass_mark }}%</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Display Order</td>
								<td>{{ $class->order }}</td>
							</tr>
							<tr>
								<td class="text-muted">Status</td>
								<td>
									<span class="badge bg-{{ $class->is_active ? 'success' : 'secondary' }}">
										{{ $class->is_active ? 'Active' : 'Inactive' }}
									</span>
								</td>
							</tr>
							<tr>
								<td class="text-muted">Created</td>
								<td>{{ $class->created_at->format('d M Y, h:i A') }}</td>
							</tr>
							<tr>
								<td class="text-muted">Last Updated</td>
								<td>{{ $class->updated_at->format('d M Y, h:i A') }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Sections -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Sections ({{ $class->sections->count() }})</h5>
					<a href="{{ route('admin.sections.index') }}?class_id={{ $class->id }}" class="btn btn-outline-primary btn-sm">
						Manage Sections
					</a>
				</div>
			</div>
			<div class="card-body">
				@if($class->sections->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Section Name</th>
									<th>Capacity</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($class->sections as $section)
									<tr>
										<td><strong>{{ $section->name }}</strong></td>
										<td>{{ $section->capacity ?? 'Not set' }}</td>
										<td>
											<span class="badge bg-{{ $section->is_active ? 'success' : 'secondary' }}">
												{{ $section->is_active ? 'Active' : 'Inactive' }}
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-4">
						<i data-feather="layers" style="width: 48px; height: 48px;" class="text-muted"></i>
						<p class="text-muted mt-2 mb-0">No sections found for this class.</p>
						<a href="{{ route('admin.sections.index') }}" class="btn btn-primary btn-sm mt-2">Add Sections</a>
					</div>
				@endif
			</div>
		</div>

		<!-- Recent Students -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Students ({{ $class->students->count() }})</h5>
					<a href="{{ route('admin.students.index') }}?class_id={{ $class->id }}" class="btn btn-outline-primary btn-sm">
						View All Students
					</a>
				</div>
			</div>
			<div class="card-body">
				@if($class->students->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Admission No</th>
									<th>Name</th>
									<th>Section</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($class->students->take(10) as $student)
									<tr>
										<td><strong>{{ $student->admission_no }}</strong></td>
										<td>{{ $student->full_name }}</td>
										<td>{{ $student->section->name ?? 'N/A' }}</td>
										<td>
											<span class="badge bg-{{ $student->status == 'active' ? 'success' : 'secondary' }}">
												{{ ucfirst($student->status) }}
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@if($class->students->count() > 10)
						<p class="text-muted text-center mt-2 mb-0">
							Showing 10 of {{ $class->students->count() }} students.
							<a href="{{ route('admin.students.index') }}?class_id={{ $class->id }}">View all</a>
						</p>
					@endif
				@else
					<div class="text-center py-4">
						<i data-feather="users" style="width: 48px; height: 48px;" class="text-muted"></i>
						<p class="text-muted mt-2 mb-0">No students enrolled in this class yet.</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-4">
		<!-- Quick Stats -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Quick Statistics</h6>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-6">
						<div class="bg-primary bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-primary mb-1">{{ $class->students->count() }}</h3>
							<small class="text-muted">Total Students</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-info bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-info mb-1">{{ $class->sections->count() }}</h3>
							<small class="text-muted">Sections</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-success bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-success mb-1">{{ $class->students->where('status', 'active')->count() }}</h3>
							<small class="text-muted">Active Students</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-warning bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-warning mb-1">{{ $class->subjects->count() }}</h3>
							<small class="text-muted">Subjects</small>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Actions</h6>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-outline-primary">
						<i data-feather="edit" class="me-1"></i> Edit Class
					</a>
					<a href="{{ route('admin.students.create') }}?class_id={{ $class->id }}" class="btn btn-outline-success">
						<i data-feather="user-plus" class="me-1"></i> Add Student
					</a>
					<a href="{{ route('admin.sections.index') }}?class_id={{ $class->id }}" class="btn btn-outline-info">
						<i data-feather="layers" class="me-1"></i> Manage Sections
					</a>
				</div>
			</div>
		</div>

		<!-- Back Button -->
		<a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary w-100">
			<i data-feather="arrow-left" class="me-1"></i> Back to Classes
		</a>
	</div>
</div>
@endsection
