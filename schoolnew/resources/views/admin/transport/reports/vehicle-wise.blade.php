@extends('layouts.app')

@section('title', 'Vehicle-wise Report')

@section('page-title', 'Transport - Vehicle-wise Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.reports.index') }}">Transport Reports</a></li>
    <li class="breadcrumb-item active">Vehicle-wise</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Vehicle-wise Student Report</h5>
                    @if($selectedVehicle && $students->count() > 0)
                        <a href="{{ route('admin.transport.reports.export-vehicle', ['vehicle_id' => $selectedVehicle, 'academic_year_id' => $selectedYear]) }}" class="btn btn-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.transport.reports.vehicle-wise') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ $selectedVehicle == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->vehicle_no }} - {{ $vehicle->vehicle_model }} ({{ $vehicle->max_seating_capacity }} seats)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search" class="me-1"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>

                @if($selectedVehicleData)
                    <!-- Vehicle Info -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <strong>Vehicle:</strong> {{ $selectedVehicleData->vehicle_no }}
                            </div>
                            <div class="col-md-2">
                                <strong>Model:</strong> {{ $selectedVehicleData->vehicle_model }}
                            </div>
                            <div class="col-md-2">
                                <strong>Capacity:</strong> {{ $selectedVehicleData->max_seating_capacity }}
                            </div>
                            <div class="col-md-2">
                                <strong>Assigned:</strong> {{ $students->count() }}
                            </div>
                            <div class="col-md-2">
                                <strong>Available:</strong> {{ max(0, $selectedVehicleData->max_seating_capacity - $students->count()) }}
                            </div>
                            <div class="col-md-2">
                                <strong>Utilization:</strong>
                                @php
                                    $utilization = $selectedVehicleData->max_seating_capacity > 0
                                        ? round(($students->count() / $selectedVehicleData->max_seating_capacity) * 100, 1)
                                        : 0;
                                @endphp
                                <span class="badge bg-{{ $utilization > 90 ? 'danger' : ($utilization > 70 ? 'warning' : 'success') }}">
                                    {{ $utilization }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($vehicleRoutes->count() > 0)
                        <div class="mb-3">
                            <h6>Routes assigned to this vehicle:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($vehicleRoutes as $route)
                                    <span class="badge badge-light-primary">
                                        {{ $route->route_name }} (₹{{ number_format($route->fare_amount, 2) }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Class/Section</th>
                                    <th>Route</th>
                                    <th>Pickup Point</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $assignment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assignment->student->admission_no ?? '-' }}</td>
                                        <td><strong>{{ $assignment->student->first_name ?? '' }} {{ $assignment->student->last_name ?? '' }}</strong></td>
                                        <td>
                                            {{ $assignment->student->schoolClass->name ?? '-' }}
                                            @if($assignment->student->section)
                                                / {{ $assignment->student->section->name }}
                                            @endif
                                        </td>
                                        <td>{{ $assignment->route->route_name ?? '-' }}</td>
                                        <td>{{ $assignment->pickup_point ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No students assigned to this vehicle.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i data-feather="truck" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                        <p class="text-muted">Please select a vehicle to generate the report.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
