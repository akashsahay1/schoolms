@extends('layouts.app')

@section('title', 'View Parent')

@section('page-title', 'Parent Details')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.parents.index') }}">Parents</a></li>
	<li class="breadcrumb-item active">{{ $parent->father_name ?? $parent->mother_name ?? 'Parent' }}</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12 col-lg-4">
		<!-- Quick Actions -->
		<div class="card">
			<div class="card-header">
				<h5>Quick Info</h5>
			</div>
			<div class="card-body">
				<ul class="list-unstyled mb-0">
					@if($parent->primary_contact)
						<li class="mb-2">
							<i data-feather="phone" class="me-2 text-muted"></i>
							<a href="tel:{{ $parent->primary_contact }}">{{ $parent->primary_contact }}</a>
						</li>
					@endif
					@if($parent->primary_email)
						<li class="mb-2" style="word-break: break-all;">
							<i data-feather="mail" class="me-2 text-muted"></i>
							<a href="mailto:{{ $parent->primary_email }}">{{ $parent->primary_email }}</a>
						</li>
					@endif
					<li>
						<i data-feather="users" class="me-2 text-muted"></i>
						{{ $parent->students->count() }} student(s)
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
				@if($parent->user)
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
							<input type="text" class="form-control" id="loginEmail" value="{{ $parent->user->email }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="loginEmail">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<div class="mb-3">
						<label class="text-muted small">Current Password</label>
						<div class="input-group">
							<input type="text" class="form-control" id="currentPassword" value="{{ $parent->user->plain_password ?? 'N/A' }}" readonly>
							<button class="btn btn-outline-primary copy-btn" type="button" data-target="currentPassword">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<hr>
					<h6 class="mb-3"><i data-feather="refresh-cw" style="width: 14px; height: 14px;" class="me-1"></i> Reset Password</h6>
					<form action="{{ route('admin.parents.reset-password', $parent) }}" method="POST">
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
						<small>Parent cannot access the portal</small>
					</div>
				@endif
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="{{ route('admin.parents.index') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to List
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-8">
		<!-- Father's Information -->
		@if($parent->father_name)
			<div class="card">
				<div class="card-header">
					<h5><i data-feather="user" class="me-2"></i> Father's Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Name</label>
							<p class="mb-0">{{ $parent->father_name }}</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Phone</label>
							<p class="mb-0">
								@if($parent->father_phone)
									<a href="tel:{{ $parent->father_phone }}">{{ $parent->father_phone }}</a>
								@else
									N/A
								@endif
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Email</label>
							<p class="mb-0" style="word-break: break-all;">
								@if($parent->father_email)
									<a href="mailto:{{ $parent->father_email }}">{{ $parent->father_email }}</a>
								@else
									N/A
								@endif
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Occupation</label>
							<p class="mb-0">{{ $parent->father_occupation ?? 'N/A' }}</p>
						</div>
					</div>
				</div>
			</div>
		@endif

		<!-- Mother's Information -->
		@if($parent->mother_name)
			<div class="card">
				<div class="card-header">
					<h5><i data-feather="user" class="me-2"></i> Mother's Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Name</label>
							<p class="mb-0">{{ $parent->mother_name }}</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Phone</label>
							<p class="mb-0">
								@if($parent->mother_phone)
									<a href="tel:{{ $parent->mother_phone }}">{{ $parent->mother_phone }}</a>
								@else
									N/A
								@endif
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Email</label>
							<p class="mb-0" style="word-break: break-all;">
								@if($parent->mother_email)
									<a href="mailto:{{ $parent->mother_email }}">{{ $parent->mother_email }}</a>
								@else
									N/A
								@endif
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Occupation</label>
							<p class="mb-0">{{ $parent->mother_occupation ?? 'N/A' }}</p>
						</div>
					</div>
				</div>
			</div>
		@endif

		<!-- Guardian's Information -->
		@if($parent->guardian_name)
			<div class="card">
				<div class="card-header">
					<h5><i data-feather="user" class="me-2"></i> Guardian's Information</h5>
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Name</label>
							<p class="mb-0">{{ $parent->guardian_name }}</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Relation</label>
							<p class="mb-0">{{ $parent->guardian_relation ?? 'N/A' }}</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Phone</label>
							<p class="mb-0">
								@if($parent->guardian_phone)
									<a href="tel:{{ $parent->guardian_phone }}">{{ $parent->guardian_phone }}</a>
								@else
									N/A
								@endif
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Email</label>
							<p class="mb-0" style="word-break: break-all;">
								@if($parent->guardian_email)
									<a href="mailto:{{ $parent->guardian_email }}">{{ $parent->guardian_email }}</a>
								@else
									N/A
								@endif
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Occupation</label>
							<p class="mb-0">{{ $parent->guardian_occupation ?? 'N/A' }}</p>
						</div>
						<div class="col-12">
							<label class="text-muted small">Address</label>
							<p class="mb-0">{{ $parent->guardian_address ?? 'N/A' }}</p>
						</div>
					</div>
				</div>
			</div>
		@endif

		<!-- Address Information -->
		<div class="card">
			<div class="card-header">
				<h5><i data-feather="map-pin" class="me-2"></i> Address Information</h5>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<div class="col-12">
						<label class="text-muted small">Current Address</label>
						<p class="mb-0">{{ $parent->current_address ?? 'N/A' }}</p>
					</div>
					<div class="col-12">
						<label class="text-muted small">Permanent Address</label>
						<p class="mb-0">{{ $parent->permanent_address ?? 'N/A' }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Children / Students -->
		<div class="card">
			<div class="card-header">
				<h5><i data-feather="users" class="me-2"></i> Children</h5>
			</div>
			<div class="card-body">
				@if($parent->students->count() > 0)
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Admission No</th>
									<th>Name</th>
									<th>Class</th>
									<th>Section</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@foreach($parent->students as $student)
									<tr>
										<td><strong>{{ $student->admission_no }}</strong></td>
										<td>{{ $student->first_name }} {{ $student->last_name }}</td>
										<td>{{ $student->schoolClass->name ?? 'N/A' }}</td>
										<td>{{ $student->section->name ?? 'N/A' }}</td>
										<td>
											<a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">
												<i data-feather="eye" class="me-1"></i> View
											</a>
											<a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-secondary">
												<i data-feather="edit" class="me-1"></i> Edit
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@else
					<p class="text-muted mb-0">No students associated with this parent.</p>
				@endif
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
