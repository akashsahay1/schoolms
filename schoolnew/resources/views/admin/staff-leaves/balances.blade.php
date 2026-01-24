@extends('layouts.app')

@section('title', 'Staff Leave Balances')

@section('page-title', 'Staff Leaves - Balances')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff-leaves.index') }}">Staff Leaves</a></li>
    <li class="breadcrumb-item active">Balances</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Staff Leave Balances</h5>
                    <a href="{{ route('admin.staff-leaves.balances.allocate') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Allocate Leaves
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.staff-leaves.balances') }}" method="GET" class="mb-4">
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
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $selectedDepartment == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Name</th>
                                <th>Department</th>
                                @foreach($leaveTypes as $type)
                                    <th class="text-center">{{ $type->code }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffList as $member)
                                <tr>
                                    <td>{{ $staffList->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $member->first_name }} {{ $member->last_name }}</strong>
                                        <br><small class="text-muted">{{ $member->designation->name ?? '' }}</small>
                                    </td>
                                    <td>{{ $member->department->name ?? '-' }}</td>
                                    @foreach($leaveTypes as $type)
                                        @php
                                            $balance = $member->leaveBalances->where('leave_type_id', $type->id)->first();
                                        @endphp
                                        <td class="text-center">
                                            @if($balance)
                                                <span class="badge badge-light-{{ $balance->remaining_days > 0 ? 'success' : 'danger' }}">
                                                    {{ $balance->remaining_days }}/{{ $balance->total_available }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 3 + $leaveTypes->count() }}" class="text-center py-4">
                                        <p class="text-muted">No staff members found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($staffList->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $staffList->withQueryString()->links() }}
                    </div>
                @endif

                <!-- Legend -->
                <div class="mt-4 p-3 border rounded">
                    <h6 class="mb-2">Legend</h6>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($leaveTypes as $type)
                            <span><strong>{{ $type->code }}</strong> - {{ $type->name }} ({{ $type->allowed_days }} days/year)</span>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2">Format: Remaining/Total</small>
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
