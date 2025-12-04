@extends('layouts.app')

@section('title', 'Add New Class')

@section('page-title', 'Add New Class')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
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

		<form action="{{ route('admin.classes.store') }}" method="POST">
			@csrf

			<div class="card">
				<div class="card-header">
					<h5>Class Information</h5>
					<p class="text-muted mb-0">Academic Year: <strong>{{ $academicYear->name }}</strong></p>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Class 10, Grade 5, Nursery" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="numeric_name" class="form-label">Numeric Name</label>
							<input type="text" class="form-control @error('numeric_name') is-invalid @enderror" id="numeric_name" name="numeric_name" value="{{ old('numeric_name') }}" placeholder="e.g., 10, 5, LKG">
							<small class="text-muted">Used for sorting and identification</small>
							@error('numeric_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="pass_mark" class="form-label">Pass Mark (%) <span class="text-danger">*</span></label>
							<input type="number" class="form-control @error('pass_mark') is-invalid @enderror" id="pass_mark" name="pass_mark" value="{{ old('pass_mark', 33) }}" min="0" max="100" required>
							<small class="text-muted">Minimum marks required to pass</small>
							@error('pass_mark')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="order" class="form-label">Display Order</label>
							<input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
							<small class="text-muted">Order in which classes appear in dropdowns</small>
							@error('order')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
							<i data-feather="save" class="me-1"></i> Create Class
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
						Use descriptive names like "Class 1", "Grade 5", etc.
					</li>
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Numeric name helps in sorting classes properly.
					</li>
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Set display order to arrange classes in dropdowns.
					</li>
					<li>
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						After creating a class, add sections to it.
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection
