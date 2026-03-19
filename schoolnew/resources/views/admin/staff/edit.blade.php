@extends('layouts.app')

@section('title', 'Edit Staff Member')

@section('page-title', 'Edit Staff Member')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
	<li class="breadcrumb-item active">Edit {{ $staff->full_name }}</li>
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

@if(auth()->user()->hasRole('Super Admin'))
<div class="d-flex justify-content-end gap-2 mb-3">
	<a href="{{ route('admin.custom-fields.form-settings') }}" class="btn btn-outline-info btn-sm">
		<i class="icon-settings"></i> Form Fields Settings
	</a>
	<a href="{{ route('admin.custom-fields.create') }}" class="btn btn-outline-primary btn-sm">
		<i class="icon-plus"></i> Add Custom Field
	</a>
</div>
@endif

<form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
	@csrf
	@method('PUT')

	@if(session('error'))
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			{{ session('error') }}
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	@endif

	@if($errors->any())
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<ul class="mb-0">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
						@if($isVisible('first_name'))
						<div class="col-md-6">
							<label for="first_name" class="form-label">First Name @if($isRequired('first_name'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}" {{ $isRequired('first_name') ? 'required' : '' }}>
							@error('first_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('last_name'))
						<div class="col-md-6">
							<label for="last_name" class="form-label">Last Name @if($isRequired('last_name'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}" {{ $isRequired('last_name') ? 'required' : '' }}>
							@error('last_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('gender'))
						<div class="col-md-6">
							<label for="gender" class="form-label">Gender @if($isRequired('gender'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" {{ $isRequired('gender') ? 'required' : '' }}>
								<option value="">Select Gender</option>
								<option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Male</option>
								<option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Female</option>
								<option value="other" {{ old('gender', $staff->gender) == 'other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('gender')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('date_of_birth'))
						<div class="col-md-6">
							<label for="date_of_birth" class="form-label">Date of Birth @if($isRequired('date_of_birth'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control datepicker @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth?->format('d-m-Y')) }}" placeholder="DD-MM-YYYY" {{ $isRequired('date_of_birth') ? 'required' : '' }}>
							@error('date_of_birth')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						<div class="col-md-4">
							<label for="blood_group" class="form-label">Blood Group</label>
							<select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
								<option value="">Select</option>
								@foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
									<option value="{{ $bg }}" {{ old('blood_group', $staff->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-4">
							<label for="religion" class="form-label">Religion</label>
							<select class="form-select @error('religion') is-invalid @enderror" id="religion" name="religion">
								<option value="">Select</option>
								<option value="Hindu" {{ old('religion', $staff->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
								<option value="Muslim" {{ old('religion', $staff->religion) == 'Muslim' ? 'selected' : '' }}>Muslim</option>
								<option value="Christian" {{ old('religion', $staff->religion) == 'Christian' ? 'selected' : '' }}>Christian</option>
								<option value="Sikh" {{ old('religion', $staff->religion) == 'Sikh' ? 'selected' : '' }}>Sikh</option>
								<option value="Buddhist" {{ old('religion', $staff->religion) == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
								<option value="Jain" {{ old('religion', $staff->religion) == 'Jain' ? 'selected' : '' }}>Jain</option>
								<option value="Other" {{ old('religion', $staff->religion) == 'Other' ? 'selected' : '' }}>Other</option>
							</select>
						</div>
						<div class="col-md-4">
							<label for="marital_status" class="form-label">Marital Status</label>
							<select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status">
								<option value="">Select</option>
								<option value="single" {{ old('marital_status', $staff->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
								<option value="married" {{ old('marital_status', $staff->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
								<option value="divorced" {{ old('marital_status', $staff->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
								<option value="widowed" {{ old('marital_status', $staff->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
							</select>
						</div>
						<div class="col-md-6">
							<label for="nationality" class="form-label">Nationality</label>
							<input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', $staff->nationality) }}">
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
						@if($isVisible('phone'))
						<div class="col-md-6">
							<label for="phone" class="form-label">Phone @if($isRequired('phone'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $staff->phone) }}" {{ $isRequired('phone') ? 'required' : '' }}>
							@error('phone')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('email'))
						<div class="col-md-6">
							<label for="email" class="form-label">Email @if($isRequired('email'))<span class="text-danger">*</span>@endif</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $staff->email) }}" {{ $isRequired('email') ? 'required' : '' }}>
							@error('email')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('current_address'))
						<div class="col-md-6">
							<label for="current_address" class="form-label">Current Address @if($isRequired('current_address'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('current_address') is-invalid @enderror" id="current_address" name="current_address" rows="2" {{ $isRequired('current_address') ? 'required' : '' }}>{{ old('current_address', $staff->current_address) }}</textarea>
							@error('current_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						<div class="col-md-6">
							<label for="permanent_address" class="form-label">Permanent Address</label>
							<textarea class="form-control @error('permanent_address') is-invalid @enderror" id="permanent_address" name="permanent_address" rows="2">{{ old('permanent_address', $staff->permanent_address) }}</textarea>
							@error('permanent_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@if($isVisible('emergency_contact'))
						<div class="col-md-6">
							<label for="emergency_contact" class="form-label">Emergency Contact @if($isRequired('emergency_contact'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $staff->emergency_contact) }}" {{ $isRequired('emergency_contact') ? 'required' : '' }}>
							@error('emergency_contact')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Job Details -->
			<div class="card">
				<div class="card-header">
					<h5>Job Details</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						@if($isVisible('designation_id'))
						<div class="col-md-6">
							<label for="designation_id" class="form-label">Role @if($isRequired('designation_id'))<span class="text-danger">*</span>@endif</label>
							<div class="input-group">
								<select class="form-select @error('designation_id') is-invalid @enderror" id="designation_id" name="designation_id" {{ $isRequired('designation_id') ? 'required' : '' }}>
									<option value="">Select Role</option>
									@foreach($designations as $designation)
										<option value="{{ $designation->id }}" {{ old('designation_id', $staff->designation_id) == $designation->id ? 'selected' : '' }}>{{ $designation->name }}</option>
									@endforeach
								</select>
								<a href="{{ route('admin.designations.create') }}" class="btn btn-outline-primary" title="Add New Role" target="_blank">
									<i class="icon-plus"></i>
								</a>
							</div>
							@error('designation_id')
								<div class="text-danger small mt-1">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('joining_date'))
						<div class="col-md-6">
							<label for="joining_date" class="form-label">Joining Date @if($isRequired('joining_date'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control datepicker @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date', $staff->joining_date?->format('d-m-Y')) }}" placeholder="DD-MM-YYYY" {{ $isRequired('joining_date') ? 'required' : '' }}>
							@error('joining_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('contract_type'))
						<div class="col-md-4">
							<label for="contract_type" class="form-label">Contract Type @if($isRequired('contract_type'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type" {{ $isRequired('contract_type') ? 'required' : '' }}>
								<option value="permanent" {{ old('contract_type', $staff->contract_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
								<option value="temporary" {{ old('contract_type', $staff->contract_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
								<option value="contractual" {{ old('contract_type', $staff->contract_type) == 'contractual' ? 'selected' : '' }}>Contractual</option>
							</select>
							@error('contract_type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('basic_salary'))
						<div class="col-md-4">
							<label for="basic_salary" class="form-label">Basic Salary @if($isRequired('basic_salary'))<span class="text-danger">*</span>@endif</label>
							<input type="number" class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', $staff->basic_salary) }}" step="0.01" min="0" {{ $isRequired('basic_salary') ? 'required' : '' }}>
							@error('basic_salary')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						<div class="col-md-4">
							<label for="status" class="form-label">Status <span class="text-danger">*</span></label>
							<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
								<option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
								<option value="resigned" {{ old('status', $staff->status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
								<option value="terminated" {{ old('status', $staff->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
							</select>
							@error('status')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Documents -->
			@if($isVisible('aadhaar_number') || $isVisible('aadhaar_front') || $isVisible('aadhaar_back'))
			<div class="card">
				<div class="card-header">
					<h5>Documents</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						@if($isVisible('aadhaar_number'))
						<div class="col-md-4">
							<label for="aadhaar_number" class="form-label">Aadhaar Number @if($isRequired('aadhaar_number'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('aadhaar_number') is-invalid @enderror" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number', $staff->aadhaar_number) }}" maxlength="12" {{ $isRequired('aadhaar_number') ? 'required' : '' }}>
							@error('aadhaar_number')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('aadhaar_front'))
						<div class="col-md-4">
							<label for="aadhaar_front" class="form-label">Aadhaar Front</label>
							<input type="file" class="form-control @error('aadhaar_front') is-invalid @enderror" id="aadhaar_front" name="aadhaar_front" accept="image/*,.pdf">
							@error('aadhaar_front')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							@if($staff->aadhaar_front)
								<small class="text-success"><a href="{{ asset('storage/' . $staff->aadhaar_front) }}" target="_blank">View current file</a></small>
							@endif
						</div>
						@endif
						@if($isVisible('aadhaar_back'))
						<div class="col-md-4">
							<label for="aadhaar_back" class="form-label">Aadhaar Back</label>
							<input type="file" class="form-control @error('aadhaar_back') is-invalid @enderror" id="aadhaar_back" name="aadhaar_back" accept="image/*,.pdf">
							@error('aadhaar_back')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							@if($staff->aadhaar_back)
								<small class="text-success"><a href="{{ asset('storage/' . $staff->aadhaar_back) }}" target="_blank">View current file</a></small>
							@endif
						</div>
						@endif
					</div>
				</div>
			</div>
			@endif

			<!-- Custom Fields -->
			@include('admin.custom-fields._form-fields', [
				'customFields' => $customFields,
				'customFieldValues' => $customFieldValues,
				'formContext' => 'edit'
			])
		</div>

		<div class="col-12 col-lg-4">
			<!-- Photo -->
			@if($isVisible('photo'))
			<div class="card">
				<div class="card-header">
					<h5>Photo</h5>
				</div>
				<div class="card-body text-center">
					<div class="mb-3">
						<img id="photoPreview" src="{{ $staff->photo_url }}" alt="Photo" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
					</div>
					<input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
					<small class="text-muted">Max: 2MB. JPG, PNG</small>
					@error('photo')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>
			@endif

			<!-- Staff Info -->
			<div class="card">
				<div class="card-header">
					<h6 class="mb-0">Staff Info</h6>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="text-muted">Staff ID</span>
						<span class="fw-bold">{{ $staff->staff_id }}</span>
					</div>
					<div class="d-flex justify-content-between align-items-center mb-3">
						<span class="text-muted">Joining Date</span>
						<span>{{ $staff->joining_date?->format('d M Y') }}</span>
					</div>
					<div class="d-flex justify-content-between align-items-center">
						<span class="text-muted">Experience</span>
						<span>{{ $staff->experience_years }} years</span>
					</div>
				</div>
			</div>

			<!-- Actions -->
			<div class="card">
				<div class="card-body">
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">
							<i class="icon-save me-1"></i> Update Staff Member
						</button>
						<a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-info">
							<i class="icon-eye me-1"></i> View Profile
						</a>
						<a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
							<i class="icon-arrow-left me-1"></i> Cancel
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
