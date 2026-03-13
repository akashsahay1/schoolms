@extends('layouts.app')

@section('title', 'View Teacher')

@section('page-title', 'Teacher Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
	<li class="breadcrumb-item active">{{ $teacher->full_name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-4">
		<!-- Profile Card -->
		<div class="card">
			<div class="card-body text-center">
				<div class="mb-3">
					@if($teacher->photo)
						<img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
					@else
						<div class="rounded-circle bg-{{ $teacher->gender == 'male' ? 'primary' : 'danger' }} mx-auto d-flex align-items-center justify-content-center text-white" style="width: 150px; height: 150px; font-size: 48px;">
							{{ strtoupper(substr($teacher->first_name, 0, 1)) }}
						</div>
					@endif
				</div>
				<h4 class="mb-1">{{ $teacher->full_name }}</h4>
				<p class="text-muted mb-2">{{ $teacher->designation->name ?? 'Teacher' }}</p>
				<span class="badge badge-light-{{ $teacher->status == 'active' ? 'success' : ($teacher->status == 'inactive' ? 'secondary' : ($teacher->status == 'resigned' ? 'warning' : 'danger')) }} fs-6">
					{{ ucfirst($teacher->status) }}
				</span>
				<div class="mt-3">
					<h5 class="text-primary mb-0">{{ $teacher->staff_id }}</h5>
					<small class="text-muted">Teacher ID</small>
				</div>
			</div>
		</div>

		<!-- Quick Info -->
		<div class="card">
			<div class="card-header">
				<h5>Quick Info</h5>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					<li class="mb-2">
						<i data-feather="phone" class="me-2 text-muted"></i>
						<a href="tel:{{ $teacher->phone }}">{{ $teacher->phone }}</a>
					</li>
					<li class="mb-2" style="word-break: break-all;">
						<i data-feather="mail" class="me-2 text-muted"></i>
						<a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a>
					</li>
					<li class="mb-2">
						<i data-feather="briefcase" class="me-2 text-muted"></i>
						{{ $teacher->department->name ?? 'N/A' }}
					</li>
					<li class="mb-2">
						<i data-feather="calendar" class="me-2 text-muted"></i>
						Joined: {{ $teacher->joining_date?->format('d M, Y') }}
					</li>
					<li>
						<i data-feather="clock" class="me-2 text-muted"></i>
						{{ $teacher->experience_years }} years at school
					</li>
				</ul>
			</div>
		</div>

		<!-- Portal Login Credentials -->
		<div class="card border-primary">
			<div class="card-header bg-primary">
				<h5 class="text-white mb-0"><i data-feather="key" class="me-2"></i>Portal Login Credentials</h5>
			</div>
			<div class="card-body">
				@if($teacher->user)
					@if(session('success'))
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							{{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
					@endif
					@if(session('error'))
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							{{ session('error') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
						</div>
					@endif
					<div class="mb-3">
						<label class="text-muted small">Login Email</label>
						<div class="input-group">
							<input type="text" class="form-control" id="loginEmail" value="{{ $teacher->user->email }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="loginEmail">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<div class="mb-3">
						<label class="text-muted small">Current Password</label>
						<div class="input-group">
							<input type="text" class="form-control" id="currentPassword" value="{{ $teacher->user->plain_password ?? 'N/A' }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="currentPassword">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<hr>
					<h6 class="mb-3"><i data-feather="refresh-cw" style="width: 14px; height: 14px;" class="me-1"></i> Reset Password</h6>
					<form action="{{ route('admin.teachers.reset-password', $teacher) }}" method="POST">
						@csrf
						<div class="mb-3">
							<label class="text-muted small">New Password</label>
							<div class="input-group">
								<input type="text" class="form-control @error('new_password') is-invalid @enderror" name="new_password" id="newPassword" placeholder="Enter new password" required minlength="6">
								<button class="btn btn-outline-secondary" type="button" id="generatePassword">
									<i data-feather="zap"></i>
								</button>
							</div>
							@error('new_password')
								<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
							<small class="text-muted">Min 6 characters. Click <i data-feather="zap" style="width: 12px; height: 12px;"></i> to auto-generate.</small>
						</div>
						<button type="submit" class="btn btn-warning w-100">
							<i data-feather="lock" class="me-2"></i>Reset Password
						</button>
					</form>
				@else
					<div class="text-center text-muted py-3">
						<i data-feather="alert-circle" class="mb-2" style="width: 40px; height: 40px;"></i>
						<p class="mb-0">No login account linked</p>
						<small>Teacher cannot access the portal</small>
					</div>
				@endif
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-primary">
						<i data-feather="edit" class="me-1"></i> Edit Teacher
					</a>
					<a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to List
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-8">
		<!-- Personal Information -->
		<div class="card">
			<div class="card-header">
				<h5>Personal Information</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-md-6">
						<label class="text-muted small">Full Name</label>
						<p class="mb-0">{{ $teacher->full_name }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Gender</label>
						<p class="mb-0">{{ ucfirst($teacher->gender) }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Date of Birth</label>
						<p class="mb-0">{{ $teacher->date_of_birth?->format('d M, Y') }} ({{ $teacher->age }} years)</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Blood Group</label>
						<p class="mb-0">{{ $teacher->blood_group ?? 'N/A' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Religion</label>
						<p class="mb-0">{{ $teacher->religion ?? 'N/A' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Marital Status</label>
						<p class="mb-0">{{ ucfirst($teacher->marital_status ?? 'N/A') }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Nationality</label>
						<p class="mb-0">{{ $teacher->nationality ?? 'N/A' }}</p>
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
						<label class="text-muted small">Email</label>
						<p class="mb-0" style="word-break: break-all;"><a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a></p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Phone</label>
						<p class="mb-0"><a href="tel:{{ $teacher->phone }}">{{ $teacher->phone }}</a></p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Emergency Contact</label>
						<p class="mb-0">{{ $teacher->emergency_contact ?? 'N/A' }}</p>
					</div>
					<div class="col-12">
						<label class="text-muted small">Current Address</label>
						<p class="mb-0">{{ $teacher->current_address ?? 'N/A' }}</p>
					</div>
					<div class="col-12">
						<label class="text-muted small">Permanent Address</label>
						<p class="mb-0">{{ $teacher->permanent_address ?? 'N/A' }}</p>
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
						<label class="text-muted small">Department</label>
						<p class="mb-0">{{ $teacher->department->name ?? 'N/A' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Designation</label>
						<p class="mb-0">{{ $teacher->designation->name ?? 'N/A' }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Joining Date</label>
						<p class="mb-0">{{ $teacher->joining_date?->format('d M, Y') }}</p>
					</div>
					<div class="col-md-6">
						<label class="text-muted small">Contract Type</label>
						<p class="mb-0">{{ ucfirst($teacher->contract_type) }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Qualifications -->
		<div class="card">
			<div class="card-header">
				<h5>Qualifications & Experience</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-12">
						<label class="text-muted small">Qualifications</label>
						<p class="mb-0">{{ $teacher->qualification ?? 'N/A' }}</p>
					</div>
					<div class="col-12">
						<label class="text-muted small">Experience</label>
						<p class="mb-0">{{ $teacher->experience ?? 'N/A' }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('.copy-btn').click(function() {
		var targetId = jQuery(this).data('target');
		var input = document.getElementById(targetId);
		var text = input.value;

		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(function() {
				Swal.fire({ icon: 'success', title: 'Copied!', text: 'Copied to clipboard', timer: 1500, showConfirmButton: false });
			});
		} else {
			input.select();
			input.setSelectionRange(0, 99999);
			document.execCommand('copy');
			Swal.fire({ icon: 'success', title: 'Copied!', text: 'Copied to clipboard', timer: 1500, showConfirmButton: false });
		}
	});

	jQuery('#generatePassword').click(function() {
		var chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789@#$';
		var password = '';
		for (var i = 0; i < 10; i++) {
			password += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		jQuery('#newPassword').val(password);
	});
});
</script>
@endpush
