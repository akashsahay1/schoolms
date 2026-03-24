@extends('layouts.app')

@section('title', 'Student Details')

@section('page-title', 'Student Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
	<li class="breadcrumb-item active">{{ $student->admission_no }}</li>
@endsection

@section('content')
<div class="row">
	<!-- Student Profile Card -->
	<div class="col-lg-4">
		<div class="card">
			<div class="card-body text-center">
				@if($student->photo)
					<img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
				@else
					<div class="rounded-circle bg-{{ $student->gender == 'male' ? 'primary' : 'danger' }} mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width: 150px; height: 150px; font-size: 60px;">
						{{ strtoupper(substr($student->first_name, 0, 1)) }}
					</div>
				@endif

				<h4 class="mb-1">{{ $student->full_name }}</h4>
				<p class="text-muted mb-2">{{ $student->admission_no }}</p>

				@php
					$statusColor = match($student->status) {
						'active' => 'success',
						'graduated' => 'success',
						'transferred' => 'warning',
						'expelled' => 'danger',
						default => 'secondary',
					};
				@endphp
				<span class="badge badge-light-{{ $statusColor }} fs-6 mb-3">
					{{ ucfirst($student->status) }}
				</span>

				<div class="d-flex justify-content-center gap-2 mb-3">
					<span class="badge badge-light-{{ $student->gender == 'male' ? 'primary' : 'danger' }}">{{ ucfirst($student->gender) }}</span>
					@if($student->blood_group)
						<span class="badge badge-light-info">{{ $student->blood_group }}</span>
					@endif
				</div>

				<hr>

				<div class="d-grid gap-2">
					@if($isAlumni ?? false)
						@if($student->status === 'graduated')
							<a href="{{ route('admin.certificates.marksheet', $student) }}" class="btn btn-success">
								<i class="icon-download me-1"></i> Download Marksheet
							</a>
						@endif
						@if($student->status === 'transferred')
							<a href="{{ route('admin.certificates.tc', $student) }}" class="btn btn-primary">
								<i class="icon-download me-1"></i> Download TC
							</a>
						@endif
						<a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary">
							<i class="icon-pencil me-1"></i> Edit Student
						</a>
						<a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-secondary">
							<i class="icon-arrow-left me-1"></i> Back to Alumni
						</a>
					@else
						<a href="{{ route('admin.students.id-card', $student) }}" class="btn btn-success">
							<i class="icon-credit-card me-1"></i> Print ID Card
						</a>
						<a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary">
							<i class="icon-pencil me-1"></i> Edit Student
						</a>
						<a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
							<i class="icon-arrow-left me-1"></i> Back to List
						</a>
					@endif
				</div>
			</div>
		</div>

		<!-- Quick Info -->
		<div class="card">
			<div class="card-header">
				<h5>Quick Info</h5>
			</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					<li class="list-group-item d-flex justify-content-between">
						<span>Class</span>
						<strong>{{ $student->schoolClass->name ?? 'N/A' }}</strong>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span>Section</span>
						<strong>{{ $student->section->name ?? 'N/A' }}</strong>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span>Roll No</span>
						<strong>{{ $student->roll_no ?? 'N/A' }}</strong>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span>Academic Year</span>
						<strong>{{ $student->academicYear->name ?? 'N/A' }}</strong>
					</li>
					<li class="list-group-item d-flex justify-content-between">
						<span>Admission Date</span>
						<strong>{{ $student->admission_date?->format('M d, Y') ?? 'N/A' }}</strong>
					</li>
				</ul>
			</div>
		</div>

		<!-- Login Credentials -->
		<div class="card border-primary">
			<div class="card-header bg-primary">
				<h5 class="text-white mb-0"><i data-feather="key" class="me-2"></i>Portal Login Credentials</h5>
			</div>
			<div class="card-body">
				@if($student->user)
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
							<input type="text" class="form-control" id="loginEmail" value="{{ $student->user->email }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="loginEmail">
								<i data-feather="copy"></i>
							</button>
							<button class="btn btn-outline-warning" type="button" id="editEmailBtn" title="Change Email">
								<i data-feather="edit-2"></i>
							</button>
						</div>
						<form action="{{ route('admin.students.update-email', $student) }}" method="POST" id="editEmailForm" class="d-none mt-2">
							@csrf
							<div class="input-group">
								<input type="email" class="form-control @error('new_email') is-invalid @enderror" name="new_email" placeholder="Enter new login email" required value="{{ old('new_email') }}">
								<button type="submit" class="btn btn-success">Save</button>
								<button type="button" class="btn btn-outline-secondary" id="cancelEmailEdit">Cancel</button>
							</div>
							@error('new_email')
								<div class="text-danger small mt-1">{{ $message }}</div>
							@enderror
						</form>
					</div>
					<div class="mb-3">
						<label class="text-muted small">Current Password</label>
						<div class="input-group">
							<input type="text" class="form-control" id="currentPassword" value="{{ $student->user->plain_password ?? 'N/A' }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="currentPassword">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<hr>
					<h6 class="mb-3"><i data-feather="refresh-cw" style="width: 14px; height: 14px;" class="me-1"></i> Reset Password</h6>
					<form action="{{ route('admin.students.reset-password', $student) }}" method="POST">
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
						<small>Student cannot access the portal</small>
					</div>
				@endif
			</div>
		</div>
	</div>

	<!-- Details -->
	<div class="col-lg-8">
		<!-- Personal Information -->
		<div class="card">
			<div class="card-header">
				<h5>Personal Information</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" width="40%">Full Name</td>
								<td><strong>{{ $student->full_name }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Date of Birth</td>
								<td>{{ $student->date_of_birth?->format('M d, Y') ?? 'N/A' }} <small class="text-muted">({{ $student->age }} years)</small></td>
							</tr>
							<tr>
								<td class="text-muted">Gender</td>
								<td>{{ ucfirst($student->gender) }}</td>
							</tr>
							<tr>
								<td class="text-muted">Blood Group</td>
								<td>{{ $student->blood_group ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" width="40%">Religion</td>
								<td>{{ $student->religion ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Nationality</td>
								<td>{{ $student->nationality ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Mother Tongue</td>
								<td>{{ $student->mother_tongue ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Previous School</td>
								<td>{{ $student->previous_school ?? 'N/A' }}</td>
							</tr>
						</table>
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
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" width="40%">Email</td>
								<td style="word-break: break-all;">{{ $student->email ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Phone</td>
								<td>{{ $student->phone ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" width="40%">Current Address</td>
								<td>{{ $student->current_address ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Permanent Address</td>
								<td>{{ $student->permanent_address ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Aadhaar Card Details -->
		@if($student->aadhaar_number || $student->aadhaar_front || $student->aadhaar_back)
		<div class="card">
			<div class="card-header">
				<h5>Aadhaar Card Details</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted">Aadhaar Number</td>
								<td><strong>{{ $student->aadhaar_number ?? 'N/A' }}</strong></td>
							</tr>
						</table>
					</div>
					<div class="col-md-4">
						@if($student->aadhaar_front)
							<p class="text-muted mb-1">Aadhaar Front</p>
							@if(in_array(pathinfo($student->aadhaar_front, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
								<a href="{{ asset('storage/' . $student->aadhaar_front) }}" target="_blank">
									<img src="{{ asset('storage/' . $student->aadhaar_front) }}" alt="Aadhaar Front" class="img-thumbnail" style="max-height: 120px;">
								</a>
							@else
								<a href="{{ asset('storage/' . $student->aadhaar_front) }}" target="_blank" class="btn btn-outline-primary btn-sm">
									<i data-feather="file-text" class="me-1"></i> View PDF
								</a>
							@endif
						@else
							<p class="text-muted mb-1">Aadhaar Front</p>
							<span class="text-muted">Not uploaded</span>
						@endif
					</div>
					<div class="col-md-4">
						@if($student->aadhaar_back)
							<p class="text-muted mb-1">Aadhaar Back</p>
							@if(in_array(pathinfo($student->aadhaar_back, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
								<a href="{{ asset('storage/' . $student->aadhaar_back) }}" target="_blank">
									<img src="{{ asset('storage/' . $student->aadhaar_back) }}" alt="Aadhaar Back" class="img-thumbnail" style="max-height: 120px;">
								</a>
							@else
								<a href="{{ asset('storage/' . $student->aadhaar_back) }}" target="_blank" class="btn btn-outline-primary btn-sm">
									<i data-feather="file-text" class="me-1"></i> View PDF
								</a>
							@endif
						@else
							<p class="text-muted mb-1">Aadhaar Back</p>
							<span class="text-muted">Not uploaded</span>
						@endif
					</div>
				</div>
			</div>
		</div>
		@endif

		<!-- Parent Information -->
		@if($student->parent)
		<div class="card">
			<div class="card-header">
				<h5>Parent/Guardian Information</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<h6 class="text-primary mb-3">Father's Details</h6>
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" width="40%">Name</td>
								<td><strong>{{ $student->parent->father_name }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Phone</td>
								<td>{{ $student->parent->father_phone ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Email</td>
								<td style="word-break: break-all;">{{ $student->parent->father_email ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Occupation</td>
								<td>{{ $student->parent->father_occupation ?? 'N/A' }}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<h6 class="text-danger mb-3">Mother's Details</h6>
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" width="40%">Name</td>
								<td><strong>{{ $student->parent->mother_name ?? 'N/A' }}</strong></td>
							</tr>
							<tr>
								<td class="text-muted">Phone</td>
								<td>{{ $student->parent->mother_phone ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Email</td>
								<td style="word-break: break-all;">{{ $student->parent->mother_email ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Occupation</td>
								<td>{{ $student->parent->mother_occupation ?? 'N/A' }}</td>
							</tr>
						</table>
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

		<!-- Alumni: Leaving Information -->
		@if($isAlumni ?? false)
		<div class="card border-{{ $student->status === 'graduated' ? 'success' : ($student->status === 'transferred' ? 'warning' : 'danger') }}">
			<div class="card-header bg-{{ $student->status === 'graduated' ? 'success' : ($student->status === 'transferred' ? 'warning' : 'danger') }}">
				<h5 class="text-white mb-0">
					@if($student->status === 'graduated')
						Graduation Details
					@elseif($student->status === 'transferred')
						Transfer Details
					@else
						{{ ucfirst($student->status) }} Details
					@endif
				</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<span class="text-muted d-block" style="font-size: 12px;">Status</span>
						<span class="badge badge-light-{{ $student->status === 'graduated' ? 'success' : ($student->status === 'transferred' ? 'warning' : 'danger') }} fs-6">{{ ucfirst($student->status) }}</span>
					</div>
					@if($student->leaving_date)
					<div class="col-md-4">
						<span class="text-muted d-block" style="font-size: 12px;">Leaving Date</span>
						<strong>{{ $student->leaving_date->format('d M Y') }}</strong>
					</div>
					@endif
					@if($student->academicYear)
					<div class="col-md-4">
						<span class="text-muted d-block" style="font-size: 12px;">Academic Session</span>
						<strong>{{ $student->academicYear->name }}</strong>
					</div>
					@endif
				</div>
				@if($student->leaving_reason)
				<div class="mt-3">
					<span class="text-muted d-block" style="font-size: 12px;">Reason</span>
					<p class="mb-0">{{ $student->leaving_reason }}</p>
				</div>
				@endif
			</div>
		</div>
		@endif

		<!-- Academic History (Promotion History) -->
		@if(isset($promotionHistory) && $promotionHistory->count() > 0)
		<div class="card">
			<div class="card-header">
				<h5>Academic History</h5>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="bg-light">
							<tr>
								<th>Session</th>
								<th>From Class</th>
								<th>To Class</th>
								<th class="text-center">Status</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
							@foreach($promotionHistory as $promotion)
								<tr>
									<td>
										<span class="badge badge-light-info px-2">{{ $promotion->fromAcademicYear->name ?? '-' }}</span>
										→
										<span class="badge badge-light-primary px-2">{{ $promotion->toAcademicYear->name ?? '-' }}</span>
									</td>
									<td>{{ $promotion->fromClass->name ?? '-' }}</td>
									<td>{{ $promotion->toClass->name ?? '-' }}</td>
									<td class="text-center">
										@if($promotion->status === 'promoted')
											<span class="badge badge-light-success px-2">Promoted</span>
										@elseif($promotion->status === 'retained')
											<span class="badge badge-light-warning px-2">Retained</span>
										@elseif($promotion->status === 'alumni')
											<span class="badge badge-light-info px-2">Alumni</span>
										@else
											<span class="badge badge-light-secondary px-2">{{ ucfirst($promotion->status) }}</span>
										@endif
									</td>
									<td>{{ $promotion->promoted_at ? \Carbon\Carbon::parse($promotion->promoted_at)->format('d M Y') : '-' }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
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

	jQuery('#editEmailBtn').click(function() {
		jQuery('#editEmailForm').removeClass('d-none');
		jQuery(this).addClass('d-none');
	});
	jQuery('#cancelEmailEdit').click(function() {
		jQuery('#editEmailForm').addClass('d-none');
		jQuery('#editEmailBtn').removeClass('d-none');
	});
	@error('new_email')
		jQuery('#editEmailForm').removeClass('d-none');
		jQuery('#editEmailBtn').addClass('d-none');
	@enderror
});
</script>
@endpush
