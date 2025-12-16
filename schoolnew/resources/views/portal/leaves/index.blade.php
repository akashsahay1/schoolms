@extends('layouts.portal')

@section('title', 'Leave Applications')
@section('page-title', 'Leave Applications')

@section('breadcrumb')
    <li class="breadcrumb-item active">Leave Applications</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <form method="GET" action="{{ route('portal.leaves.index') }}" class="d-flex gap-2">
                            <select name="status" class="form-select" style="width: auto;">
                                <option value="">All Status</option>
                                @foreach(\App\Models\LeaveApplication::STATUSES as $key => $label)
                                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-primary">Filter</button>
                        </form>
                        <a href="{{ route('portal.leaves.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Apply for Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Applications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($leaves->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Applied On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaves as $leave)
                                        <tr>
                                            <td>{{ $leave->getLeaveTypeLabel() }}</td>
                                            <td>{{ $leave->from_date->format('M d, Y') }}</td>
                                            <td>{{ $leave->to_date->format('M d, Y') }}</td>
                                            <td>{{ $leave->total_days }}</td>
                                            <td>
                                                <span class="badge {{ $leave->getStatusBadgeClass() }}">{{ $leave->getStatusLabel() }}</span>
                                            </td>
                                            <td>{{ $leave->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('portal.leaves.show', $leave) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if($leave->canBeModified())
                                                    <form action="{{ route('portal.leaves.cancel', $leave) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this leave application?')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $leaves->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-file-text-o fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Leave Applications</h5>
                            <p class="text-muted">You haven't applied for any leave yet.</p>
                            <a href="{{ route('portal.leaves.create') }}" class="btn btn-primary">Apply for Leave</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
