@extends('layouts.app')

@section('title', 'SMS Templates')

@section('page-title', 'Settings - SMS Templates')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.sms') }}">SMS Settings</a></li>
    <li class="breadcrumb-item active">Templates</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>SMS Templates</h5>
                    <a href="{{ route('admin.settings.sms.templates.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Template
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filters -->
                <form action="{{ route('admin.settings.sms.templates') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search templates..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Content</th>
                                <th>Status</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td>{{ $templates->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $template->name }}</strong>
                                        <br><small class="text-muted">{{ $template->slug }}</small>
                                    </td>
                                    <td><span class="badge badge-light-primary">{{ $template->category_label }}</span></td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($template->content, 100) }}</small>
                                    </td>
                                    <td>
                                        @if($template->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.settings.sms.templates.edit', $template) }}" title="Edit">
                                                <svg>
                                                    <use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.settings.sms.templates.destroy', $template) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $template->name }}">
                                                    <svg>
                                                        <use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted mb-0">No templates found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($templates->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $templates->withQueryString()->links() }}
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
