@extends('layouts.app')

@section('title', 'Message Delivery Logs')

@section('page-title', 'Delivery Logs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.messaging.bulk.index') }}">Bulk Messages</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.messaging.bulk.show', $bulkMessage) }}">{{ Str::limit($bulkMessage->title, 20) }}</a></li>
    <li class="breadcrumb-item active">Logs</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Delivery Logs</h5>
                        <small class="text-muted">{{ $bulkMessage->title }}</small>
                    </div>
                    <a href="{{ route('admin.messaging.bulk.show', $bulkMessage) }}" class="btn btn-outline-primary">
                        <i data-feather="arrow-left" class="me-1"></i> Back to Message
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.messaging.bulk.logs', $bulkMessage) }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="channel" class="form-select">
                                <option value="">All Channels</option>
                                <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="notification" {{ request('channel') == 'notification' ? 'selected' : '' }}>Notification</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="filter" class="me-1"></i> Filter
                            </button>
                        </div>
                        @if(request()->hasAny(['status', 'channel']))
                            <div class="col-md-2">
                                <a href="{{ route('admin.messaging.bulk.logs', $bulkMessage) }}" class="btn btn-secondary w-100">Clear</a>
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Logs Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Recipient</th>
                                <th>Contact</th>
                                <th>Channel</th>
                                <th>Status</th>
                                <th>Error</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        {{ $log->recipient_name }}
                                        @if($log->user)
                                            <small class="text-muted d-block">User #{{ $log->user_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->channel === 'sms')
                                            <i data-feather="phone" class="me-1" style="width: 14px; height: 14px;"></i>
                                            {{ $log->recipient_phone ?? '-' }}
                                        @elseif($log->channel === 'email')
                                            <i data-feather="mail" class="me-1" style="width: 14px; height: 14px;"></i>
                                            {{ $log->recipient_email ?? '-' }}
                                        @else
                                            <i data-feather="bell" class="me-1" style="width: 14px; height: 14px;"></i>
                                            In-App Notification
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ strtoupper($log->channel) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->getStatusBadgeClass() }}">{{ $log->getStatusLabel() }}</span>
                                    </td>
                                    <td>
                                        @if($log->error_message)
                                            <span class="text-danger" title="{{ $log->error_message }}">
                                                {{ Str::limit($log->error_message, 40) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i:s') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="inbox" class="mb-2" style="width: 48px; height: 48px;"></i>
                                            <p class="mb-0">No delivery logs found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->withQueryString()->links() }}
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
