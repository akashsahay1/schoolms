@extends('layouts.app')

@section('title', 'Class-wise Transport Report')

@section('page-title', 'Transport - Class-wise Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.reports.index') }}">Transport Reports</a></li>
    <li class="breadcrumb-item active">Class-wise</li>
@endsection

@section('content')
<div class="row">
    <!-- Class Summary -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Class Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th class="text-center">Transport</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classSummary as $class)
                                <tr>
                                    <td>{{ $class->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-light-{{ $class->students_count > 0 ? 'primary' : 'secondary' }}">
                                            {{ $class->students_count }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary">
                                <th>Total</th>
                                <th class="text-center">{{ $classSummary->sum('students_count') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Report -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Class-wise Transport Report</h5>
                    @if($selectedClass && $students->count() > 0)
                        <a href="{{ route('admin.transport.reports.export-class', ['class_id' => $selectedClass, 'academic_year_id' => $selectedYear]) }}" class="btn btn-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.transport.reports.class-wise') }}" method="GET" class="mb-4">
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
                        <div class="col-md-4">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="search" class="me-1"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>

                @if($selectedClass)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Section</th>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Monthly Fare</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $assignment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assignment->student->admission_no ?? '-' }}</td>
                                        <td><strong>{{ $assignment->student->first_name ?? '' }} {{ $assignment->student->last_name ?? '' }}</strong></td>
                                        <td>{{ $assignment->student->section->name ?? '-' }}</td>
                                        <td>{{ $assignment->route->route_name ?? '-' }}</td>
                                        <td>
                                            @if($assignment->route && $assignment->route->vehicle)
                                                <span class="badge badge-light-info">{{ $assignment->route->vehicle->vehicle_no }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>₹{{ number_format($assignment->route->fare_amount ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No students with transport in this class.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($students->count() > 0)
                                <tfoot>
                                    <tr class="table-secondary">
                                        <th colspan="6" class="text-end">Total Monthly:</th>
                                        <th>₹{{ number_format($students->sum(fn($a) => $a->route->fare_amount ?? 0), 2) }}</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i data-feather="book" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                        <p class="text-muted">Please select a class to generate the report.</p>
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
