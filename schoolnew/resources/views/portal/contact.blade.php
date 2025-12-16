@extends('layouts.portal')

@section('title', 'Contact School')
@section('page-title', 'Contact School')

@section('breadcrumb')
    <li class="breadcrumb-item active">Contact School</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Contact Form -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Send a Message</h5>
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

                    <form action="{{ route('portal.contact.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required placeholder="Brief subject of your message">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" required placeholder="Please describe your query or concern in detail...">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-paper-plane me-1"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Previous Messages -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>My Messages</h5>
                </div>
                <div class="card-body">
                    @if($messages->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($messages as $message)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge {{ $message->getStatusBadgeClass() }}">{{ $message->getStatusLabel() }}</span>
                                            <span class="badge {{ $message->getPriorityBadgeClass() }} ms-1">{{ $message->getPriorityLabel() }}</span>
                                            <h6 class="mb-1 mt-2">{{ $message->subject }}</h6>
                                            <small class="text-muted">{{ $message->created_at->format('M d, Y h:i A') }}</small>
                                        </div>
                                        <a href="{{ route('portal.contact.show', $message) }}" class="btn btn-sm btn-outline-info">
                                            View
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-3">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-envelope-o fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No messages yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
