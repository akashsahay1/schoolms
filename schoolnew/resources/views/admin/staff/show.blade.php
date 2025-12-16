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
						<i data-feather="mail" class="text-primary me-3" style="width: 18px;"></i>
						<span>{{ $staff->email }}</span>
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
							<i data-feather="map-pin" class="text-primary me-3 mt-1" style="width: 18px;"></i>
							<span>{{ $staff->current_address }}</span>
						</li>
					@endif
				</ul>
			</div>
		</div>

		<!-- Actions -->
		<div class="card">
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary">
						<i data-feather="edit" class="me-1"></i> Edit Staff
					</a>
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

		<!-- Employment Information -->
		<div class="card">
			<div class="card-header">
				<h5>Employment Information</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td class="text-muted" style="width: 40%;">Department</td>
								<td>{{ $staff->department->name ?? 'N/A' }}</td>
							</tr>
							<tr>
								<td class="text-muted">Designation</td>
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

		<!-- Qualifications -->
		@if($staff->qualification || $staff->experience)
			<div class="card">
				<div class="card-header">
					<h5>Qualifications & Experience</h5>
				</div>
				<div class="card-body">
					@if($staff->qualification)
						<div class="mb-3">
							<h6 class="text-muted">Qualifications</h6>
							<p>{{ $staff->qualification }}</p>
						</div>
					@endif
					@if($staff->experience)
						<div>
							<h6 class="text-muted">Experience</h6>
							<p class="mb-0">{{ $staff->experience }}</p>
						</div>
					@endif
				</div>
			</div>
		@endif

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
	</div>
</div>
@endsection
