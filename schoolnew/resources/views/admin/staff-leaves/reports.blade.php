@extends('layouts.app')

@section('title', 'Staff Leave Reports')

@section('page-title', 'Staff Leaves - Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff-leaves.index') }}">Staff Leaves</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Leave Reports</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.staff-leaves.reports.export', request()->query()) }}" class="btn btn-outline-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <form action="{{ route('admin.staff-leaves.reports') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Leave Summary by Type</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th class="text-center">Applications</th>
                                <th class="text-center">Total Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveByType as $item)
                                <tr>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $item->leave_type }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->count }}</td>
                                    <td class="text-center">
                                        <strong>{{ $item->total_days }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <p class="text-muted mb-0">No approved leaves found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($leaveByType->count() > 0)
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ $leaveByType->sum('count') }}</td>
                                    <td class="text-center">{{ $leaveByType->sum('total_days') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Leave Summary by Department</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th class="text-center">Applications</th>
                                <th class="text-center">Total Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveByDepartment as $item)
                                <tr>
                                    <td>
                                        {{ $item->department->name ?? 'Unknown' }}
                                    </td>
                                    <td class="text-center">{{ $item->count }}</td>
                                    <td class="text-center">
                                        <strong>{{ $item->total_days }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <p class="text-muted mb-0">No approved leaves found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($leaveByDepartment->count() > 0)
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ $leaveByDepartment->sum('count') }}</td>
                                    <td class="text-center">{{ $leaveByDepartment->sum('total_days') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Leave Type Definitions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Code</th>
                                <th>Allowed Days</th>
                                <th>Is Paid</th>
                                <th>Requires Attachment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveTypes as $type)
                                <tr>
                                    <td>
                                        <strong>{{ $type->name }}</strong>
                                        @if($type->description)
                                            <br><small class="text-muted">{{ $type->description }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-light-secondary">{{ $type->code }}</span></td>
                                    <td>{{ $type->allowed_days }} days/year</td>
                                    <td>
                                        @if($type->is_paid)
                                            <span class="badge badge-light-success">Paid</span>
                                        @else
                                            <span class="badge badge-light-warning">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($type->requires_attachment)
                                            <span class="badge badge-light-info">Required</span>
                                        @else
                                            <span class="text-muted">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($type->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
