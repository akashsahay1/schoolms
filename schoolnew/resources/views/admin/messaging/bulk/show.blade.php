@extends('layouts.app')

@section('title', 'View Bulk Message')

@section('page-title', 'View Bulk Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.messaging.bulk.index') }}">Bulk Messages</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ $bulkMessage->title }}</h5>
                <span class="badge {{ $bulkMessage->getStatusBadgeClass() }} fs-6">{{ $bulkMessage->getStatusLabel() }}</span>
            </div>
            <div class="card-body">
                <!-- Message Content -->
                <div class="bg-light p-4 rounded mb-4">
                    <h6 class="text-muted mb-2">Message Content</h6>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $bulkMessage->message }}</p>
                </div>

                <!-- Delivery Statistics -->
                <h6 class="mb-3">Delivery Statistics</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light-primary border-0">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1 text-primary">{{ $stats['total'] }}</h3>
                                <small class="text-muted">Total Recipients</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-success border-0">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1 text-success">{{ $stats['sent'] + $stats['delivered'] }}</h3>
                                <small class="text-muted">Sent Successfully</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-danger border-0">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1 text-danger">{{ $stats['failed'] }}</h3>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light-warning border-0">
                            <div class="card-body text-center py-3">
                                <h3 class="mb-1 text-warning">{{ $stats['pending'] }}</h3>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($stats['total'] > 0)
                    <div class="progress mb-4" style="height: 20px;">
                        @php
                            $successRate = (($stats['sent'] + $stats['delivered']) / $stats['total']) * 100;
                            $failedRate = ($stats['failed'] / $stats['total']) * 100;
                            $pendingRate = ($stats['pending'] / $stats['total']) * 100;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $successRate }}%" title="Sent: {{ $successRate }}%">
                            {{ number_format($successRate, 1) }}%
                        </div>
                        <div class="progress-bar bg-danger" style="width: {{ $failedRate }}%" title="Failed: {{ $failedRate }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ $pendingRate }}%" title="Pending: {{ $pendingRate }}%"></div>
                    </div>
                @endif

                <!-- Recent Logs -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Recent Delivery Logs</h6>
                    <a href="{{ route('admin.messaging.bulk.logs', $bulkMessage) }}" class="btn btn-sm btn-outline-primary">
                        View All Logs
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Recipient</th>
                                <th>Channel</th>
                                <th>Status</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bulkMessage->logs->take(10) as $log)
                                <tr>
                                    <td>
                                        {{ $log->recipient_name }}
                                        <small class="text-muted d-block">
                                            @if($log->channel === 'sms')
                                                {{ $log->recipient_phone }}
                                            @elseif($log->channel === 'email')
                                                {{ $log->recipient_email }}
                                            @else
                                                In-App
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ strtoupper($log->channel) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->getStatusBadgeClass() }}">{{ $log->getStatusLabel() }}</span>
                                        @if($log->error_message)
                                            <small class="text-danger d-block">{{ Str::limit($log->error_message, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $log->sent_at ? $log->sent_at->format('M d, H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No delivery logs yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Message Details -->
        <div class="card">
            <div class="card-header">
                <h5>Message Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">Type</td>
                        <td><span class="badge badge-light-primary">{{ $bulkMessage->getMessageTypeLabel() }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Recipients</td>
                        <td>{{ $bulkMessage->getRecipientTypeLabel() }}</td>
                    </tr>
                    @if($bulkMessage->recipient_type === 'class_wise' && $bulkMessage->recipient_filters)
                        <tr>
                            <td class="text-muted">Classes</td>
                            <td>
                                @php
                                    $classIds = $bulkMessage->recipient_filters['class_ids'] ?? [];
                                    $classes = \App\Models\SchoolClass::whereIn('id', $classIds)->pluck('name');
                                @endphp
                                {{ $classes->implode(', ') ?: 'None' }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Created By</td>
                        <td>{{ $bulkMessage->creator?->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Created</td>
                        <td>{{ $bulkMessage->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @if($bulkMessage->scheduled_at)
                        <tr>
                            <td class="text-muted">Scheduled</td>
                            <td>{{ $bulkMessage->scheduled_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endif
                    @if($bulkMessage->sent_at)
                        <tr>
                            <td class="text-muted">Sent At</td>
                            <td>{{ $bulkMessage->sent_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Success Rate</td>
                        <td>
                            <span class="text-{{ $bulkMessage->getSuccessRate() >= 90 ? 'success' : ($bulkMessage->getSuccessRate() >= 70 ? 'warning' : 'danger') }}">
                                {{ $bulkMessage->getSuccessRate() }}%
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(in_array($bulkMessage->status, ['draft', 'scheduled']))
                        <a href="{{ route('admin.messaging.bulk.edit', $bulkMessage) }}" class="btn btn-outline-primary">
                            <i data-feather="edit" class="me-1"></i> Edit Message
                        </a>
                        <form action="{{ route('admin.messaging.bulk.send', $bulkMessage) }}" method="POST" class="send-form">
                            @csrf
                            <button type="button" class="btn btn-primary w-100 send-confirm">
                                <i data-feather="send" class="me-1"></i> Send Now
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.messaging.bulk.logs', $bulkMessage) }}" class="btn btn-outline-secondary">
                        <i data-feather="list" class="me-1"></i> View All Logs
                    </a>
                    @if($bulkMessage->status !== 'sending')
                        <form action="{{ route('admin.messaging.bulk.destroy', $bulkMessage) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline-danger w-100 delete-confirm" data-name="{{ $bulkMessage->title }}">
                                <i data-feather="trash-2" class="me-1"></i> Delete Message
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.messaging.bulk.index') }}" class="btn btn-light">
                        <i data-feather="arrow-left" class="me-1"></i> Back to List
                    </a>
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

    // Send confirmation
    jQuery('.send-confirm').on('click', function(e) {
        e.preventDefault();
        var form = jQuery(this).closest('form');

        Swal.fire({
            title: 'Send Message Now?',
            text: 'This will send the message to all recipients immediately.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7366ff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Send Now'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
