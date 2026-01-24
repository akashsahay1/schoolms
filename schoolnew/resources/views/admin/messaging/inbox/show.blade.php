@extends('layouts.app')

@section('title', 'View Message')

@section('page-title', 'View Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.messaging.inbox.index') }}">Messages</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $message->subject }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.messaging.inbox.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="arrow-left" class="me-1"></i> Back
                    </a>
                    <form action="{{ route('admin.messaging.inbox.destroy', $message) }}" method="POST" class="delete-form d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger btn-sm delete-confirm" data-name="{{ $message->subject }}">
                            <i data-feather="trash-2" class="me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <!-- Original Message -->
                <div class="message-thread mb-4">
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            @if($message->sender->photo)
                                <img src="{{ asset('storage/' . $message->sender->photo) }}" alt="" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    {{ substr($message->sender->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-0">{{ $message->sender->name }}</h6>
                                    <small class="text-muted">
                                        To: {{ $message->recipient->name }}
                                        @if($message->student)
                                            | About: <span class="text-primary">{{ $message->student->first_name }} {{ $message->student->last_name }}</span>
                                        @endif
                                    </small>
                                </div>
                                <small class="text-muted">{{ $message->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="message-content bg-light p-4 rounded mb-3">
                        <div style="white-space: pre-wrap;">{{ $message->message }}</div>

                        @if($message->attachment)
                            <div class="mt-3 pt-3 border-top">
                                <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i data-feather="paperclip" class="me-1"></i> View Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Replies -->
                @if($message->replies->count() > 0)
                    <h6 class="mb-3">Replies ({{ $message->replies->count() }})</h6>
                    @foreach($message->replies as $reply)
                        <div class="message-reply mb-4 {{ $reply->sender_id === auth()->id() ? 'ms-4' : '' }}">
                            <div class="d-flex mb-2">
                                <div class="me-3">
                                    @if($reply->sender->photo)
                                        <img src="{{ asset('storage/' . $reply->sender->photo) }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-{{ $reply->sender_id === auth()->id() ? 'success' : 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ substr($reply->sender->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-0 small">
                                                {{ $reply->sender->name }}
                                                @if($reply->sender_id === auth()->id())
                                                    <span class="badge badge-light-success ms-1">You</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <small class="text-muted">{{ $reply->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="message-content bg-{{ $reply->sender_id === auth()->id() ? 'light-success' : 'light' }} p-3 rounded">
                                <div style="white-space: pre-wrap;">{{ $reply->message }}</div>

                                @if($reply->attachment)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i data-feather="paperclip" class="me-1"></i> View Attachment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Reply Form -->
                <div class="reply-form mt-4 pt-4 border-top">
                    <h6 class="mb-3">Send Reply</h6>
                    <form action="{{ route('admin.messaging.inbox.reply', $message) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="4" placeholder="Type your reply..." required></textarea>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <input type="file" name="attachment" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            </div>
                            <div class="col-md-6 text-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="send" class="me-1"></i> Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Message Info -->
        <div class="card">
            <div class="card-header">
                <h5>Message Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted">From</td>
                        <td>
                            {{ $message->sender->name }}
                            <span class="badge badge-light-primary">{{ ucfirst($message->sender_type) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">To</td>
                        <td>
                            {{ $message->recipient->name }}
                            <span class="badge badge-light-info">{{ ucfirst($message->recipient_type) }}</span>
                        </td>
                    </tr>
                    @if($message->student)
                        <tr>
                            <td class="text-muted">Student</td>
                            <td>{{ $message->student->first_name }} {{ $message->student->last_name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Date</td>
                        <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($message->is_read)
                                <span class="badge badge-light-success">Read</span>
                                <small class="text-muted d-block">{{ $message->read_at?->format('M d, Y H:i') }}</small>
                            @else
                                <span class="badge badge-light-warning">Unread</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Replies</td>
                        <td>{{ $message->replies->count() }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($message->student)
            <!-- Student Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Student Info</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($message->student->photo)
                            <img src="{{ asset('storage/' . $message->student->photo) }}" alt="" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                {{ substr($message->student->first_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <table class="table table-borderless small">
                        <tr>
                            <td class="text-muted">Name</td>
                            <td>{{ $message->student->first_name }} {{ $message->student->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Admission No</td>
                            <td>{{ $message->student->admission_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Class</td>
                            <td>{{ $message->student->class?->name }} - {{ $message->student->section?->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
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
