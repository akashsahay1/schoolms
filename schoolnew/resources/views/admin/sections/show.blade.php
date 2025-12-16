@extends('layouts.app')

@section('title', $section->full_name . ' - Section Details')

@section('page-title', 'Section Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
	<li class="breadcrumb-item active">{{ $section->full_name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-8">
		<!-- Section Information -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">{{ $section->full_name }}</h5>
					<div>
						<a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-primary btn-sm">
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
								<td class="text-muted" style="width: 40%;">Section Name</td>
								<td><strong>{{ $section->name }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Class</td>
								<td>
									<a href="{{ route('admin.classes.show', $section->schoolClass) }}">
										{{ $section->schoolClass->name ?? 'N/A' }}
									</a>
								</td>
							</tr>
							<tr>
								<td class="text-muted">Room Number</td>
								<td>{{ $section->room_no ?? 'Not set' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Capacity</td>
								<td>{{ $section->capacity ?? 'Unlimited' }}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Class Teacher</td>
								<td>{{ $section->classTeacher->name ?? 'Not assigned' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Status</td>
								<td>
									<span class="badge badge-light-{{ $section->is_active ? 'success' : 'danger' }}">
										{{ $section->is_active ? 'Active' : 'Inactive' }}
									</span>
								</td>
							</tr>
							<tr>
								<td class="text-muted">Created</td>
								<td>{{ $section->created_at->format('d M Y, h:i A') }}</td>
							</tr>
							<tr>
								<td class="text-muted">Last Updated</td>
								<td>{{ $section->updated_at->format('d M Y, h:i A') }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Students -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Students ({{ $section->students->count() }})</h5>
					<a href="{{ route('admin.students.index') }}?section_id={{ $section->id }}" class="btn btn-outline-primary btn-sm">
						View All Students
					</a>
				</div>
			</div>
			<div class="card-body">
				@if($section->students->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Admission No</th>
									<th>Name</th>
									<th>Roll No</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($section->students->take(10) as $student)
									<tr>
										<td><strong>{{ $student->admission_no }}</strong></td>
										<td>{{ $student->full_name }}</td>
										<td>{{ $student->roll_no ?? '-' }}</td>
										<td>
											<span class="badge badge-light-{{ $student->status == 'active' ? 'success' : 'danger' }}">
												{{ ucfirst($student->status) }}
											</span>
										</td>
										<td>
											<a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info">
												<i data-feather="eye"></i>
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					@if($section->students->count() > 10)
						<p class="text-muted text-center mt-2 mb-0">
							Showing 10 of {{ $section->students->count() }} students.
							<a href="{{ route('admin.students.index') }}?section_id={{ $section->id }}">View all</a>
						</p>
					@endif
				@else
					<div class="text-center py-4">
						<i data-feather="users" style="width: 48px; height: 48px;" class="text-muted"></i>
						<p class="text-muted mt-2 mb-0">No students enrolled in this section yet.</p>
						<a href="{{ route('admin.students.create') }}?class_id={{ $section->class_id }}&section_id={{ $section->id }}" class="btn btn-primary btn-sm mt-2">Add Student</a>
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
							<h3 class="text-primary mb-1">{{ $section->students->count() }}</h3>
							<small class="text-muted">Total Students</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-success bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-success mb-1">{{ $section->students->where('status', 'active')->count() }}</h3>
							<small class="text-muted">Active Students</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-info bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-info mb-1">{{ $section->students->where('gender', 'male')->count() }}</h3>
							<small class="text-muted">Male</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-danger bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-danger mb-1">{{ $section->students->where('gender', 'female')->count() }}</h3>
							<small class="text-muted">Female</small>
						</div>
					</div>
				</div>

				@if($section->capacity)
					<div class="mt-4">
						<div class="d-flex justify-content-between mb-2">
							<span class="text-muted">Capacity Usage</span>
							<span class="text-muted">{{ $section->students->count() }}/{{ $section->capacity }}</span>
						</div>
						@php
							$percentage = min(100, ($section->students->count() / $section->capacity) * 100);
						@endphp
						<div class="progress" style="height: 10px;">
							<div class="progress-bar bg-{{ $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success') }}" style="width: {{ $percentage }}%"></div>
						</div>
					</div>
				@endif
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Actions</h6>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="{{ route('admin.sections.edit', $section) }}" class="btn btn-outline-primary">
						<i data-feather="edit" class="me-1"></i> Edit Section
					</a>
					<a href="{{ route('admin.students.create') }}?class_id={{ $section->class_id }}&section_id={{ $section->id }}" class="btn btn-outline-success">
						<i data-feather="user-plus" class="me-1"></i> Add Student
					</a>
					<a href="{{ route('admin.students.index') }}?section_id={{ $section->id }}" class="btn btn-outline-info">
						<i data-feather="users" class="me-1"></i> View All Students
					</a>
				</div>
			</div>
		</div>

		<!-- Back Button -->
		<a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary w-100">
			<i data-feather="arrow-left" class="me-1"></i> Back to Sections
		</a>
	</div>
</div>
@endsection
