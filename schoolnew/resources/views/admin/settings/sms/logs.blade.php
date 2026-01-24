@extends('layouts.app')

@section('title', 'SMS Logs')

@section('page-title', 'Settings - SMS Logs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.sms') }}">SMS Settings</a></li>
    <li class="breadcrumb-item active">Logs</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>SMS Logs</h5>
                    <a href="{{ route('admin.settings.sms') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="me-1"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.settings.sms.logs') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by phone or name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="message_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($messageTypes as $key => $label)
                                    <option value="{{ $key }}" {{ request('message_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.settings.sms.logs') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Recipient</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Sent By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $logs->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $log->recipient_phone }}</strong>
                                        @if($log->recipient_name)
                                            <br><small class="text-muted">{{ $log->recipient_name }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-light-secondary">{{ $log->message_type_label }}</span></td>
                                    <td>
                                        <small>{{ Str::limit($log->message, 80) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->getStatusBadgeClass() }}">{{ $log->getStatusLabel() }}</span>
                                        @if($log->status === 'failed' && $log->error_message)
                                            <br><small class="text-danger">{{ Str::limit($log->error_message, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $log->sent_at ? $log->sent_at->format('M d, Y h:i A') : '-' }}</td>
                                    <td>{{ $log->sender->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No SMS logs found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $logs->withQueryString()->links() }}
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
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
