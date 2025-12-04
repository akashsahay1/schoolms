@extends('layouts.app')

@section('title', 'Edit Subject')

@section('page-title', 'Edit Subject')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
	<li class="breadcrumb-item active">Edit {{ $subject->name }}</li>
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

		<form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
			@csrf
			@method('PUT')

			<div class="card">
				<div class="card-header">
					<h5>Edit Subject Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subject->name) }}" placeholder="e.g., Mathematics, Physics, English" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="code" class="form-label">Subject Code <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $subject->code) }}" placeholder="e.g., MATH, PHY, ENG" required style="text-transform: uppercase;">
							<small class="text-muted">Unique code for the subject</small>
							@error('code')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="type" class="form-label">Subject Type <span class="text-danger">*</span></label>
							<select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
								<option value="theory" {{ old('type', $subject->type) == 'theory' ? 'selected' : '' }}>Theory</option>
								<option value="practical" {{ old('type', $subject->type) == 'practical' ? 'selected' : '' }}>Practical</option>
								<option value="both" {{ old('type', $subject->type) == 'both' ? 'selected' : '' }}>Both (Theory + Practical)</option>
							</select>
							@error('type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="full_marks" class="form-label">Full Marks <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('full_marks') is-invalid @enderror" id="full_marks" name="full_marks" value="{{ old('full_marks', $subject->full_marks) }}" min="1" required>
							@error('full_marks')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="pass_marks" class="form-label">Pass Marks <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('pass_marks') is-invalid @enderror" id="pass_marks" name="pass_marks" value="{{ old('pass_marks', $subject->pass_marks) }}" min="0" required>
							@error('pass_marks')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<label class="form-label">Assign to Classes</label>
							@php
								$assignedClasses = $subject->classes->pluck('id')->toArray();
							@endphp
							<div class="row">
								@foreach($classes as $class)
									<div class="col-md-4 col-6">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="class_{{ $class->id }}" name="classes[]" value="{{ $class->id }}" {{ in_array($class->id, old('classes', $assignedClasses)) ? 'checked' : '' }}>
											<label class="form-check-label" for="class_{{ $class->id }}">
												{{ $class->name }}
											</label>
										</div>
									</div>
								@endforeach
							</div>
							<small class="text-muted">Select classes where this subject will be taught</small>
						</div>
						<div class="col-md-6">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_optional" name="is_optional" value="1" {{ old('is_optional', $subject->is_optional) ? 'checked' : '' }}>
								<label class="form-check-label" for="is_optional">
									Optional Subject
								</label>
								<small class="text-muted d-block">Students can choose whether to take this subject</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
								<label class="form-check-label" for="is_active">
									Active Subject
								</label>
								<small class="text-muted d-block">Inactive subjects won't appear in exam/marks entry</small>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="d-flex justify-content-between">
						<a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">
							<i data-feather="arrow-left" class="me-1"></i> Cancel
						</a>
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Subject
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Subject Statistics</h6>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Assigned Classes</span>
					<span class="badge bg-primary">{{ $subject->classes->count() }}</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Type</span>
					<span class="badge bg-info">{{ ucfirst($subject->type) }}</span>
				</div>
				<div class="d-flex justify-content-between align-items-center">
					<span class="text-muted">Created</span>
					<span class="text-muted">{{ $subject->created_at->format('d M Y') }}</span>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
