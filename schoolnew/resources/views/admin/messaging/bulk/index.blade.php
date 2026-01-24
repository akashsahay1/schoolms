@extends('layouts.app')

@section('title', 'Bulk Messages')

@section('page-title', 'Bulk Messages')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Bulk Messages</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>All Bulk Messages</h5>
                    <a href="{{ route('admin.messaging.bulk.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> New Bulk Message
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('admin.messaging.bulk.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="message_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="sms" {{ request('message_type') == 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="email" {{ request('message_type') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="notification" {{ request('message_type') == 'notification' ? 'selected' : '' }}>Notification</option>
                                <option value="all" {{ request('message_type') == 'all' ? 'selected' : '' }}>All Channels</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i data-feather="filter" class="me-1"></i> Filter
                            </button>
                        </div>
                        @if(request()->hasAny(['search', 'status', 'message_type']))
                            <div class="col-md-2">
                                <a href="{{ route('admin.messaging.bulk.index') }}" class="btn btn-secondary w-100">Clear</a>
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Messages Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Recipients</th>
                                <th>Sent/Total</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $message)
                                <tr>
                                    <td>{{ $message->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.messaging.bulk.show', $message) }}" class="text-primary">
                                            {{ Str::limit($message->title, 40) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $message->getMessageTypeLabel() }}</span>
                                    </td>
                                    <td>{{ $message->getRecipientTypeLabel() }}</td>
                                    <td>
                                        <span class="text-success">{{ $message->sent_count }}</span> /
                                        <span class="text-muted">{{ $message->total_recipients }}</span>
                                        @if($message->failed_count > 0)
                                            <span class="text-danger">({{ $message->failed_count }} failed)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $message->getStatusBadgeClass() }}">
                                            {{ $message->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.messaging.bulk.show', $message) }}" title="View">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
                                            </a>
                                            @if(in_array($message->status, ['draft', 'scheduled']))
                                                <a class="square-white" href="{{ route('admin.messaging.bulk.edit', $message) }}" title="Edit">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                                </a>
                                                <form action="{{ route('admin.messaging.bulk.send', $message) }}" method="POST" class="d-inline send-form">
                                                    @csrf
                                                    <button type="button" class="square-white border-0 bg-transparent p-0 send-confirm" title="Send Now">
                                                        <svg><use href="{{ asset('assets/svg/icon-sprite.svg#send') }}"></use></svg>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($message->status !== 'sending')
                                                <form action="{{ route('admin.messaging.bulk.destroy', $message) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $message->title }}">
                                                        <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="inbox" class="mb-2" style="width: 48px; height: 48px;"></i>
                                            <p class="mb-0">No bulk messages found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $messages->withQueryString()->links() }}
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
