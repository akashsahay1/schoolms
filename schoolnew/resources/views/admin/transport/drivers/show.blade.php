@extends('layouts.app')

@section('title', 'View Driver')

@section('page-title', 'Transport - View Driver')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.drivers.index') }}">Drivers</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($driver->photo)
                    <img src="{{ asset('storage/' . $driver->photo) }}" alt="{{ $driver->full_name }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                        <i data-feather="user" class="text-muted" style="width: 60px; height: 60px;"></i>
                    </div>
                @endif
                <h4 class="mb-1">{{ $driver->full_name }}</h4>
                <span class="badge badge-light-primary mb-2">{{ $driver->employee_id }}</span>
                <div class="mb-3">
                    @if($driver->is_active)
                        <span class="badge badge-light-success">Active</span>
                    @else
                        <span class="badge badge-light-danger">Inactive</span>
                    @endif
                    <span class="badge {{ $driver->getLicenseStatusBadgeClass() }}">{{ $driver->getLicenseStatusLabel() }}</span>
                </div>
                <p class="text-muted mb-0">
                    <i data-feather="phone" class="me-1" style="width: 14px;"></i> {{ $driver->phone }}
                </p>
                @if($driver->email)
                    <p class="text-muted mb-0">
                        <i data-feather="mail" class="me-1" style="width: 14px;"></i> {{ $driver->email }}
                    </p>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-primary flex-fill">
                        <i data-feather="edit" class="me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary flex-fill">
                        <i data-feather="arrow-left" class="me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Documents Card -->
        <div class="card">
            <div class="card-header">
                <h5>Documents</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i data-feather="credit-card" class="me-2" style="width: 16px;"></i> License</span>
                        @if($driver->license_document)
                            <a href="{{ asset('storage/' . $driver->license_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i data-feather="file-text" class="me-2" style="width: 16px;"></i> ID Proof</span>
                        @if($driver->id_proof_document)
                            <a href="{{ asset('storage/' . $driver->id_proof_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Date of Birth</label>
                        <p class="mb-0">{{ $driver->date_of_birth ? $driver->date_of_birth->format('M d, Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Gender</label>
                        <p class="mb-0">{{ ucfirst($driver->gender) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Blood Group</label>
                        <p class="mb-0">{{ $driver->blood_group ?: '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Alternate Phone</label>
                        <p class="mb-0">{{ $driver->alternate_phone ?: '-' }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Address</label>
                        <p class="mb-0">{{ $driver->address ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>License & Employment</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">License Number</label>
                        <p class="mb-0">{{ $driver->license_number }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">License Type</label>
                        <p class="mb-0">{{ $driver->license_type ?: '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">License Expiry</label>
                        <p class="mb-0">
                            {{ $driver->license_expiry->format('M d, Y') }}
                            @if($driver->isLicenseExpired())
                                <span class="badge badge-light-danger ms-2">Expired</span>
                            @elseif($driver->isLicenseExpiringSoon())
                                <span class="badge badge-light-warning ms-2">Expiring Soon</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Joining Date</label>
                        <p class="mb-0">{{ $driver->joining_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Salary</label>
                        <p class="mb-0">₹{{ number_format($driver->salary, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Emergency Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-muted small">Contact Name</label>
                        <p class="mb-0">{{ $driver->emergency_contact_name ?: '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Contact Phone</label>
                        <p class="mb-0">{{ $driver->emergency_contact_phone ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Assigned Vehicles</h5>
            </div>
            <div class="card-body">
                @if($driver->vehicles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Vehicle No</th>
                                    <th>Model</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($driver->vehicles as $vehicle)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.vehicles.show', $vehicle) }}">{{ $vehicle->vehicle_no }}</a>
                                        </td>
                                        <td>{{ $vehicle->vehicle_model }}</td>
                                        <td>
                                            @if($vehicle->routes->count() > 0)
                                                @foreach($vehicle->routes as $route)
                                                    <span class="badge badge-light-info">{{ $route->title }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No routes</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vehicle->status === 'active')
                                                <span class="badge badge-light-success">Active</span>
                                            @elseif($vehicle->status === 'inactive')
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @else
                                                <span class="badge badge-light-warning">{{ ucfirst($vehicle->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">No vehicles assigned to this driver.</p>
                @endif
            </div>
        </div>

        @if($driver->notes)
            <div class="card">
                <div class="card-header">
                    <h5>Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $driver->notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
