@extends('layouts.portal')

@section('title', 'Message Details')
@section('page-title', 'Message Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.contact') }}">Contact School</a></li>
    <li class="breadcrumb-item active">Message</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Message Details</h5>
                        <span class="badge {{ $message->getStatusBadgeClass() }} fs-6">{{ $message->getStatusLabel() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Category</label>
                            <p class="fw-medium mb-0">{{ $message->getCategoryLabel() }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Priority</label>
                            <p class="mb-0"><span class="badge {{ $message->getPriorityBadgeClass() }}">{{ $message->getPriorityLabel() }}</span></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Subject</label>
                        <p class="fw-medium mb-0">{{ $message->subject }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted">Your Message</label>
                        <div class="bg-light p-3 rounded text-dark">
                            {{ $message->message }}
                        </div>
                        <small class="text-muted">Sent on {{ $message->created_at->format('F d, Y h:i A') }}</small>
                    </div>

                    @if($message->admin_response)
                        <hr>
                        <div class="mb-4">
                            <label class="text-muted">School's Response</label>
                            <div class="bg-success bg-opacity-10 p-3 rounded border-start border-4 border-success text-dark">
                                {{ $message->admin_response }}
                            </div>
                            @if($message->responded_at)
                                <small class="text-muted">Responded on {{ $message->responded_at->format('F d, Y h:i A') }}</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-light-info text-dark">
                            <i class="fa fa-info-circle me-2"></i>
                            Your message is being reviewed. We will respond soon.
                        </div>
                    @endif

                    <hr>

                    <a href="{{ route('portal.contact') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Back to Messages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
