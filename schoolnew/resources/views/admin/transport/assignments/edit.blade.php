@extends('layouts.app')

@section('title', 'Edit Route Assignment')

@section('page-title', 'Transport - Edit Route Assignment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.assignments.index') }}">Transport</a></li>
    <li class="breadcrumb-item active">Edit Assignment</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit Route Assignment</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.transport.assignments.update', $assignment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Student Info (Read-only) -->
                    <div class="alert alert-secondary mb-4">
                        <h6 class="mb-2">Student Information</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Name:</strong> {{ $assignment->student->first_name ?? 'N/A' }} {{ $assignment->student->last_name ?? '' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Admission No:</strong> {{ $assignment->student->admission_no ?? '-' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Class:</strong> {{ $assignment->student->schoolClass->name ?? '-' }}
                                @if($assignment->student->section)
                                    / {{ $assignment->student->section->name }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ (old('academic_year_id', $assignment->academic_year_id) == $year->id) ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Route <span class="text-danger">*</span></label>
                            <select name="transport_route_id" id="transport_route_id" class="form-select @error('transport_route_id') is-invalid @enderror" required>
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ old('transport_route_id', $assignment->transport_route_id) == $route->id ? 'selected' : '' }} data-fare="{{ $route->fare_amount }}" data-vehicle="{{ $route->vehicle->vehicle_no ?? 'N/A' }}">
                                        {{ $route->route_name }} ({{ $route->start_place }} - {{ $route->end_place }})
                                    </option>
                                @endforeach
                            </select>
                            @error('transport_route_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="routeInfo" class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Vehicle:</strong> <span id="routeVehicle">{{ $assignment->route->vehicle->vehicle_no ?? '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Monthly Fare:</strong> <span id="routeFare">₹{{ number_format($assignment->route->fare_amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Pickup/Drop Details</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pickup Point</label>
                            <input type="text" name="pickup_point" id="pickup_point" class="form-control @error('pickup_point') is-invalid @enderror" value="{{ old('pickup_point', $assignment->pickup_point) }}" placeholder="e.g., Near City Mall">
                            @error('pickup_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Drop Point</label>
                            <input type="text" name="drop_point" id="drop_point" class="form-control @error('drop_point') is-invalid @enderror" value="{{ old('drop_point', $assignment->drop_point) }}" placeholder="e.g., Main Gate">
                            @error('drop_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $assignment->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Assignment</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Update Assignment
                        </button>
                        <a href="{{ route('admin.transport.assignments.index') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Assignment History</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Created: {{ $assignment->created_at->format('M d, Y h:i A') }}</p>
                <p class="text-muted mb-0">Last Updated: {{ $assignment->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Route selection - show info
    jQuery('#transport_route_id').on('change', function() {
        var selected = jQuery(this).find('option:selected');
        if (selected.val()) {
            var vehicle = selected.data('vehicle');
            var fare = selected.data('fare');
            jQuery('#routeVehicle').text(vehicle);
            jQuery('#routeFare').text('₹' + parseFloat(fare).toFixed(2));
            jQuery('#routeInfo').removeClass('d-none');
        } else {
            jQuery('#routeInfo').addClass('d-none');
        }
    });

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
