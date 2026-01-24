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
                            <select name="section_id" id="section_id" class="form-select">
                                <option value="">All Sections</option>
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
                            <input type="text" name="pickup_point" id="pickup_point" class="form-control @error('pickup_point') is-invalid @enderror" value="{{ old('pickup_point') }}" placeholder="e.g., Near City Mall">
                            @error('pickup_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Drop Point</label>
                            <input type="text" name="drop_point" id="drop_point" class="form-control @error('drop_point') is-invalid @enderror" value="{{ old('drop_point') }}" placeholder="e.g., Main Gate">
                            @error('drop_point')
                                <div class="invalid-feedback">{{ $message }}</div>
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

    // Trigger on page load if route is selected
    if (jQuery('#transport_route_id').val()) {
        jQuery('#transport_route_id').trigger('change');
    }

    // Class selection - load sections
    jQuery('#class_id').on('change', function() {
        var classId = jQuery(this).val();
        var sectionSelect = jQuery('#section_id');
        var studentSelect = jQuery('#student_id');

        sectionSelect.html('<option value="">Loading...</option>');
        studentSelect.html('<option value="">Select Section or Class First</option>').prop('disabled', true);

        if (classId) {
            jQuery.ajax({
                url: '{{ route("admin.transport.assignments.sections") }}',
                type: 'GET',
                data: { class_id: classId },
                success: function(data) {
                    var options = '<option value="">All Sections</option>';
                    data.forEach(function(section) {
                        options += '<option value="' + section.id + '">' + section.name + '</option>';
                    });
                    sectionSelect.html(options);

                    // Load students for the class
                    loadStudents(classId, null);
                },
                error: function() {
                    sectionSelect.html('<option value="">Error loading sections</option>');
                }
            });
        } else {
            sectionSelect.html('<option value="">All Sections</option>');
        }
    });

    // Section selection - load students
    jQuery('#section_id').on('change', function() {
        var classId = jQuery('#class_id').val();
        var sectionId = jQuery(this).val();

        if (classId) {
            loadStudents(classId, sectionId);
        }
    });

    function loadStudents(classId, sectionId) {
        var studentSelect = jQuery('#student_id');
        studentSelect.html('<option value="">Loading...</option>').prop('disabled', true);

        jQuery.ajax({
            url: '{{ route("admin.transport.assignments.students") }}',
            type: 'GET',
            data: {
                class_id: classId,
                section_id: sectionId
            },
            success: function(data) {
                var options = '<option value="">Select Student</option>';
                data.forEach(function(student) {
                    options += '<option value="' + student.id + '">' + student.first_name + ' ' + student.last_name + ' (' + student.admission_no + ')</option>';
                });
                studentSelect.html(options).prop('disabled', false);
            },
            error: function() {
                studentSelect.html('<option value="">Error loading students</option>');
            }
        });
    }

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
