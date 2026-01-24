@extends('layouts.app')

@section('title', 'Website Pages')

@section('page-title', 'Website - Pages')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.website.index') }}">Website</a></li>
    <li class="breadcrumb-item active">Pages</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Website Pages</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Title</th>
                                <th>URL</th>
                                <th>Status</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td><strong>{{ ucfirst($page->slug) }}</strong></td>
                                    <td>{{ $page->title }}</td>
                                    <td>
                                        <a href="{{ route('website.' . ($page->slug === 'home' ? 'home' : $page->slug)) }}" target="_blank" class="text-primary">
                                            /{{ $page->slug === 'home' ? '' : $page->slug }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($page->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.website.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="edit-2" style="width: 14px;"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">No pages found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
});
</script>
@endpush
