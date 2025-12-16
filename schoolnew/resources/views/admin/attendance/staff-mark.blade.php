@extends('layouts.app')

@section('title', 'Mark Staff Attendance')

@section('page-title', 'Mark Staff Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Staff Attendance</li>
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
                <h5>Select Date and Department</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.staff-attendance.mark') }}" class="row g-3">
                    <input type="hidden" name="load" value="1">
                    <div class="col-md-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block">Load Staff</button>
                    </div>
                </form>
            </div>
        </div>

        @if($staff->count() > 0)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Mark Attendance {{ $selectedDepartment ? '- ' . $selectedDepartment->name : '- All Staff' }}</h5>
                    <div>
                        <strong>Date:</strong> {{ Carbon\Carbon::parse($selectedDate)->format('d M Y, l') }}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.staff-attendance.store') }}" method="POST" id="attendance-form">
                        @csrf
                        <input type="hidden" name="department_id" value="{{ request('department_id') }}">
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
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="markAll('on_leave')">
                                        Mark All On Leave
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Staff Name</th>
                                        <th>Department</th>
                                        <th width="120">Status</th>
                                        <th width="100">Check In</th>
                                        <th width="100">Check Out</th>
                                        <th width="180">Remarks</th>
                                        <th width="80">Current</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff as $index => $member)
                                        @php
                                            $attendance = $attendanceData->get($member->id);
                                            $currentStatus = $attendance ? $attendance->status : 'present';
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        @if($member->photo)
                                                            <img src="{{ $member->photo_url }}" alt="{{ $member->full_name }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-{{ $member->gender == 'male' ? 'primary' : 'danger' }} d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px; font-size: 14px;">
                                                                {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $member->full_name }}</strong>
                                                        <br><small class="text-muted">{{ $member->designation->name ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $member->department->name ?? 'N/A' }}</td>
                                            <td>
                                                <select name="attendance[{{ $member->id }}]" class="form-select form-select-sm attendance-select" data-staff="{{ $member->id }}">
                                                    <option value="present" {{ $currentStatus == 'present' ? 'selected' : '' }}>Present</option>
                                                    <option value="absent" {{ $currentStatus == 'absent' ? 'selected' : '' }}>Absent</option>
                                                    <option value="late" {{ $currentStatus == 'late' ? 'selected' : '' }}>Late</option>
                                                    <option value="half_day" {{ $currentStatus == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                    <option value="on_leave" {{ $currentStatus == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="time" name="check_in_time[{{ $member->id }}]" class="form-control form-control-sm" value="{{ $attendance ? $attendance->check_in_time?->format('H:i') : '' }}">
                                            </td>
                                            <td>
                                                <input type="time" name="check_out_time[{{ $member->id }}]" class="form-control form-control-sm" value="{{ $attendance ? $attendance->check_out_time?->format('H:i') : '' }}">
                                            </td>
                                            <td>
                                                <input type="text" name="remarks[{{ $member->id }}]" class="form-control form-control-sm" placeholder="Optional" value="{{ $attendance ? $attendance->remarks : '' }}">
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
                                        <strong>Total Staff:</strong> {{ $staff->count() }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.staff-attendance.mark') }}" class="btn btn-light">Reset</a>
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
        @elseif(request()->has('load'))
            <div class="card mt-3">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i data-feather="users" class="icon-lg text-muted"></i>
                    </div>
                    <h5 class="text-muted">No Staff Found</h5>
                    <p class="text-muted">No staff members found{{ $selectedDepartment ? ' in ' . $selectedDepartment->name : '' }}.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function markAll(status) {
        jQuery('.attendance-select').val(status).trigger('change');
    }
</script>
@endpush
