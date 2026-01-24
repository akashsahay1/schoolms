@extends('layouts.app')

@section('title', 'Compose Message')

@section('page-title', 'Compose Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.messaging.inbox.index') }}">Messages</a></li>
    <li class="breadcrumb-item active">Compose</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>{{ $replyTo ? 'Reply to Message' : 'New Message' }}</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($replyTo)
                    <div class="alert alert-light mb-4">
                        <h6 class="mb-2">Replying to:</h6>
                        <p class="mb-1"><strong>{{ $replyTo->subject }}</strong></p>
                        <small class="text-muted">From: {{ $replyTo->sender->name }} - {{ $replyTo->created_at->format('M d, Y H:i') }}</small>
                        <p class="mb-0 mt-2">{{ Str::limit($replyTo->message, 200) }}</p>
                    </div>
                @endif

                <form action="{{ route('admin.messaging.inbox.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if($replyTo)
                        <input type="hidden" name="parent_message_id" value="{{ $replyTo->id }}">
                        <input type="hidden" name="recipient_id" value="{{ $replyTo->sender_id === auth()->id() ? $replyTo->recipient_id : $replyTo->sender_id }}">
                        <input type="hidden" name="student_id" value="{{ $replyTo->student_id }}">
                    @else
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Recipient <span class="text-danger">*</span></label>
                                <select name="recipient_id" class="form-select" id="recipientSelect" required>
                                    <option value="">-- Select Recipient --</option>
                                    @if($teachers->count() > 0)
                                        <optgroup label="Teachers">
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ old('recipient_id') == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($parents->count() > 0)
                                        <optgroup label="Parents">
                                            @foreach($parents as $parent)
                                                <option value="{{ $parent->id }}" {{ old('recipient_id') == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($staff->count() > 0)
                                        <optgroup label="Staff/Admin">
                                            @foreach($staff as $member)
                                                <option value="{{ $member->id }}" {{ old('recipient_id') == $member->id ? 'selected' : '' }}>
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Related Student (Optional)</label>
                                <select name="student_id" class="form-select">
                                    <option value="">-- Select Student --</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select if this message is about a specific student</small>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject', $replyTo ? 'Re: ' . $replyTo->subject : '') }}" placeholder="Enter message subject" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="8" placeholder="Type your message here..." required>{{ old('message') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="send" class="me-1"></i> Send Message
                        </button>
                        <a href="{{ route('admin.messaging.inbox.index') }}" class="btn btn-light">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                        Messages are private between sender and recipient
                    </li>
                    <li class="mb-2">
                        <i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                        Select a student if the message is about a specific student's progress
                    </li>
                    <li class="mb-2">
                        <i data-feather="info" class="text-info me-2" style="width: 16px; height: 16px;"></i>
                        Attachments are limited to 5MB
                    </li>
                    <li class="mb-2">
                        <i data-feather="alert-circle" class="text-warning me-2" style="width: 16px; height: 16px;"></i>
                        For urgent matters, please contact the school office directly
                    </li>
                </ul>
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
