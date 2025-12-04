@extends('layouts.app')

@section('title', 'Add New Subject')

@section('page-title', 'Add New Subject')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
	<li class="breadcrumb-item active">Add New</li>
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

		<form action="{{ route('admin.subjects.store') }}" method="POST">
			@csrf

			<div class="card">
				<div class="card-header">
					<h5>Subject Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Mathematics, Physics, English" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="code" class="form-label">Subject Code <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="e.g., MATH, PHY, ENG" required style="text-transform: uppercase;">
							<small class="text-muted">Unique code for the subject</small>
							@error('code')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="type" class="form-label">Subject Type <span class="text-danger">*</span></label>
							<select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
								<option value="theory" {{ old('type') == 'theory' ? 'selected' : '' }}>Theory</option>
								<option value="practical" {{ old('type') == 'practical' ? 'selected' : '' }}>Practical</option>
								<option value="both" {{ old('type') == 'both' ? 'selected' : '' }}>Both (Theory + Practical)</option>
							</select>
							@error('type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="full_marks" class="form-label">Full Marks <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('full_marks') is-invalid @enderror" id="full_marks" name="full_marks" value="{{ old('full_marks', 100) }}" min="1" required>
							@error('full_marks')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="pass_marks" class="form-label">Pass Marks <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('pass_marks') is-invalid @enderror" id="pass_marks" name="pass_marks" value="{{ old('pass_marks', 33) }}" min="0" required>
							@error('pass_marks')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<label class="form-label">Assign to Classes</label>
							<div class="row">
								@foreach($classes as $class)
									<div class="col-md-4 col-6">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="class_{{ $class->id }}" name="classes[]" value="{{ $class->id }}" {{ in_array($class->id, old('classes', [])) ? 'checked' : '' }}>
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
								<input class="form-check-input" type="checkbox" id="is_optional" name="is_optional" value="1" {{ old('is_optional') ? 'checked' : '' }}>
								<label class="form-check-label" for="is_optional">
									Optional Subject
								</label>
								<small class="text-muted d-block">Students can choose whether to take this subject</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
							<i data-feather="save" class="me-1"></i> Create Subject
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Quick Tips</h6>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Subject code should be unique (e.g., MATH101).
					</li>
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Choose "Both" type for subjects with theory and lab components.
					</li>
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Pass marks should be less than full marks.
					</li>
					<li>
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Mark as optional for elective subjects.
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection
