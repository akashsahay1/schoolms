@extends('layouts.app')

@section('title', $staff->full_name . ' - Staff Details')

@section('page-title', 'Staff Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
	<li class="breadcrumb-item active">{{ $staff->full_name }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-4">
		<!-- Profile Card -->
		<div class="card">
			<div class="card-body text-center">
				<div class="mb-3">
					<img src="{{ $staff->photo_url }}" alt="{{ $staff->full_name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
				</div>
				<h4 class="mb-1">{{ $staff->full_name }}</h4>
				<p class="text-muted mb-2">{{ $staff->designation->name ?? 'N/A' }}</p>
				<span class="badge badge-light-{{ $staff->status == 'active' ? 'success' : ($staff->status == 'inactive' ? 'secondary' : ($staff->status == 'resigned' ? 'warning' : 'danger')) }} mb-3">
					{{ ucfirst($staff->status) }}
				</span>
				<div class="border-top pt-3">
					<div class="row text-center">
						<div class="col-4">
							<h5 class="text-primary mb-0">{{ $staff->experience_years }}</h5>
							<small class="text-muted">Years</small>
						</div>
						<div class="col-4">
							<h5 class="text-info mb-0">{{ $staff->department->name ?? 'N/A' }}</h5>
							<small class="text-muted">Dept.</small>
						</div>
						<div class="col-4">
							<h5 class="text-success mb-0">{{ ucfirst($staff->contract_type) }}</h5>
							<small class="text-muted">Type</small>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Contact Info -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Contact Information</h6>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					<li class="d-flex align-items-center mb-3">
						<i data-feather="mail" class="text-primary me-3 flex-shrink-0" style="width: 18px;"></i>
						<span style="word-break: break-all; min-width: 0;">{{ $staff->email }}</span>
					</li>
					<li class="d-flex align-items-center mb-3">
						<i data-feather="phone" class="text-primary me-3" style="width: 18px;"></i>
						<span>{{ $staff->phone }}</span>
					</li>
					@if($staff->emergency_contact)
						<li class="d-flex align-items-center mb-3">
							<i data-feather="phone-call" class="text-danger me-3" style="width: 18px;"></i>
							<span>{{ $staff->emergency_contact }} (Emergency)</span>
						</li>
					@endif
					@if($staff->current_address)
						<li class="d-flex align-items-start">
							<i data-feather="map-pin" class="text-primary me-3 mt-1 flex-shrink-0" style="width: 18px;"></i>
							<span style="min-width: 0; overflow-wrap: break-word;">{{ $staff->current_address }}</span>
						</li>
					@endif
				</ul>
			</div>
		</div>

		<!-- Portal Login Credentials -->
		<div class="card border-primary">
			<div class="card-header bg-primary">
				<h5 class="text-white mb-0"><i data-feather="key" class="me-2"></i>Portal Login Credentials</h5>
			</div>
			<div class="card-body">
				@if($staff->user)
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
							<input type="text" class="form-control" id="loginEmail" value="{{ $staff->user->email }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="loginEmail">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<div class="mb-3">
						<label class="text-muted small">Current Password</label>
						<div class="input-group">
							<input type="text" class="form-control" id="currentPassword" value="{{ $staff->user->plain_password ?? 'N/A' }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="currentPassword">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<hr>
					@if(!$staff->user->hasRole('Super Admin') || auth()->user()->hasRole('Super Admin'))
						<h6 class="mb-3"><i data-feather="refresh-cw" style="width: 14px; height: 14px;" class="me-1"></i> Reset Password</h6>
						<form action="{{ route('admin.staff.reset-password', $staff) }}" method="POST">
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
						<div class="text-center text-muted py-2">
							<i data-feather="shield" class="mb-2" style="width: 30px; height: 30px;"></i>
							<p class="mb-0 small">Super Admin account - password changes restricted</p>
						</div>
					@endif
				@else
					<div class="text-center text-muted py-3">
						<i data-feather="alert-circle" class="mb-2" style="width: 40px; height: 40px;"></i>
						<p class="mb-0">No login account linked</p>
						<small>Staff member cannot access the portal</small>
					</div>
				@endif
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-body">
				<div class="d-grid gap-2">
					@if(!($staff->user && $staff->user->hasRole('Super Admin')) || auth()->user()->hasRole('Super Admin'))
						<a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary">
							<i data-feather="edit" class="me-1"></i> Edit Staff
						</a>
					@endif
					<a href="{{ route('admin.staff.id-card', $staff) }}" class="btn btn-info">
						<i data-feather="credit-card" class="me-1"></i> Print ID Card
					</a>
					<a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to List
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-8">
		<!-- Basic Information -->
		<div class="card">
			<div class="card-header">
				<h5>Basic Information</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Staff ID</td>
								<td><strong>{{ $staff->staff_id }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Full Name</td>
								<td>{{ $staff->full_name }}</td>
							</tr>
							<tr>
								<td class="text-muted">Gender</td>
								<td>{{ ucfirst($staff->gender) }}</td>
							</tr>
							<tr>
								<td class="text-muted">Date of Birth</td>
								<td>{{ $staff->date_of_birth?->format('d M Y') }} ({{ $staff->age }} years)</td>
							</tr>
							<tr>
								<td class="text-muted">Blood Group</td>
								<td>{{ $staff->blood_group ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Religion</td>
								<td>{{ $staff->religion ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Marital Status</td>
								<td>{{ ucfirst($staff->marital_status ?? 'N/A') }}</td>
							</tr>
							<tr>
								<td class="text-muted">Nationality</td>
								<td>{{ $staff->nationality ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">National ID</td>
								<td>{{ $staff->national_id ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Job Details -->
		<div class="card">
			<div class="card-header">
				<h5>Job Details</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Role</td>
								<td>{{ $staff->designation->name ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Joining Date</td>
								<td>{{ $staff->joining_date?->format('d M Y') }}</td>
							</tr>
							<tr>
								<td class="text-muted">Contract Type</td>
								<td>{{ ucfirst($staff->contract_type) }}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Experience</td>
								<td>{{ $staff->experience_years }} years in this school</td>
							</tr>
							<tr>
								<td class="text-muted">Status</td>
								<td>
									<span class="badge badge-light-{{ $staff->status == 'active' ? 'success' : ($staff->status == 'inactive' ? 'secondary' : ($staff->status == 'resigned' ? 'warning' : 'danger')) }}">
										{{ ucfirst($staff->status) }}
									</span>
								</td>
							</tr>
							@if($staff->leaving_date)
								<tr>
									<td class="text-muted">Leaving Date</td>
									<td>{{ $staff->leaving_date?->format('d M Y') }}</td>
								</tr>
							@endif
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Address Information -->
		@if($staff->current_address || $staff->permanent_address)
			<div class="card">
				<div class="card-header">
					<h5>Address Information</h5>
				</div>
				<div class="card-body">
					<div class="row">
						@if($staff->current_address)
							<div class="col-md-6">
								<h6 class="text-muted">Current Address</h6>
								<p>{{ $staff->current_address }}</p>
							</div>
						@endif
						@if($staff->permanent_address)
							<div class="col-md-6">
								<h6 class="text-muted">Permanent Address</h6>
								<p class="mb-0">{{ $staff->permanent_address }}</p>
							</div>
						@endif
					</div>
				</div>
			</div>
		@endif

		<!-- Documents -->
		@if($staff->aadhaar_number || $staff->aadhaar_front || $staff->aadhaar_back)
		<div class="card">
			<div class="card-header">
				<h5>Documents</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Aadhaar Number</td>
								<td><strong>{{ $staff->aadhaar_number ?? 'N/A' }}</strong></td>
							</tr>
						</table>
					</div>
					<div class="col-md-3">
						@if($staff->aadhaar_front)
							<p class="text-muted mb-1">Aadhaar Front</p>
							@if(in_array(pathinfo($staff->aadhaar_front, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
								<a href="{{ asset('storage/' . $staff->aadhaar_front) }}" target="_blank">
									<img src="{{ asset('storage/' . $staff->aadhaar_front) }}" alt="Aadhaar Front" class="img-thumbnail" style="max-height: 120px;">
								</a>
							@else
								<a href="{{ asset('storage/' . $staff->aadhaar_front) }}" target="_blank" class="btn btn-outline-primary btn-sm">
									<i data-feather="file-text" class="me-1"></i> View PDF
								</a>
							@endif
						@endif
					</div>
					<div class="col-md-3">
						@if($staff->aadhaar_back)
							<p class="text-muted mb-1">Aadhaar Back</p>
							@if(in_array(pathinfo($staff->aadhaar_back, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
								<a href="{{ asset('storage/' . $staff->aadhaar_back) }}" target="_blank">
									<img src="{{ asset('storage/' . $staff->aadhaar_back) }}" alt="Aadhaar Back" class="img-thumbnail" style="max-height: 120px;">
								</a>
							@else
								<a href="{{ asset('storage/' . $staff->aadhaar_back) }}" target="_blank" class="btn btn-outline-primary btn-sm">
									<i data-feather="file-text" class="me-1"></i> View PDF
								</a>
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>
		@endif

		<!-- Custom Fields -->
		@if($customFields->count() > 0 && collect($customFieldValues)->filter()->count() > 0)
		<div class="card">
			<div class="card-header">
				<h5>Additional Information</h5>
			</div>
			<div class="card-body">
				<div class="row">
					@foreach($customFields as $field)
						@if(!empty($customFieldValues[$field->id]))
						<div class="col-md-6 mb-3">
							<span class="text-muted">{{ $field->name }}</span>
							<div>
								@if($field->field_type === 'checkbox')
									<span class="badge badge-light-{{ $customFieldValues[$field->id] ? 'success' : 'secondary' }}">
										{{ $customFieldValues[$field->id] ? 'Yes' : 'No' }}
									</span>
								@elseif($field->field_type === 'file')
									<a href="{{ asset('storage/' . $customFieldValues[$field->id]) }}" target="_blank" class="btn btn-outline-primary btn-sm">
										<i data-feather="file" class="me-1"></i> View File
									</a>
								@else
									<strong>{{ $customFieldValues[$field->id] }}</strong>
								@endif
							</div>
						</div>
						@endif
					@endforeach
				</div>
			</div>
		</div>
		@endif
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
