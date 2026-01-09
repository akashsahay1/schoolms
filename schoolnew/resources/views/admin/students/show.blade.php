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

				<span class="badge badge-light-{{ $student->status == 'active' ? 'success' : ($student->status == 'inactive' ? 'secondary' : 'warning') }} fs-6 mb-3">
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
					<a href="{{ route('admin.students.id-card', $student) }}" class="btn btn-success">
						<i data-feather="credit-card" class="me-1"></i> Print ID Card
					</a>
					<a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary">
						<i data-feather="edit" class="me-1"></i> Edit Student
					</a>
					<a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
						<i data-feather="arrow-left" class="me-1"></i> Back to List
					</a>
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
					<div class="mb-3">
						<label class="text-muted small">Login Email</label>
						<div class="input-group">
							<input type="text" class="form-control" id="loginEmail" value="{{ $student->user->email }}" readonly>
							<button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('loginEmail')">
								<i data-feather="copy"></i>
							</button>
						</div>
					</div>
					<div class="mb-3">
						<label class="text-muted small">Password</label>
						<div class="input-group">
							<input type="text" class="form-control" id="loginPassword" value="{{ $student->admission_no }}" readonly>
							<button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('loginPassword')">
								<i data-feather="copy"></i>
							</button>
						</div>
						<small class="text-muted">Default password is Admission Number</small>
					</div>
					<button class="btn btn-primary w-100" onclick="copyAllCredentials()">
						<i data-feather="clipboard" class="me-2"></i>Copy All Credentials
					</button>
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
								<td>{{ $student->email ?? 'N/A' }}</td>
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
								<td>{{ $student->parent->father_email ?? 'N/A' }}</td>
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
								<td>{{ $student->parent->mother_email ?? 'N/A' }}</td>
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
	</div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(elementId) {
	var input = document.getElementById(elementId);
	input.select();
	input.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(input.value);

	Swal.fire({
		icon: 'success',
		title: 'Copied!',
		text: 'Copied to clipboard',
		timer: 1500,
		showConfirmButton: false
	});
}

function copyAllCredentials() {
	var email = document.getElementById('loginEmail').value;
	var password = document.getElementById('loginPassword').value;
	var text = "Student Portal Login Credentials\n";
	text += "================================\n";
	text += "Email: " + email + "\n";
	text += "Password: " + password + "\n";
	text += "URL: {{ url('/login') }}";

	navigator.clipboard.writeText(text);

	Swal.fire({
		icon: 'success',
		title: 'Copied!',
		text: 'All credentials copied to clipboard',
		timer: 1500,
		showConfirmButton: false
	});
}
</script>
@endpush
