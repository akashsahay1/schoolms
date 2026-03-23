@extends('layouts.app')

@section('title', 'Assign Route')

@section('page-title', 'Transport - Assign Route')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.assignments.index') }}">Transport</a></li>
    <li class="breadcrumb-item active">Assign Route</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Assign Route to Student</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.transport.assignments.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ (old('academic_year_id', $currentAcademicYear?->id) == $year->id) ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_active ? '(Current)' : '' }}
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
                                    <option value="{{ $route->id }}" {{ old('transport_route_id') == $route->id ? 'selected' : '' }} data-fare="{{ $route->fare_amount }}" data-vehicle="{{ $route->vehicle->vehicle_no ?? 'N/A' }}">
                                        {{ $route->route_name }} ({{ $route->start_place }} - {{ $route->end_place }})
                                    </option>
                                @endforeach
                            </select>
                            @error('transport_route_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="routeInfo" class="alert alert-info d-none mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Vehicle:</strong> <span id="routeVehicle">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Monthly Fare:</strong> <span id="routeFare">-</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Select Student</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" id="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Section</label>
                            <select name="section_id" id="section_id" class="form-select" disabled>
                                <option value="">Select Class First</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Student <span class="text-danger">*</span></label>
                            <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required disabled>
                                <option value="">Select Class First</option>
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Pickup/Drop Details</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pickup Point</label>
                            <select name="pickup_point" id="pickup_point_select" class="form-select" disabled>
                                <option value="">Select Route First</option>
                            </select>
                            <input type="text" name="pickup_point_custom" id="pickup_point_custom" class="form-control mt-2 d-none" placeholder="Enter custom pickup point">
                            @error('pickup_point')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Drop Point</label>
                            <select name="drop_point" id="drop_point_select" class="form-select" disabled>
                                <option value="">Select Route First</option>
                            </select>
                            <input type="text" name="drop_point_custom" id="drop_point_custom" class="form-control mt-2 d-none" placeholder="Enter custom drop point">
                            @error('drop_point')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Assignment</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1"></i> Assign Route
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
                <h5>Instructions</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i> Select the academic year</li>
                    <li class="mb-2"><i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i> Choose a transport route</li>
                    <li class="mb-2"><i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i> Select class to load students</li>
                    <li class="mb-2"><i data-feather="check-circle" class="text-success me-2" style="width: 16px; height: 16px;"></i> Pick a student to assign</li>
                    <li class="mb-2"><i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i> Pickup/Drop points are optional</li>
                    <li><i data-feather="alert-circle" class="text-warning me-2" style="width: 16px; height: 16px;"></i> A student can only have one active assignment per academic year</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    // Route selection - show info + load stops
    jQuery('#transport_route_id').on('change', function() {
        var selected = jQuery(this).find('option:selected');
        if (selected.val()) {
            var vehicle = selected.data('vehicle');
            var fare = selected.data('fare');
            jQuery('#routeVehicle').text(vehicle);
            jQuery('#routeFare').text('₹' + parseFloat(fare).toFixed(2));
            jQuery('#routeInfo').removeClass('d-none');
            loadStops(selected.val());
        } else {
            jQuery('#routeInfo').addClass('d-none');
            loadStops(null);
        }
    });

    // Load stops for selected route
    function loadStops(routeId) {
        var pickupSelect = jQuery('#pickup_point_select');
        var dropSelect = jQuery('#drop_point_select');

        if (!routeId) {
            pickupSelect.html('<option value="">Select Route First</option>').prop('disabled', true);
            dropSelect.html('<option value="">Select Route First</option>').prop('disabled', true);
            jQuery('#pickup_point_custom, #drop_point_custom').addClass('d-none').val('');
            return;
        }

        pickupSelect.html('<option value="">Loading...</option>').prop('disabled', true);
        dropSelect.html('<option value="">Loading...</option>').prop('disabled', true);

        jQuery.ajax({
            url: '{{ url("admin/transport/routes") }}/' + routeId + '/stops',
            type: 'GET',
            success: function(stops) {
                var options = '<option value="">Select Point</option>';
                if (stops && stops.length > 0) {
                    for (var i = 0; i < stops.length; i++) {
                        options += '<option value="' + stops[i] + '">' + stops[i] + '</option>';
                    }
                }
                options += '<option value="__other__">Other (Custom)</option>';
                pickupSelect.html(options).prop('disabled', false);
                dropSelect.html(options).prop('disabled', false);
            },
            error: function() {
                var fallback = '<option value="">No stops found</option><option value="__other__">Other (Custom)</option>';
                pickupSelect.html(fallback).prop('disabled', false);
                dropSelect.html(fallback).prop('disabled', false);
            }
        });
    }

    // Show/hide custom input when "Other" is selected
    jQuery(document).on('change', '#pickup_point_select', function() {
        if (jQuery(this).val() === '__other__') {
            jQuery('#pickup_point_custom').removeClass('d-none').focus();
        } else {
            jQuery('#pickup_point_custom').addClass('d-none').val('');
        }
    });

    jQuery(document).on('change', '#drop_point_select', function() {
        if (jQuery(this).val() === '__other__') {
            jQuery('#drop_point_custom').removeClass('d-none').focus();
        } else {
            jQuery('#drop_point_custom').addClass('d-none').val('');
        }
    });

    // Trigger on page load if route is selected
    if (jQuery('#transport_route_id').val()) {
        jQuery('#transport_route_id').trigger('change');
    }

    // Class selection - load sections + students
    jQuery('#class_id').on('change', function() {
        var classId = jQuery(this).val();
        var sectionSelect = jQuery('#section_id');
        var studentSelect = jQuery('#student_id');

        if (!classId) {
            sectionSelect.html('<option value="">Select Class First</option>').prop('disabled', true);
            studentSelect.html('<option value="">Select Class First</option>').prop('disabled', true);
            return;
        }

        sectionSelect.html('<option value="">Loading...</option>').prop('disabled', true);
        studentSelect.html('<option value="">Loading...</option>').prop('disabled', true);

        jQuery.ajax({
            url: '{{ route("admin.transport.assignments.sections") }}',
            type: 'GET',
            data: { class_id: classId },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                if (data.length > 0) {
                    var options = '<option value="">All Sections</option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    sectionSelect.html(options).prop('disabled', false);
                } else {
                    sectionSelect.html('<option value="">No sections for this class</option>').prop('disabled', true);
                }

                // Load students for the class regardless of sections
                loadStudents(classId, null);
            },
            error: function() {
                sectionSelect.html('<option value="">No sections available</option>').prop('disabled', true);
                // Still load students even if sections fail
                loadStudents(classId, null);
            }
        });
    });

    // Section selection - reload students
    jQuery('#section_id').on('change', function() {
        var classId = jQuery('#class_id').val();
        if (classId) {
            loadStudents(classId, jQuery(this).val());
        }
    });

    function loadStudents(classId, sectionId) {
        var studentSelect = jQuery('#student_id');
        studentSelect.html('<option value="">Loading...</option>').prop('disabled', true);

        jQuery.ajax({
            url: '{{ route("admin.transport.assignments.students") }}',
            type: 'GET',
            data: { class_id: classId, section_id: sectionId },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(data) {
                if (data.length > 0) {
                    var options = '<option value="">Select Student</option>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].id + '">' + data[i].first_name + ' ' + data[i].last_name + ' (' + data[i].admission_no + ')</option>';
                    }
                    studentSelect.html(options).prop('disabled', false);
                } else {
                    studentSelect.html('<option value="">No students found</option>').prop('disabled', true);
                }
            },
            error: function() {
                studentSelect.html('<option value="">No students found</option>').prop('disabled', true);
            }
        });
    }

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
