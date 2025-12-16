@extends('layouts.app')

@section('title', 'Add New Teacher')

@section('page-title', 'Add New Teacher')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
	<li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
	@csrf

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

	<div class="row">
		<div class="col-12 col-lg-8">
			<!-- Basic Information -->
			<div class="card">
				<div class="card-header">
					<h5>Basic Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
							@error('first_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="last_name" class="form-label">Last Name</label>
							<input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}">
							@error('last_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
							<select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
								<option value="">Select Gender</option>
								<option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
								<option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
								<option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('gender')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
							<input type="text" class="form-control datepicker @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" placeholder="DD-MM-YYYY" required>
							@error('date_of_birth')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="blood_group" class="form-label">Blood Group</label>
							<select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
								<option value="">Select</option>
								@foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
									<option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
								@endforeach
							</select>
							@error('blood_group')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="religion" class="form-label">Religion</label>
							<input type="text" class="form-control @error('religion') is-invalid @enderror" id="religion" name="religion" value="{{ old('religion') }}">
							@error('religion')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="marital_status" class="form-label">Marital Status</label>
							<select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status">
								<option value="">Select</option>
								<option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
								<option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
								<option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
								<option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
							</select>
							@error('marital_status')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="nationality" class="form-label">Nationality</label>
							<input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', 'Indian') }}">
							@error('nationality')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Contact Information -->
			<div class="card">
				<div class="card-header">
					<h5>Contact Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="email" class="form-label">Email <span class="text-danger">*</span></label>
							<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
							@error('email')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
							@error('phone')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="emergency_contact" class="form-label">Emergency Contact</label>
							<input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}">
							@error('emergency_contact')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<label for="current_address" class="form-label">Current Address</label>
							<textarea class="form-control @error('current_address') is-invalid @enderror" id="current_address" name="current_address" rows="2">{{ old('current_address') }}</textarea>
							@error('current_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<label for="permanent_address" class="form-label">Permanent Address</label>
							<textarea class="form-control @error('permanent_address') is-invalid @enderror" id="permanent_address" name="permanent_address" rows="2">{{ old('permanent_address') }}</textarea>
							@error('permanent_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Employment Information -->
			<div class="card">
				<div class="card-header">
					<h5>Employment Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
							<select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
								<option value="">Select Department</option>
								@foreach($departments as $department)
									<option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
										{{ $department->name }}
									</option>
								@endforeach
							</select>
							@error('department_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-6">
							<label for="designation_id" class="form-label">Designation <span class="text-danger">*</span></label>
							<select class="form-select @error('designation_id') is-invalid @enderror" id="designation_id" name="designation_id" required>
								<option value="">Select Designation</option>
								@foreach($designations as $designation)
									<option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
										{{ $designation->name }}
									</option>
								@endforeach
							</select>
							@error('designation_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
							<input type="text" class="form-control datepicker @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date') }}" placeholder="DD-MM-YYYY" required>
							@error('joining_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="contract_type" class="form-label">Contract Type <span class="text-danger">*</span></label>
							<select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type" required>
								<option value="permanent" {{ old('contract_type', 'permanent') == 'permanent' ? 'selected' : '' }}>Permanent</option>
								<option value="temporary" {{ old('contract_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
								<option value="contractual" {{ old('contract_type') == 'contractual' ? 'selected' : '' }}>Contractual</option>
							</select>
							@error('contract_type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-md-4">
							<label for="basic_salary" class="form-label">Basic Salary</label>
							<input type="number" class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary" value="{{ old('basic_salary') }}" step="0.01" min="0">
							@error('basic_salary')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Qualifications -->
			<div class="card">
				<div class="card-header">
					<h5>Qualifications</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-12">
							<label for="qualification" class="form-label">Qualifications</label>
							<textarea class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" rows="2" placeholder="e.g., B.Ed, M.Sc Mathematics">{{ old('qualification') }}</textarea>
							@error('qualification')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						<div class="col-12">
							<label for="experience" class="form-label">Experience</label>
							<textarea class="form-control @error('experience') is-invalid @enderror" id="experience" name="experience" rows="2" placeholder="Previous teaching experience">{{ old('experience') }}</textarea>
							@error('experience')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-lg-4">
			<!-- Photo Upload -->
			<div class="card">
				<div class="card-header">
					<h5>Photo</h5>
				</div>
				<div class="card-body text-center">
					<div class="mb-3">
						<img id="photoPreview" src="{{ asset('assets/images/user/user.png') }}" alt="Photo Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
					</div>
					<input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
					<small class="text-muted">Max size: 2MB. Formats: JPG, PNG</small>
					@error('photo')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<!-- Actions -->
			<div class="card">
				<div class="card-body">
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Save Teacher
						</button>
						<a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
							<i data-feather="arrow-left" class="me-1"></i> Cancel
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection

@push('scripts')
<script>
	function previewPhoto(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById('photoPreview').src = e.target.result;
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
</script>
@endpush
