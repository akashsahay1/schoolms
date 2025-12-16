@extends('layouts.app')

@section('title', 'Fee Collection')

@section('page-title', 'Fee Collection')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Fee Collection</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>Select Class to Collect Fees</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.fees.collection') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select" id="class-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" id="section-select">
                            <option value="">All Sections</option>
                            @if($selectedClass)
                                @foreach($selectedClass->sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block" id="show-students-btn">Show Students</button>
                    </div>
                </form>
            </div>
        </div>

        @if($students->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Students - {{ $selectedClass->name }} {{ $selectedSection ? '- Section ' . $selectedSection->name : '' }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Roll No.</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Pending Fees</th>
                                    <th>Pending Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->roll_no }}</td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->schoolClass->name }}</td>
                                        <td>{{ $student->section->name }}</td>
                                        <td>
                                            @if(isset($student->pendingFees))
                                                <span class="badge badge-light-{{ $student->pendingFees['count'] > 0 ? 'warning' : 'success' }}">
                                                    {{ $student->pendingFees['count'] }} Pending
                                                </span>
                                            @else
                                                <span class="badge badge-light-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($student->pendingFees) && $student->pendingFees['amount'] > 0)
                                                <strong class="text-danger">₹{{ number_format($student->pendingFees['amount'], 2) }}</strong>
                                            @else
                                                <span class="text-success">₹0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.fees.collect', $student) }}" class="btn btn-primary btn-sm">
                                                <i data-feather="credit-card" class="icon-xs"></i> Collect Fee
                                            </a>
                                        </td>
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
        // Load sections when class changes
        jQuery('#class-select').on('change', function() {
            var classId = jQuery(this).val();
            var sectionSelect = jQuery('#section-select');

            sectionSelect.html('<option value="">Loading...</option>');

            if (classId) {
                jQuery.get('/admin/students/sections/' + classId, function(data) {
                    sectionSelect.html('<option value="">All Sections</option>');
                    jQuery.each(data, function(index, section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                }).fail(function() {
                    console.error('Failed to load sections');
                    sectionSelect.html('<option value="">All Sections</option>');
                });
            } else {
                sectionSelect.html('<option value="">All Sections</option>');
            }
        });

        // Validate class selection before form submission
        jQuery('#show-students-btn').on('click', function(e) {
            var classId = jQuery('#class-select').val();
            if (!classId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Class',
                    text: 'Please select a class first'
                });
                return false;
            }
        });
    });
</script>
@endpush