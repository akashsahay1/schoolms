@extends('layouts.app')

@section('title', 'Messages')

@section('page-title', 'Messages')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Messages</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.messaging.inbox.create') }}" class="btn btn-primary w-100 mb-3">
                    <i data-feather="plus" class="me-1"></i> Compose Message
                </a>

                <ul class="nav flex-column nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('filter') ? 'active' : '' }}" href="{{ route('admin.messaging.inbox.index') }}">
                            <i data-feather="inbox" class="me-2" style="width: 16px; height: 16px;"></i> Inbox
                            @if($unreadCount > 0)
                                <span class="badge bg-danger float-end">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') === 'unread' ? 'active' : '' }}" href="{{ route('admin.messaging.inbox.index', ['filter' => 'unread']) }}">
                            <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i> Unread
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') === 'sent' ? 'active' : '' }}" href="{{ route('admin.messaging.inbox.index', ['filter' => 'sent']) }}">
                            <i data-feather="send" class="me-2" style="width: 16px; height: 16px;"></i> Sent
                        </a>
                    </li>
                </ul>

                <hr>

                <div class="d-grid">
                    <a href="{{ route('admin.messaging.inbox.mark-all-read') }}" class="btn btn-outline-secondary btn-sm" onclick="event.preventDefault(); document.getElementById('markAllReadForm').submit();">
                        <i data-feather="check-circle" class="me-1" style="width: 14px; height: 14px;"></i> Mark All Read
                    </a>
                    <form id="markAllReadForm" action="{{ route('admin.messaging.inbox.mark-all-read') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>{{ request('filter') === 'sent' ? 'Sent Messages' : (request('filter') === 'unread' ? 'Unread Messages' : 'Inbox') }}</h5>
                    <form action="{{ route('admin.messaging.inbox.index') }}" method="GET" class="d-flex gap-2">
                        @if(request('filter'))
                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                        @endif
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search messages..." value="{{ request('search') }}" style="width: 200px;">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i data-feather="search" style="width: 14px; height: 14px;"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($messages as $message)
                        @php
                            $isUnread = !$message->is_read && $message->recipient_id === auth()->id();
                            $otherParty = $message->sender_id === auth()->id() ? $message->recipient : $message->sender;
                        @endphp
                        <a href="{{ route('admin.messaging.inbox.show', $message) }}" class="list-group-item list-group-item-action {{ $isUnread ? 'bg-light-primary' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex">
                                    <div class="me-3">
                                        @if($otherParty && $otherParty->photo)
                                            <img src="{{ asset('storage/' . $otherParty->photo) }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                {{ $otherParty ? substr($otherParty->name, 0, 1) : '?' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1 {{ $isUnread ? 'fw-bold' : '' }}">
                                            {{ $message->subject }}
                                            @if($message->replies->count() > 0)
                                                <span class="badge bg-secondary ms-1">{{ $message->replies->count() }}</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            @if($message->sender_id === auth()->id())
                                                <span class="text-primary">To:</span> {{ $otherParty?->name }}
                                            @else
                                                <span class="text-success">From:</span> {{ $otherParty?->name }}
                                            @endif
                                            @if($message->student)
                                                <span class="badge badge-light-info ms-1">{{ $message->student->first_name }} {{ $message->student->last_name }}</span>
                                            @endif
                                        </small>
                                        <p class="mb-0 text-muted small mt-1">{{ Str::limit($message->message, 80) }}</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                    @if($message->attachment)
                                        <br><i data-feather="paperclip" style="width: 12px; height: 12px;"></i>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i data-feather="inbox" class="text-muted mb-3" style="width: 64px; height: 64px;"></i>
                            <p class="text-muted mb-0">No messages found</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @if($messages->hasPages())
                <div class="card-footer">
                    {{ $messages->withQueryString()->links() }}
                </div>
            @endif
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
