@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('page-title', 'Mark Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Mark Attendance</li>
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

        <div class="card">
            <div class="card-header">
                <h5>Select Class and Date</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.mark') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" id="class-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" class="form-select" id="section-select" required>
                            <option value="">Select Section</option>
                            @if($selectedClass)
                                @foreach($selectedClass->sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Load Students</button>
                    </div>
                </form>
            </div>
        </div>

        @if(!$activeYear)
            <div class="alert alert-warning mt-3">
                <strong>Warning:</strong> No active academic year found. Please <a href="{{ route('admin.academic-years.index') }}">set an active academic year</a> first.
            </div>
        @elseif(request()->filled('class_id') && request()->filled('section_id') && $students->count() == 0)
            <div class="alert alert-info mt-3">
                <strong>No Students Found:</strong> There are no students in the selected class and section.
            </div>
        @endif

        @if($students->count() > 0)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Mark Attendance - {{ $selectedClass->name }} - Section {{ $selectedSection->name }}</h5>
                    <div>
                        <strong>Date:</strong> {{ Carbon\Carbon::parse($selectedDate)->format('d M Y, l') }}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.attendance.store') }}" method="POST" id="attendance-form">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                        <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="markAll('present')">
                                        Mark All Present
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="markAll('absent')">
                                        Mark All Absent
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="markAll('late')">
                                        Mark All Late
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">Roll No.</th>
                                        <th>Student Name</th>
                                        <th width="100">Status</th>
                                        <th width="120">Check In Time</th>
                                        <th width="200">Remarks</th>
                                        <th width="80">Current</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        @php
                                            $attendance = $attendanceData->get($student->id);
                                            $currentStatus = $attendance ? $attendance->status : 'present';
                                        @endphp
                                        <tr>
                                            <td>{{ $student->roll_no }}</td>
                                            <td>{{ $student->full_name }}</td>
                                            <td>
                                                <select name="attendance[{{ $student->id }}]" class="form-select form-select-sm attendance-select" 
                                                        data-student="{{ $student->id }}">
                                                    <option value="present" {{ $currentStatus == 'present' ? 'selected' : '' }}>Present</option>
                                                    <option value="absent" {{ $currentStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                                    <option value="late" {{ $currentStatus == 'late' ? 'selected' : '' }}>Late</option>
                                                    <option value="half_day" {{ $currentStatus == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="time" name="check_in_time[{{ $student->id }}]" 
                                                       class="form-control form-control-sm check-in-time"
                                                       value="{{ $attendance ? $attendance->check_in_time : '' }}">
                                            </td>
                                            <td>
                                                <input type="text" name="remarks[{{ $student->id }}]" 
                                                       class="form-control form-control-sm"
                                                       placeholder="Optional remarks"
                                                       value="{{ $attendance ? $attendance->remarks : '' }}">
                                            </td>
                                            <td>
                                                @if($attendance)
                                                    {!! $attendance->status_badge !!}
                                                @else
                                                    <span class="badge badge-light-secondary">Not Marked</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Total Students:</strong> {{ $students->count() }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.attendance.mark') }}" class="btn btn-light">Reset</a>
                                        <button type="submit" class="btn btn-success" id="save-attendance">
                                            <i data-feather="save" class="icon-xs"></i> Save Attendance
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
                jQuery.get('/admin/attendance/sections/' + classId, function(data) {
                    sectionSelect.html('<option value="">Select Section</option>');
                    jQuery.each(data, function(index, section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                }).fail(function() {
                    console.error('Failed to load sections');
                    sectionSelect.html('<option value="">Select Section</option>');
                });
            } else {
                sectionSelect.html('<option value="">Select Section</option>');
            }
        });

        // Handle attendance status changes
        jQuery('.attendance-select').on('change', function() {
            var status = jQuery(this).val();
            var studentId = jQuery(this).data('student');
            var checkInField = jQuery('input[name="check_in_time[' + studentId + ']"]');

            if (status === 'present' || status === 'late') {
                checkInField.prop('disabled', false);
                if (!checkInField.val() && status === 'late') {
                    // Set default late time if not set
                    checkInField.val('09:30');
                }
            } else {
                checkInField.prop('disabled', true);
                checkInField.val('');
            }
        });

        // Initialize check-in fields based on current status
        jQuery('.attendance-select').trigger('change');
    });

    function markAll(status) {
        jQuery('.attendance-select').val(status).trigger('change');
    }
</script>
@endpush