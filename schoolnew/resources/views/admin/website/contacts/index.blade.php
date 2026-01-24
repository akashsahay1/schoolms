@extends('layouts.app')

@section('title', 'Contact Messages')

@section('page-title', 'Website - Contact Messages')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Contact Messages</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Contact Messages</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Filters -->
                <form action="{{ route('admin.website.contacts') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, subject..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.website.contacts') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Received</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr class="{{ $contact->status === 'new' ? 'table-light fw-bold' : '' }}">
                                    <td>{{ $contacts->firstItem() + $loop->index }}</td>
                                    <td>{{ $contact->name }}</td>
                                    <td>{{ $contact->email }}</td>
                                    <td>{{ Str::limit($contact->subject, 40) }}</td>
                                    <td>
                                        <span class="badge {{ $contact->getStatusBadgeClass() }}">{{ $contact->getStatusLabel() }}</span>
                                    </td>
                                    <td>{{ $contact->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.website.contacts.show', $contact) }}" class="btn btn-sm btn-outline-primary">
                                                <i data-feather="eye" style="width: 14px;"></i>
                                            </a>
                                            <form action="{{ route('admin.website.contacts.destroy', $contact) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-confirm" data-name="this message">
                                                    <i data-feather="trash-2" style="width: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No contact messages found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($contacts->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $contacts->withQueryString()->links() }}
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
