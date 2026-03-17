@extends('layouts.app')

@section('title', 'Add New Staff')

@section('page-title', 'Add New Staff Member')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
	<li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
@php
	$fs = $fieldSettings ?? [];
	$isVisible = function($field) use ($fs) {
		return ($fs[$field]['visible'] ?? true);
	};
	$isRequired = function($field) use ($fs) {
		return ($fs[$field]['required'] ?? false) && ($fs[$field]['visible'] ?? true);
	};
@endphp
<form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
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
						@if($isVisible('last_name'))
						<div class="col-md-6" data-field-wrapper="last_name">
							<label for="last_name" class="form-label">Last Name @if($isRequired('last_name'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" {{ $isRequired('last_name') ? 'required' : '' }}>
							@error('last_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
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
						@if($isVisible('blood_group'))
						<div class="col-md-4" data-field-wrapper="blood_group">
							<label for="blood_group" class="form-label">Blood Group @if($isRequired('blood_group'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group" {{ $isRequired('blood_group') ? 'required' : '' }}>
								<option value="">Select</option>
								@foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
									<option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
								@endforeach
							</select>
							@error('blood_group')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('religion'))
						<div class="col-md-4" data-field-wrapper="religion">
							<label for="religion" class="form-label">Religion @if($isRequired('religion'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('religion') is-invalid @enderror" id="religion" name="religion" {{ $isRequired('religion') ? 'required' : '' }}>
								<option value="">Select Religion</option>
								<option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
								<option value="Muslim" {{ old('religion') == 'Muslim' ? 'selected' : '' }}>Muslim</option>
								<option value="Christian" {{ old('religion') == 'Christian' ? 'selected' : '' }}>Christian</option>
								<option value="Sikh" {{ old('religion') == 'Sikh' ? 'selected' : '' }}>Sikh</option>
								<option value="Buddhist" {{ old('religion') == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
								<option value="Jain" {{ old('religion') == 'Jain' ? 'selected' : '' }}>Jain</option>
								<option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('religion')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('marital_status'))
						<div class="col-md-4" data-field-wrapper="marital_status">
							<label for="marital_status" class="form-label">Marital Status @if($isRequired('marital_status'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status" {{ $isRequired('marital_status') ? 'required' : '' }}>
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
						@endif
						@if($isVisible('nationality'))
						<div class="col-md-4" data-field-wrapper="nationality">
							<label for="nationality" class="form-label">Nationality @if($isRequired('nationality'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', 'Indian') }}" {{ $isRequired('nationality') ? 'required' : '' }}>
							@error('nationality')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
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
						@if($isVisible('emergency_contact'))
						<div class="col-md-6" data-field-wrapper="emergency_contact">
							<label for="emergency_contact" class="form-label">Emergency Contact @if($isRequired('emergency_contact'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" {{ $isRequired('emergency_contact') ? 'required' : '' }}>
							@error('emergency_contact')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('current_address'))
						<div class="col-12" data-field-wrapper="current_address">
							<label for="current_address" class="form-label">Current Address @if($isRequired('current_address'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('current_address') is-invalid @enderror" id="current_address" name="current_address" rows="2" {{ $isRequired('current_address') ? 'required' : '' }}>{{ old('current_address') }}</textarea>
							@error('current_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('permanent_address'))
						<div class="col-12" data-field-wrapper="permanent_address">
							<label for="permanent_address" class="form-label">Permanent Address @if($isRequired('permanent_address'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('permanent_address') is-invalid @enderror" id="permanent_address" name="permanent_address" rows="2" {{ $isRequired('permanent_address') ? 'required' : '' }}>{{ old('permanent_address') }}</textarea>
							@error('permanent_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
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
						@if($isVisible('basic_salary'))
						<div class="col-md-4" data-field-wrapper="basic_salary">
							<label for="basic_salary" class="form-label">Basic Salary @if($isRequired('basic_salary'))<span class="text-danger">*</span>@endif</label>
							<input type="number" class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary" value="{{ old('basic_salary') }}" step="0.01" min="0" {{ $isRequired('basic_salary') ? 'required' : '' }}>
							@error('basic_salary')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Aadhaar & PAN Card Details -->
			@if($isVisible('aadhaar_number') || $isVisible('aadhaar_front') || $isVisible('aadhaar_back') || $isVisible('pan_number'))
			<div class="card">
				<div class="card-header">
					<h5>Aadhaar & PAN Card Details</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						@if($isVisible('aadhaar_number'))
						<div class="col-md-6" data-field-wrapper="aadhaar_number">
							<label for="aadhaar_number" class="form-label">Aadhaar Card Number @if($isRequired('aadhaar_number'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('aadhaar_number') is-invalid @enderror" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number') }}" placeholder="Enter 12-digit Aadhaar number" maxlength="12" {{ $isRequired('aadhaar_number') ? 'required' : '' }}>
							@error('aadhaar_number')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('pan_number'))
						<div class="col-md-6" data-field-wrapper="pan_number">
							<label for="pan_number" class="form-label">PAN Card Number @if($isRequired('pan_number'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('pan_number') is-invalid @enderror" id="pan_number" name="pan_number" value="{{ old('pan_number') }}" placeholder="Enter PAN number" maxlength="10" style="text-transform: uppercase;" {{ $isRequired('pan_number') ? 'required' : '' }}>
							@error('pan_number')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('aadhaar_front'))
						<div class="col-md-6" data-field-wrapper="aadhaar_front">
							<label for="aadhaar_front" class="form-label">Aadhaar Card Front @if($isRequired('aadhaar_front'))<span class="text-danger">*</span>@endif</label>
							<input type="file" class="form-control @error('aadhaar_front') is-invalid @enderror" id="aadhaar_front" name="aadhaar_front" accept="image/*,.pdf" {{ $isRequired('aadhaar_front') ? 'required' : '' }}>
							@error('aadhaar_front')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
						</div>
						@endif
						@if($isVisible('aadhaar_back'))
						<div class="col-md-6" data-field-wrapper="aadhaar_back">
							<label for="aadhaar_back" class="form-label">Aadhaar Card Back @if($isRequired('aadhaar_back'))<span class="text-danger">*</span>@endif</label>
							<input type="file" class="form-control @error('aadhaar_back') is-invalid @enderror" id="aadhaar_back" name="aadhaar_back" accept="image/*,.pdf" {{ $isRequired('aadhaar_back') ? 'required' : '' }}>
							@error('aadhaar_back')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<small class="text-muted">JPG, PNG or PDF (max 2MB)</small>
						</div>
						@endif
					</div>
				</div>
			</div>
			@endif

			<!-- Qualifications -->
			@if($isVisible('qualification') || $isVisible('experience'))
			<div class="card">
				<div class="card-header">
					<h5>Qualifications</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						@if($isVisible('qualification'))
						<div class="col-12" data-field-wrapper="qualification">
							<label for="qualification" class="form-label">Qualifications @if($isRequired('qualification'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" rows="2" placeholder="e.g., B.Ed, M.Sc Mathematics" {{ $isRequired('qualification') ? 'required' : '' }}>{{ old('qualification') }}</textarea>
							@error('qualification')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('experience'))
						<div class="col-12" data-field-wrapper="experience">
							<label for="experience" class="form-label">Experience @if($isRequired('experience'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('experience') is-invalid @enderror" id="experience" name="experience" rows="2" placeholder="Previous work experience" {{ $isRequired('experience') ? 'required' : '' }}>{{ old('experience') }}</textarea>
							@error('experience')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
					</div>
				</div>
			</div>
			@endif

			<!-- Custom Fields -->
			@include('admin.custom-fields._form-fields', [
				'customFields' => $customFields,
				'customFieldValues' => [],
				'formContext' => 'create'
			])
		</div>

		<div class="col-12 col-lg-4">
			<!-- Photo Upload -->
			@if($isVisible('photo'))
			<div class="card" data-field-wrapper="photo">
				<div class="card-header">
					<h5>Photo</h5>
				</div>
				<div class="card-body text-center">
					<div class="mb-3">
						<img id="photoPreview" src="{{ asset('assets/images/user/user.png') }}" alt="Photo Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
					</div>
					<input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)" {{ $isRequired('photo') ? 'required' : '' }}>
					<small class="text-muted">Max size: 2MB. Formats: JPG, PNG</small>
					@error('photo')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>
			@endif

			<!-- Login Credentials -->
			<div class="card">
				<div class="card-header">
					<h5>Login Credentials</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label for="password" class="form-label">Password</label>
						<input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ old('password') }}" placeholder="Leave empty for auto-generate">
						@error('password')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<small class="text-muted">Min 6 characters. Leave empty to auto-generate a secure password.</small>
					</div>
					<div class="alert alert-info mb-0">
						<small><i data-feather="info" style="width: 14px; height: 14px;"></i> Staff will login using their <strong>Email</strong> and this password.</small>
					</div>
				</div>
			</div>

			<!-- Actions -->
			<div class="card">
				<div class="card-body">
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Save Staff Member
						</button>
						<a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
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

	if (typeof feather !== 'undefined') feather.replace();
</script>
@endpush
