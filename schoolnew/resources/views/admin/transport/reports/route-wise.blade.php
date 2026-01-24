@extends('layouts.app')

@section('title', 'Route-wise Report')

@section('page-title', 'Transport - Route-wise Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.reports.index') }}">Transport Reports</a></li>
    <li class="breadcrumb-item active">Route-wise</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Route-wise Student Report</h5>
                    @if($selectedRoute && $students->count() > 0)
                        <a href="{{ route('admin.transport.reports.export-route', ['route_id' => $selectedRoute, 'academic_year_id' => $selectedYear]) }}" class="btn btn-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.transport.reports.route-wise') }}" method="GET" class="mb-4">
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
                            <label class="form-label">Route <span class="text-danger">*</span></label>
                            <select name="route_id" class="form-select" required>
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ $selectedRoute == $route->id ? 'selected' : '' }}>
                                        {{ $route->route_name }} ({{ $route->start_place }} - {{ $route->end_place }})
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

                @if($selectedRouteData)
                    <!-- Route Info -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Route:</strong> {{ $selectedRouteData->route_name }}
                            </div>
                            <div class="col-md-3">
                                <strong>Vehicle:</strong> {{ $selectedRouteData->vehicle->vehicle_no ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Monthly Fare:</strong> ₹{{ number_format($selectedRouteData->fare_amount, 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Students:</strong> {{ $students->count() }}
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Class/Section</th>
                                    <th>Pickup Point</th>
                                    <th>Drop Point</th>
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
                                        <td>{{ $assignment->pickup_point ?? '-' }}</td>
                                        <td>{{ $assignment->drop_point ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No students assigned to this route.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i data-feather="map-pin" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                        <p class="text-muted">Please select a route to generate the report.</p>
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
