@extends('layouts.app')

@section('title', 'Staff Leave Applications')

@section('page-title', 'Staff Leave Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Staff Leaves</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round warning">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $pendingCount }}</h4>
                        <span class="f-light">Pending</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round success">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#task') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $approvedCount }}</h4>
                        <span class="f-light">Approved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round danger">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#close') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $rejectedCount }}</h4>
                        <span class="f-light">Rejected</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round primary">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#file') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $pendingCount + $approvedCount + $rejectedCount }}</h4>
                        <span class="f-light">Total</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Staff Leave Applications</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.staff-leaves.types.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="settings" class="me-1"></i> Leave Types
                        </a>
                        <a href="{{ route('admin.staff-leaves.balances') }}" class="btn btn-outline-info">
                            <i data-feather="bar-chart-2" class="me-1"></i> Balances
                        </a>
                        <a href="{{ route('admin.staff-leaves.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Apply Leave
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.staff-leaves.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="leave_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->code }}" {{ request('leave_type') == $type->code ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="department" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff</th>
                                <th>Leave Type</th>
                                <th>From - To</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    <td>{{ $leaves->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $leave->appliedByUser->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td><span class="badge badge-light-secondary">{{ $leave->leave_type }}</span></td>
                                    <td>
                                        {{ $leave->from_date->format('M d') }} - {{ $leave->to_date->format('M d, Y') }}
                                    </td>
                                    <td><span class="badge badge-light-primary">{{ $leave->total_days }}</span></td>
                                    <td>
                                        <span title="{{ $leave->reason }}">{{ Str::limit($leave->reason, 30) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $leave->getStatusBadgeClass() }}">
                                            {{ $leave->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.staff-leaves.show', $leave) }}" class="btn btn-info btn-sm" title="View">
                                                <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            @if($leave->status === 'pending')
                                                <form action="{{ route('admin.staff-leaves.approve', $leave) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                        <i data-feather="check" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm reject-btn" data-id="{{ $leave->id }}" data-name="{{ $leave->appliedByUser->name ?? 'N/A' }}" title="Reject">
                                                    <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <p class="text-muted mb-2">No leave applications found.</p>
                                        <a href="{{ route('admin.staff-leaves.create') }}" class="btn btn-primary">Apply for Leave</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leaves->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $leaves->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Rejecting leave application for <strong id="rejectStaffName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="admin_remarks" class="form-control" rows="3" required placeholder="Please provide a reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    jQuery('.reject-btn').on('click', function() {
        var leaveId = jQuery(this).data('id');
        var staffName = jQuery(this).data('name');

        jQuery('#rejectStaffName').text(staffName);
        jQuery('#rejectForm').attr('action', '{{ url("admin/staff-leaves") }}/' + leaveId + '/reject');
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    });

    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
