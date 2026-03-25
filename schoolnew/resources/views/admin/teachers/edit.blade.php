@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('page-title', 'Edit Teacher')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
	<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
@php
	$fs = $fieldSettings ?? [];
	$alwaysRequired = ['first_name', 'gender', 'date_of_birth', 'email', 'phone', 'subject_id', 'designation_id', 'joining_date', 'contract_type'];
	$isVisible = function($field) use ($fs) {
		return ($fs[$field]['visible'] ?? true);
	};
	$isRequired = function($field) use ($fs, $alwaysRequired) {
		if (in_array($field, $alwaysRequired)) return true;
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

<form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
	@csrf
	@method('PUT')

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
						@if($isVisible('first_name'))
						<div class="col-md-6">
							<label for="first_name" class="form-label">First Name @if($isRequired('first_name'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" {{ $isRequired('first_name') ? 'required' : '' }}>
							@error('first_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('last_name'))
						<div class="col-md-6">
							<label for="last_name" class="form-label">Last Name @if($isRequired('last_name'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" {{ $isRequired('last_name') ? 'required' : '' }}>
							@error('last_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('gender'))
						<div class="col-md-4">
							<label for="gender" class="form-label">Gender @if($isRequired('gender'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" {{ $isRequired('gender') ? 'required' : '' }}>
								<option value="">Select Gender</option>
								<option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male</option>
								<option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Female</option>
								<option value="other" {{ old('gender', $teacher->gender) == 'other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('gender')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('date_of_birth'))
						<div class="col-md-4">
							<label for="date_of_birth" class="form-label">Date of Birth @if($isRequired('date_of_birth'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control datepicker @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $teacher->date_of_birth?->format('d-m-Y')) }}" placeholder="DD-MM-YYYY" {{ $isRequired('date_of_birth') ? 'required' : '' }}>
							@error('date_of_birth')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('blood_group'))
						<div class="col-md-4">
							<label for="blood_group" class="form-label">Blood Group @if($isRequired('blood_group'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group" {{ $isRequired('blood_group') ? 'required' : '' }}>
								<option value="">Select</option>
								@foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
									<option value="{{ $bg }}" {{ old('blood_group', $teacher->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
								@endforeach
							</select>
							@error('blood_group')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('religion'))
						<div class="col-md-4">
							<label for="religion" class="form-label">Religion @if($isRequired('religion'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('religion') is-invalid @enderror" id="religion" name="religion" {{ $isRequired('religion') ? 'required' : '' }}>
								<option value="">Select Religion</option>
								<option value="Hindu" {{ old('religion', $teacher->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
								<option value="Muslim" {{ old('religion', $teacher->religion) == 'Muslim' ? 'selected' : '' }}>Muslim</option>
								<option value="Christian" {{ old('religion', $teacher->religion) == 'Christian' ? 'selected' : '' }}>Christian</option>
								<option value="Sikh" {{ old('religion', $teacher->religion) == 'Sikh' ? 'selected' : '' }}>Sikh</option>
								<option value="Buddhist" {{ old('religion', $teacher->religion) == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
								<option value="Jain" {{ old('religion', $teacher->religion) == 'Jain' ? 'selected' : '' }}>Jain</option>
								<option value="Other" {{ old('religion', $teacher->religion) == 'Other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('religion')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('marital_status'))
						<div class="col-md-4">
							<label for="marital_status" class="form-label">Marital Status @if($isRequired('marital_status'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status" {{ $isRequired('marital_status') ? 'required' : '' }}>
								<option value="">Select</option>
								<option value="single" {{ old('marital_status', $teacher->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
								<option value="married" {{ old('marital_status', $teacher->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
								<option value="divorced" {{ old('marital_status', $teacher->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
								<option value="widowed" {{ old('marital_status', $teacher->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
							</select>
							@error('marital_status')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('nationality'))
						<div class="col-md-4">
							<label for="nationality" class="form-label">Nationality @if($isRequired('nationality'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('nationality') is-invalid @enderror" id="nationality" name="nationality" value="{{ old('nationality', $teacher->nationality) }}" {{ $isRequired('nationality') ? 'required' : '' }}>
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
						@if($isVisible('email'))
						<div class="col-md-6">
							<label for="email" class="form-label">Email @if($isRequired('email'))<span class="text-danger">*</span>@endif</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $teacher->email) }}" {{ $isRequired('email') ? 'required' : '' }}>
							@error('email')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('phone'))
						<div class="col-md-6">
							<label for="phone" class="form-label">Phone @if($isRequired('phone'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}" {{ $isRequired('phone') ? 'required' : '' }}>
							@error('phone')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('emergency_contact'))
						<div class="col-md-6">
							<label for="emergency_contact" class="form-label">Emergency Contact @if($isRequired('emergency_contact'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $teacher->emergency_contact) }}" {{ $isRequired('emergency_contact') ? 'required' : '' }}>
							@error('emergency_contact')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('current_address'))
						<div class="col-12">
							<label for="current_address" class="form-label">Current Address @if($isRequired('current_address'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('current_address') is-invalid @enderror" id="current_address" name="current_address" rows="2" {{ $isRequired('current_address') ? 'required' : '' }}>{{ old('current_address', $teacher->current_address) }}</textarea>
							@error('current_address')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('permanent_address'))
						<div class="col-12">
							<label for="permanent_address" class="form-label">Permanent Address @if($isRequired('permanent_address'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('permanent_address') is-invalid @enderror" id="permanent_address" name="permanent_address" rows="2" {{ $isRequired('permanent_address') ? 'required' : '' }}>{{ old('permanent_address', $teacher->permanent_address) }}</textarea>
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
						@if($isVisible('subject_id'))
						<div class="col-md-6">
							<label for="subject_id" class="form-label">Subject @if($isRequired('subject_id'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" {{ $isRequired('subject_id') ? 'required' : '' }}>
								<option value="">Select Subject</option>
								@foreach($subjects as $subject)
									<option value="{{ $subject->id }}" {{ old('subject_id', $teacher->subject_id) == $subject->id ? 'selected' : '' }}>
										{{ $subject->name }}
									</option>
								@endforeach
							</select>
							@error('subject_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('designation_id'))
						<div class="col-md-6">
							<label for="designation_id" class="form-label">Designation @if($isRequired('designation_id'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('designation_id') is-invalid @enderror" id="designation_id" name="designation_id" {{ $isRequired('designation_id') ? 'required' : '' }}>
								<option value="">Select Designation</option>
								@foreach($designations as $designation)
									<option value="{{ $designation->id }}" {{ old('designation_id', $teacher->designation_id) == $designation->id ? 'selected' : '' }}>
										{{ $designation->name }}
									</option>
								@endforeach
							</select>
							@error('designation_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('joining_date'))
						<div class="col-md-4">
							<label for="joining_date" class="form-label">Joining Date @if($isRequired('joining_date'))<span class="text-danger">*</span>@endif</label>
							<input type="text" class="form-control datepicker @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date', $teacher->joining_date?->format('d-m-Y')) }}" placeholder="DD-MM-YYYY" {{ $isRequired('joining_date') ? 'required' : '' }}>
							@error('joining_date')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('contract_type'))
						<div class="col-md-4">
							<label for="contract_type" class="form-label">Contract Type @if($isRequired('contract_type'))<span class="text-danger">*</span>@endif</label>
							<select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type" {{ $isRequired('contract_type') ? 'required' : '' }}>
								<option value="permanent" {{ old('contract_type', $teacher->contract_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
								<option value="temporary" {{ old('contract_type', $teacher->contract_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
								<option value="contractual" {{ old('contract_type', $teacher->contract_type) == 'contractual' ? 'selected' : '' }}>Contractual</option>
							</select>
							@error('contract_type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('basic_salary'))
						<div class="col-md-4">
							<label for="basic_salary" class="form-label">Basic Salary @if($isRequired('basic_salary'))<span class="text-danger">*</span>@endif</label>
							<input type="number" class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', $teacher->basic_salary) }}" step="0.01" min="0" {{ $isRequired('basic_salary') ? 'required' : '' }}>
							@error('basic_salary')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						<div class="col-md-4">
							<label for="status" class="form-label">Status <span class="text-danger">*</span></label>
							<select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
								<option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Active</option>
								<option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
								<option value="resigned" {{ old('status', $teacher->status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
								<option value="terminated" {{ old('status', $teacher->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
							</select>
							@error('status')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>
			</div>

			<!-- Aadhaar & PAN Card Details -->
			@include('admin.partials._aadhaar-pan-fields', ['model' => $teacher, 'context' => 'edit'])

			<!-- Qualifications -->
			<div class="card">
				<div class="card-header">
					<h5>Qualifications</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						@if($isVisible('qualification'))
						<div class="col-12">
							<label for="qualification" class="form-label">Qualifications @if($isRequired('qualification'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" rows="2" placeholder="e.g., B.Ed, M.Sc Mathematics" {{ $isRequired('qualification') ? 'required' : '' }}>{{ old('qualification', $teacher->qualification) }}</textarea>
							@error('qualification')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
						@if($isVisible('experience'))
						<div class="col-12">
							<label for="experience" class="form-label">Experience @if($isRequired('experience'))<span class="text-danger">*</span>@endif</label>
							<textarea class="form-control @error('experience') is-invalid @enderror" id="experience" name="experience" rows="2" placeholder="Previous teaching experience" {{ $isRequired('experience') ? 'required' : '' }}>{{ old('experience', $teacher->experience) }}</textarea>
							@error('experience')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Custom Fields -->
			@include('admin.custom-fields._form-fields', [
				'customFields' => $customFields ?? [],
				'customFieldValues' => $customFieldValues ?? [],
				'formContext' => 'edit'
			])
		</div>

		<div class="col-12 col-lg-4">
			<!-- Teacher ID -->
			<div class="card">
				<div class="card-header">
					<h5>Teacher ID</h5>
				</div>
				<div class="card-body text-center">
					<h3 class="text-primary mb-0">{{ $teacher->staff_id }}</h3>
					<small class="text-muted">Joined: {{ $teacher->joining_date?->format('d M, Y') }}</small>
				</div>
			</div>

			<!-- Photo Upload -->
			@if($isVisible('photo'))
			<div class="card">
				<div class="card-header">
					<h5>Photo</h5>
				</div>
				<div class="card-body text-center">
					<div class="mb-3">
						<img id="photoPreview" src="{{ $teacher->photo_url }}" alt="Photo Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
					</div>
					<input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
					<small class="text-muted">Max size: 2MB. Formats: JPG, PNG</small>
					@error('photo')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>
			@endif

			<!-- Signature Upload -->
			<div class="card">
				<div class="card-header">
					<h5>Signature</h5>
				</div>
				<div class="card-body text-center">
					@if($teacher->signature_image)
						<div class="mb-2">
							<img src="{{ asset('storage/' . $teacher->signature_image) }}" alt="Signature" style="max-height: 60px; border: 1px solid #dee2e6; padding: 5px; border-radius: 4px;">
						</div>
						<div class="form-check d-inline-block mb-2">
							<input class="form-check-input" type="checkbox" name="remove_signature" value="1" id="remove_signature">
							<label class="form-check-label text-danger" for="remove_signature">Remove signature</label>
						</div>
					@else
						<div class="border rounded p-3 bg-light mb-2">
							<i class="icon-pencil text-muted" style="font-size: 24px;"></i>
							<p class="text-muted mb-0" style="font-size: 12px;">No signature uploaded</p>
						</div>
					@endif
					<input type="file" class="form-control @error('signature_image') is-invalid @enderror" name="signature_image" accept="image/*">
					<small class="text-muted">Transparent PNG, 300x100px recommended. Used on certificates.</small>
					@error('signature_image')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>

			<!-- Actions -->
			<div class="card">
				<div class="card-body">
					<div class="d-grid gap-2">
						<button type="submit" class="btn btn-primary">
							<i data-feather="save" class="me-1"></i> Update Teacher
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
