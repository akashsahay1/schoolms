@extends('layouts.app')

@section('title', 'Attendance Calendar')

@section('page-title', 'Attendance Calendar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance Calendar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>View Attendance Calendar</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.calendar') }}" class="row g-3 mb-4">
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
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            @for($year = date('Y') - 2; $year <= date('Y') + 1; $year++)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">View Calendar</button>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedClass && $selectedSection)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ $selectedClass->name }} - Section {{ $selectedSection->name }} | {{ date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear)) }}</h5>
                    <div class="d-flex gap-2">
                        <span class="badge badge-light-success px-3 py-2"><i data-feather="check-circle" class="icon-xs me-1"></i> Present</span>
                        <span class="badge badge-light-danger px-3 py-2"><i data-feather="x-circle" class="icon-xs me-1"></i> Absent</span>
                        <span class="badge badge-light-warning px-3 py-2"><i data-feather="clock" class="icon-xs me-1"></i> Late</span>
                        <span class="badge badge-light-info px-3 py-2"><i data-feather="minus-circle" class="icon-xs me-1"></i> Half Day</span>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $firstDayOfMonth = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1);
                        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
                        $startingDayOfWeek = $firstDayOfMonth->dayOfWeek;
                        $daysInMonth = $firstDayOfMonth->daysInMonth;
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-bordered attendance-calendar">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 14.28%;">Sun</th>
                                    <th class="text-center" style="width: 14.28%;">Mon</th>
                                    <th class="text-center" style="width: 14.28%;">Tue</th>
                                    <th class="text-center" style="width: 14.28%;">Wed</th>
                                    <th class="text-center" style="width: 14.28%;">Thu</th>
                                    <th class="text-center" style="width: 14.28%;">Fri</th>
                                    <th class="text-center" style="width: 14.28%;">Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentDay = 1;
                                    $totalWeeks = ceil(($daysInMonth + $startingDayOfWeek) / 7);
                                @endphp

                                @for($week = 0; $week < $totalWeeks; $week++)
                                    <tr>
                                        @for($day = 0; $day < 7; $day++)
                                            @php
                                                $dayOffset = $week * 7 + $day;
                                                $isValidDay = $dayOffset >= $startingDayOfWeek && $currentDay <= $daysInMonth;
                                                $dateString = null;
                                                $dayData = null;

                                                if ($isValidDay) {
                                                    $dateString = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $currentDay);
                                                    $dayData = $calendarData[$dateString] ?? null;
                                                }
                                            @endphp

                                            <td class="calendar-day {{ $isValidDay ? '' : 'bg-light' }} {{ $day == 0 ? 'sunday' : '' }}">
                                                @if($isValidDay)
                                                    <div class="day-header">
                                                        <span class="day-number {{ $dateString == date('Y-m-d') ? 'today' : '' }}">
                                                            {{ $currentDay }}
                                                        </span>
                                                    </div>
                                                    @if($dayData)
                                                        <div class="day-content">
                                                            <div class="attendance-stats">
                                                                @if($dayData['present'] > 0)
                                                                    <span class="badge badge-light-success" title="Present">{{ $dayData['present'] }}</span>
                                                                @endif
                                                                @if($dayData['absent'] > 0)
                                                                    <span class="badge badge-light-danger" title="Absent">{{ $dayData['absent'] }}</span>
                                                                @endif
                                                                @if($dayData['late'] > 0)
                                                                    <span class="badge badge-light-warning" title="Late">{{ $dayData['late'] }}</span>
                                                                @endif
                                                                @if($dayData['half_day'] > 0)
                                                                    <span class="badge badge-light-info" title="Half Day">{{ $dayData['half_day'] }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="attendance-percent mt-1">
                                                                @php
                                                                    $percentage = $dayData['total'] > 0
                                                                        ? round(($dayData['present'] + $dayData['late'] + $dayData['half_day'] * 0.5) / $dayData['total'] * 100, 1)
                                                                        : 0;
                                                                @endphp
                                                                <small class="text-{{ $percentage >= 75 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger') }}">
                                                                    {{ $percentage }}%
                                                                </small>
                                                            </div>
                                                            <a href="{{ route('admin.attendance.mark', ['class_id' => $selectedClass->id, 'section_id' => $selectedSection->id, 'date' => $dateString]) }}"
                                                               class="btn btn-outline-primary btn-sm mt-1 w-100">View</a>
                                                        </div>
                                                    @else
                                                        <div class="day-content text-center text-muted">
                                                            <small>Not Marked</small>
                                                            @if(\Carbon\Carbon::parse($dateString)->lte(now()) && $day != 0)
                                                                <a href="{{ route('admin.attendance.mark', ['class_id' => $selectedClass->id, 'section_id' => $selectedSection->id, 'date' => $dateString]) }}"
                                                                   class="btn btn-primary btn-sm mt-1 w-100">Mark</a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @php $currentDay++; @endphp
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Monthly Summary</h6>
                                    @php
                                        $totalPresent = collect($calendarData)->sum('present');
                                        $totalAbsent = collect($calendarData)->sum('absent');
                                        $totalLate = collect($calendarData)->sum('late');
                                        $totalHalfDay = collect($calendarData)->sum('half_day');
                                        $markedDays = count($calendarData);
                                    @endphp
                                    <div class="row text-center">
                                        <div class="col-md-2">
                                            <h4 class="text-primary">{{ $markedDays }}</h4>
                                            <p class="mb-0 text-muted">Days Marked</p>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-success">{{ $totalPresent }}</h4>
                                            <p class="mb-0 text-muted">Total Present</p>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-danger">{{ $totalAbsent }}</h4>
                                            <p class="mb-0 text-muted">Total Absent</p>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-warning">{{ $totalLate }}</h4>
                                            <p class="mb-0 text-muted">Total Late</p>
                                        </div>
                                        <div class="col-md-2">
                                            <h4 class="text-info">{{ $totalHalfDay }}</h4>
                                            <p class="mb-0 text-muted">Total Half Day</p>
                                        </div>
                                        <div class="col-md-2">
                                            @php
                                                $total = $totalPresent + $totalAbsent + $totalLate + $totalHalfDay;
                                                $avgAttendance = $total > 0 ? round(($totalPresent + $totalLate + $totalHalfDay * 0.5) / $total * 100, 1) : 0;
                                            @endphp
                                            <h4 class="text-{{ $avgAttendance >= 75 ? 'success' : ($avgAttendance >= 50 ? 'warning' : 'danger') }}">{{ $avgAttendance }}%</h4>
                                            <p class="mb-0 text-muted">Avg Attendance</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request()->has('class_id'))
            <div class="card mt-3">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i data-feather="calendar" class="icon-lg text-muted"></i>
                    </div>
                    <h5 class="text-muted">Select Class and Section</h5>
                    <p class="text-muted">Please select both class and section to view the attendance calendar.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Classes data for section filtering
    const classesData = @json($classes);

    jQuery(document).ready(function() {
        // Load sections when class changes
        jQuery('#class-select').on('change', function() {
            var classId = jQuery(this).val();
            var sectionSelect = jQuery('#section-select');

            sectionSelect.html('<option value="">Select Section</option>');

            if (classId) {
                var selectedClass = classesData.find(c => c.id == classId);
                if (selectedClass && selectedClass.sections) {
                    selectedClass.sections.forEach(function(section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .attendance-calendar .calendar-day {
        vertical-align: top;
        height: 120px;
        padding: 5px;
    }

    .attendance-calendar .day-header {
        text-align: right;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
        margin-bottom: 5px;
    }

    .attendance-calendar .day-number {
        font-weight: 600;
        font-size: 14px;
    }

    .attendance-calendar .day-number.today {
        background: var(--bs-primary);
        color: white;
        padding: 2px 8px;
        border-radius: 50%;
    }

    .attendance-calendar .sunday {
        background-color: #fff5f5;
    }

    .attendance-calendar .day-content {
        font-size: 12px;
    }

    .attendance-calendar .attendance-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        justify-content: center;
    }

    .attendance-calendar .attendance-stats .badge {
        font-size: 10px;
        padding: 2px 5px;
    }

    @media print {
        .btn, .card-header button {
            display: none !important;
        }
    }
</style>
@endpush
