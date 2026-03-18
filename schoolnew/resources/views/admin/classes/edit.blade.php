@extends('layouts.app')

@section('title', 'Edit Class')

@section('page-title', 'Edit Class')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
	<li class="breadcrumb-item active">Edit {{ $class->name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-8">
		<!-- Error Messages -->
		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		@if($errors->any())
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<ul class="mb-0">
					@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<form action="{{ route('admin.classes.update', $class) }}" method="POST">
			@csrf
			@method('PUT')

			<div class="card">
				<div class="card-header">
					<h5>Edit Class Information</h5>
					<p class="text-muted mb-0">Academic Year: <strong>{{ $class->academicYear->name ?? 'N/A' }}</strong></p>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $class->name) }}" placeholder="e.g., Class 10, Grade 5, Nursery" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="numeric_name" class="form-label">Numeric Name</label>
							<input type="text" class="form-control @error('numeric_name') is-invalid @enderror" id="numeric_name" name="numeric_name" value="{{ old('numeric_name', $class->numeric_name) }}" placeholder="e.g., 10, 5, LKG">
							<small class="text-muted">Used for sorting and identification</small>
							@error('numeric_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="pass_mark" class="form-label">Pass Mark (%) <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('pass_mark') is-invalid @enderror" id="pass_mark" name="pass_mark" value="{{ old('pass_mark', $class->pass_mark) }}" min="0" max="100" required>
							<small class="text-muted">Minimum marks required to pass</small>
							@error('pass_mark')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="order" class="form-label">Display Order</label>
							<input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $class->order) }}" min="0">
							<small class="text-muted">Order in which classes appear in dropdowns</small>
							@error('order')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
								<label class="form-check-label" for="is_active">
									Active Class
								</label>
								<small class="text-muted d-block">Inactive classes won't appear in student registration</small>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="d-flex justify-content-between">
						<a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
							<i data-feather="arrow-left" class="me-1"></i> Cancel
						</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Class
						</button>
					</div>
				</div>
			</div>
		</form>

		<!-- Subjects Management -->
		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Assigned Subjects ({{ $class->subjects->count() }})</h5>
				</div>
			</div>
			<div class="card-body">
				@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				@endif

				<!-- Add Subject -->
				@php
					$assignedIds = $class->subjects->pluck('id')->toArray();
					$availableSubjects = $allSubjects->whereNotIn('id', $assignedIds);
				@endphp
				@if($availableSubjects->count() > 0)
				<form action="{{ route('admin.classes.add-subject', $class) }}" method="POST" class="mb-3">
					@csrf
					<div class="input-group">
						<select class="form-select" name="subject_id" required>
							<option value="">Select Subject to Add</option>
							@foreach($availableSubjects as $subject)
								<option value="{{ $subject->id }}">{{ $subject->name }}</option>
							@endforeach
						</select>
						<button type="submit" class="btn btn-primary">
							<i class="icon-plus"></i> Add
						</button>
					</div>
				</form>
				@else
					<div class="alert alert-info mb-3">All subjects are already assigned to this class.</div>
				@endif

				<!-- Assigned List with Teacher Assignment -->
				@if($class->subjects->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped mb-0">
							<thead>
								<tr>
									<th>Subject</th>
									<th>Assigned Teacher</th>
									<th style="width: 70px;">Remove</th>
								</tr>
							</thead>
							<tbody>
								@foreach($class->subjects as $subject)
									<tr>
										<td>
											<strong>{{ $subject->name }}</strong>
											@if($subject->code)<br><small class="text-muted">{{ $subject->code }}</small>@endif
										</td>
										<td>
											<form action="{{ route('admin.classes.assign-teacher', $class) }}" method="POST" class="assign-teacher-form">
												@csrf
												<input type="hidden" name="subject_id" value="{{ $subject->id }}">
												<select class="form-select form-select-sm" name="teacher_id" onchange="this.form.submit()">
													<option value="">-- No Teacher --</option>
													@foreach($teachers as $teacher)
														<option value="{{ $teacher->id }}" {{ $subject->pivot->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
													@endforeach
												</select>
											</form>
										</td>
										<td>
											<form action="{{ route('admin.classes.remove-subject', [$class, $subject]) }}" method="POST" class="d-inline remove-subject-form">
												@csrf
												@method('DELETE')
												<button type="button" class="btn btn-outline-danger btn-sm remove-subject-btn" data-name="{{ $subject->name }}">
													<i class="icon-trash"></i>
												</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<div class="text-center py-3">
						<p class="text-muted mb-0">No subjects assigned yet. Add subjects from the dropdown above.</p>
					</div>
				@endif
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Class Statistics</h6>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Sections</span>
					<span class="badge badge-light-info">{{ $class->sections->count() }}</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Students</span>
					<span class="badge badge-light-primary">{{ $class->students->count() }}</span>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span class="text-muted">Created</span>
					<span class="text-muted">{{ $class->created_at->format('d M Y') }}</span>
				</div>
			</div>
		</div>

		@if($class->students->count() > 0 || $class->sections->count() > 0)
			<div class="card border-warning">
				<div class="card-body">
					<h6 class="text-warning mb-2">
						<i data-feather="alert-triangle" class="me-1"></i> Warning
					</h6>
					<p class="text-muted mb-0 small">
						This class has {{ $class->students->count() }} students and {{ $class->sections->count() }} sections. Making it inactive will hide it from new registrations but won't affect existing data.
					</p>
				</div>
			</div>
		@endif
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery(document).on('click', '.remove-subject-btn', function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		var name = jQuery(this).data('name');
		Swal.fire({
			title: 'Remove Subject?',
			html: 'Remove <strong>' + name + '</strong> from this class?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes, remove',
			reverseButtons: true
		}).then(function(result) {
			if (result.isConfirmed) form.submit();
		});
	});
});
</script>
@endpush
