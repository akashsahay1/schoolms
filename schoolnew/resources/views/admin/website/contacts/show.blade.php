@extends('layouts.app')

@section('title', 'View Message')

@section('page-title', 'Website - View Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.contacts') }}">Contact Messages</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Contact Message</h5>
                    <span class="badge {{ $contact->getStatusBadgeClass() }}">{{ $contact->getStatusLabel() }}</span>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Subject</h6>
                    <h5>{{ $contact->subject }}</h5>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted mb-2">Message</h6>
                    <div class="bg-light p-3 rounded">
                        {{ $contact->message }}
                    </div>
                </div>

                @if($contact->reply)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Your Reply <small>({{ $contact->replied_at->format('M d, Y h:i A') }})</small></h6>
                        <div class="bg-success bg-opacity-10 p-3 rounded border-start border-success border-3">
                            {{ $contact->reply }}
                        </div>
                    </div>
                @endif

                @if(!$contact->isReplied())
                    <hr class="my-4">
                    <h6 class="mb-3">Send Reply</h6>
                    <form action="{{ route('admin.website.contacts.reply', $contact) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="reply" class="form-control" rows="4" placeholder="Type your reply here..." required>{{ old('reply') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="send" class="me-1"></i> Send Reply
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Sender Details</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <span class="text-muted d-block small">Name</span>
                        <strong>{{ $contact->name }}</strong>
                    </li>
                    <li class="mb-3">
                        <span class="text-muted d-block small">Email</span>
                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                    </li>
                    @if($contact->phone)
                        <li class="mb-3">
                            <span class="text-muted d-block small">Phone</span>
                            <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                        </li>
                    @endif
                    <li>
                        <span class="text-muted d-block small">Received</span>
                        <strong>{{ $contact->created_at->format('M d, Y h:i A') }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <a href="{{ route('admin.website.contacts') }}" class="btn btn-outline-secondary w-100 mt-3">
            <i data-feather="arrow-left" class="me-1"></i> Back to Messages
        </a>
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
