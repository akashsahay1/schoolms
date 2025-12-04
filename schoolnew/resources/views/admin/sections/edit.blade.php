@extends('layouts.app')

@section('title', 'Edit Section')

@section('page-title', 'Edit Section')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
	<li class="breadcrumb-item active">Edit {{ $section->full_name }}</li>
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

		<form action="{{ route('admin.sections.update', $section) }}" method="POST">
			@csrf
			@method('PUT')

			<div class="card">
				<div class="card-header">
					<h5>Edit Section Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Section Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $section->name) }}" placeholder="e.g., A, B, Science, Commerce" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
							<select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
								<option value="">Select Class</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ old('class_id', $section->class_id) == $class->id ? 'selected' : '' }}>
										{{ $class->name }}
									</option>
								@endforeach
							</select>
							@error('class_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="capacity" class="form-label">Capacity</label>
							<input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $section->capacity) }}" min="1" placeholder="Maximum students">
							<small class="text-muted">Leave empty for unlimited</small>
							@error('capacity')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="room_no" class="form-label">Room Number</label>
							<input type="text" class="form-control @error('room_no') is-invalid @enderror" id="room_no" name="room_no" value="{{ old('room_no', $section->room_no) }}" placeholder="e.g., 101, Block A-12">
							@error('room_no')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="class_teacher_id" class="form-label">Class Teacher</label>
							<select class="form-select @error('class_teacher_id') is-invalid @enderror" id="class_teacher_id" name="class_teacher_id">
								<option value="">Select Teacher (Optional)</option>
								@foreach($teachers as $teacher)
									<option value="{{ $teacher->id }}" {{ old('class_teacher_id', $section->class_teacher_id) == $teacher->id ? 'selected' : '' }}>
										{{ $teacher->name }}
									</option>
								@endforeach
							</select>
							@error('class_teacher_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
								<label class="form-check-label" for="is_active">
									Active Section
								</label>
								<small class="text-muted d-block">Inactive sections won't appear in student registration</small>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="d-flex justify-content-between">
						<a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
							<i data-feather="arrow-left" class="me-1"></i> Cancel
						</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Section
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Section Statistics</h6>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Students</span>
					<span class="badge bg-primary">{{ $section->students->count() }}</span>
				</div>
				@if($section->capacity)
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="text-muted">Capacity</span>
						<span class="badge bg-info">{{ $section->capacity }}</span>
					</div>
					<div class="progress mb-2" style="height: 8px;">
						@php
							$percentage = min(100, ($section->students->count() / $section->capacity) * 100);
						@endphp
						<div class="progress-bar bg-{{ $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success') }}" style="width: {{ $percentage }}%"></div>
					</div>
					<small class="text-muted">{{ round($percentage) }}% filled</small>
				@endif
				<div class="d-flex justify-content-between align-items-center mt-3">
					<span class="text-muted">Created</span>
					<span class="text-muted">{{ $section->created_at->format('d M Y') }}</span>
				</div>
			</div>
		</div>

		@if($section->students->count() > 0)
			<div class="card border-warning">
				<div class="card-body">
					<h6 class="text-warning mb-2">
						<i data-feather="alert-triangle" class="me-1"></i> Warning
					</h6>
					<p class="text-muted mb-0 small">
						This section has {{ $section->students->count() }} students. Changing the class will affect all these students.
					</p>
				</div>
			</div>
		@endif
	</div>
</div>
@endsection
