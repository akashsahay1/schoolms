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
	</div>

	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Class Statistics</h6>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Sections</span>
					<span class="badge bg-info">{{ $class->sections->count() }}</span>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-3">
					<span class="text-muted">Students</span>
					<span class="badge bg-primary">{{ $class->students->count() }}</span>
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
