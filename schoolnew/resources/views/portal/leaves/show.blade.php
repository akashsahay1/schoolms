@extends('layouts.portal')

@section('title', 'Leave Application Details')
@section('page-title', 'Leave Application Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.leaves.index') }}">Leave Applications</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Leave Application</h5>
                        <span class="badge {{ $leave->getStatusBadgeClass() }} fs-6">{{ $leave->getStatusLabel() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted">Leave Type</label>
                            <p class="fw-medium mb-3">{{ $leave->getLeaveTypeLabel() }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Total Days</label>
                            <p class="fw-medium mb-3">{{ $leave->total_days }} day(s)</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted">From Date</label>
                            <p class="fw-medium mb-3">{{ $leave->from_date->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">To Date</label>
                            <p class="fw-medium mb-3">{{ $leave->to_date->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted">Reason</label>
                        <div class="bg-light p-3 rounded text-dark">
                            {{ $leave->reason }}
                        </div>
                    </div>

                    @if($leave->attachment)
                        <div class="mb-4">
                            <label class="text-muted">Attachment</label>
                            <div>
                                <a href="{{ asset('storage/' . $leave->attachment) }}" class="btn btn-outline-primary" target="_blank">
                                    <i class="fa fa-download me-1"></i> View Attachment
                                </a>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted">Applied On</label>
                            <p class="fw-medium mb-0">{{ $leave->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        @if($leave->approved_at)
                            <div class="col-md-6">
                                <label class="text-muted">{{ $leave->status === 'approved' ? 'Approved' : 'Processed' }} On</label>
                                <p class="fw-medium mb-0">{{ $leave->approved_at->format('F d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>

                    @if($leave->admin_remarks)
                        <div class="mb-4">
                            <label class="text-muted">Admin Remarks</label>
                            <div class="bg-light p-3 rounded text-dark border-start border-4 border-{{ $leave->status === 'approved' ? 'success' : 'danger' }}">
                                {{ $leave->admin_remarks }}
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('portal.leaves.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                        @if($leave->canBeModified())
                            <form action="{{ route('portal.leaves.cancel', $leave) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this leave application?')">
                                    <i class="fa fa-times me-1"></i> Cancel Application
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
