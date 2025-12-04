@extends('layouts.app')

@section('title', 'Add New Section')

@section('page-title', 'Add New Section')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
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

		<form action="{{ route('admin.sections.store') }}" method="POST">
			@csrf

			<div class="card">
				<div class="card-header">
					<h5>Section Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Section Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., A, B, Science, Commerce" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
							<select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
								<option value="">Select Class</option>
								@foreach($classes as $class)
									<option value="{{ $class->id }}" {{ old('class_id', request('class_id')) == $class->id ? 'selected' : '' }}>
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
							<input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" placeholder="Maximum students">
							<small class="text-muted">Leave empty for unlimited</small>
							@error('capacity')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="room_no" class="form-label">Room Number</label>
							<input type="text" class="form-control @error('room_no') is-invalid @enderror" id="room_no" name="room_no" value="{{ old('room_no') }}" placeholder="e.g., 101, Block A-12">
							@error('room_no')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="class_teacher_id" class="form-label">Class Teacher</label>
							<select class="form-select @error('class_teacher_id') is-invalid @enderror" id="class_teacher_id" name="class_teacher_id">
								<option value="">Select Teacher (Optional)</option>
								@foreach($teachers as $teacher)
									<option value="{{ $teacher->id }}" {{ old('class_teacher_id') == $teacher->id ? 'selected' : '' }}>
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
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
							<i data-feather="save" class="me-1"></i> Create Section
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
						Use simple names like "A", "B", "C" for sections.
					</li>
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Set capacity to limit student enrollment.
					</li>
					<li class="mb-2">
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Room number helps in timetable management.
					</li>
					<li>
						<i data-feather="info" class="text-primary me-2" style="width: 16px;"></i>
						Assign a class teacher for better management.
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection
