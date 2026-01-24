@extends('layouts.app')

@section('title', 'Leave Application Details')

@section('page-title', 'Staff Leaves - View Application')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.staff-leaves.index') }}">Staff Leaves</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Leave Application Details</h5>
                    <span class="badge {{ $leave->getStatusBadgeClass() }} fs-6">{{ $leave->getStatusLabel() }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Leave Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Leave Type:</td>
                                <td><span class="badge badge-light-secondary">{{ $leave->leave_type }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">From Date:</td>
                                <td>{{ $leave->from_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">To Date:</td>
                                <td>{{ $leave->to_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Days:</td>
                                <td><span class="badge badge-light-primary">{{ $leave->total_days }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Applied On:</td>
                                <td>{{ $leave->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Staff Information</h6>
                        @if($staff)
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $staff->first_name }} {{ $staff->last_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department:</td>
                                    <td>{{ $staff->department->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Designation:</td>
                                    <td>{{ $staff->designation->name ?? '-' }}</td>
                                </tr>
                            </table>
                        @else
                            <p class="text-muted">Staff information not available</p>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Reason</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $leave->reason }}
                    </div>
                </div>

                @if($leave->attachment)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Attachment</h6>
                        <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="btn btn-outline-primary">
                            <i data-feather="paperclip" class="me-1"></i> View Attachment
                        </a>
                    </div>
                @endif

                @if($leave->status !== 'pending')
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Admin Remarks</h6>
                        <div class="p-3 border rounded">
                            {{ $leave->admin_remarks ?? 'No remarks' }}
                        </div>
                        <small class="text-muted">
                            {{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }} by {{ $leave->approvedByUser->name ?? 'Admin' }}
                            on {{ $leave->approved_at?->format('M d, Y h:i A') }}
                        </small>
                    </div>
                @endif

                @if($leave->status === 'pending')
                    <div class="d-flex gap-2 mt-4">
                        <form action="{{ route('admin.staff-leaves.approve', $leave) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i data-feather="check" class="me-1"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i data-feather="x" class="me-1"></i> Reject
                        </button>
                    </div>
                @elseif($leave->status === 'approved')
                    <form action="{{ route('admin.staff-leaves.cancel', $leave) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to cancel this approved leave?')">
                            <i data-feather="x-circle" class="me-1"></i> Cancel Leave
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Timeline</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <div class="d-flex">
                            <div class="me-2"><span class="badge badge-light-primary rounded-circle p-2">1</span></div>
                            <div>
                                <strong>Applied</strong>
                                <p class="text-muted mb-0 small">{{ $leave->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </li>
                    @if($leave->status !== 'pending')
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-2">
                                    <span class="badge badge-light-{{ $leave->status === 'approved' ? 'success' : 'danger' }} rounded-circle p-2">2</span>
                                </div>
                                <div>
                                    <strong>{{ ucfirst($leave->status) }}</strong>
                                    <p class="text-muted mb-0 small">{{ $leave->approved_at?->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <a href="{{ route('admin.staff-leaves.index') }}" class="btn btn-secondary w-100 mt-3">
            <i data-feather="arrow-left" class="me-1"></i> Back to List
        </a>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.staff-leaves.reject', $leave) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
