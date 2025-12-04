@extends('layouts.app')

@section('title', $subject->name . ' - Subject Details')

@section('page-title', 'Subject Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
	<li class="breadcrumb-item active">{{ $subject->name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-8">
		<!-- Subject Information -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">{{ $subject->name }} ({{ $subject->code }})</h5>
					<div>
						<a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-primary btn-sm">
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
								<td class="text-muted" style="width: 40%;">Subject Name</td>
								<td><strong>{{ $subject->name }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Subject Code</td>
								<td><span class="badge bg-secondary">{{ $subject->code }}</span></td>
							</tr>
							<tr>
								<td class="text-muted">Type</td>
								<td>
									<span class="badge bg-{{ $subject->type == 'theory' ? 'primary' : ($subject->type == 'practical' ? 'info' : 'success') }}">
										{{ ucfirst($subject->type) }}
									</span>
								</td>
							</tr>
							<tr>
								<td class="text-muted">Optional</td>
								<td>
									@if($subject->is_optional)
										<span class="badge bg-warning">Yes</span>
									@else
										<span class="badge bg-secondary">No</span>
									@endif
								</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Full Marks</td>
								<td><strong>{{ $subject->full_marks }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Pass Marks</td>
								<td>{{ $subject->pass_marks }}</td>
							</tr>
							<tr>
								<td class="text-muted">Status</td>
								<td>
									<span class="badge bg-{{ $subject->is_active ? 'success' : 'secondary' }}">
										{{ $subject->is_active ? 'Active' : 'Inactive' }}
									</span>
								</td>
							</tr>
							<tr>
								<td class="text-muted">Created</td>
								<td>{{ $subject->created_at->format('d M Y, h:i A') }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Assigned Classes -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Assigned Classes ({{ $subject->classes->count() }})</h5>
					<a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-outline-primary btn-sm">
						Manage Classes
					</a>
				</div>
			</div>
			<div class="card-body">
				@if($subject->classes->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Class Name</th>
									<th>Teacher</th>
									<th>Credit Hours</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($subject->classes as $class)
									<tr>
										<td>
											<a href="{{ route('admin.classes.show', $class) }}">
												<strong>{{ $class->name }}</strong>
											</a>
										</td>
										<td>{{ $class->pivot->teacher_id ? 'Assigned' : 'Not assigned' }}</td>
										<td>{{ $class->pivot->credit_hours ?? '-' }}</td>
										<td>
											<a href="{{ route('admin.classes.show', $class) }}" class="btn btn-sm btn-outline-info">
												<i data-feather="eye"></i>
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-4">
						<i data-feather="book-open" style="width: 48px; height: 48px;" class="text-muted"></i>
						<p class="text-muted mt-2 mb-0">This subject is not assigned to any class yet.</p>
						<a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-primary btn-sm mt-2">Assign to Classes</a>
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
							<h3 class="text-primary mb-1">{{ $subject->classes->count() }}</h3>
							<small class="text-muted">Classes</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-success bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-success mb-1">{{ $subject->full_marks }}</h3>
							<small class="text-muted">Full Marks</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-warning bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-warning mb-1">{{ $subject->pass_marks }}</h3>
							<small class="text-muted">Pass Marks</small>
						</div>
					</div>
					<div class="col-6">
						<div class="bg-info bg-opacity-10 rounded p-3 text-center">
							<h3 class="text-info mb-1">{{ round(($subject->pass_marks / $subject->full_marks) * 100) }}%</h3>
							<small class="text-muted">Pass %</small>
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
					<a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-outline-primary">
						<i data-feather="edit" class="me-1"></i> Edit Subject
					</a>
					<a href="{{ route('admin.subjects.create') }}" class="btn btn-outline-success">
						<i data-feather="plus" class="me-1"></i> Add New Subject
					</a>
				</div>
			</div>
		</div>

		<!-- Back Button -->
		<a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary w-100">
			<i data-feather="arrow-left" class="me-1"></i> Back to Subjects
		</a>
	</div>
</div>
@endsection
