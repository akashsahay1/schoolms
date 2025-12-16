@extends('layouts.app')

@section('title', 'Designations')

@section('page-title', 'Designations')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Designations</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>All Designations</h5>
                    @can('create staff')
                        <a href="{{ route('admin.designations.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add Designation
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Staff Count</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($designations as $designation)
                                <tr>
                                    <td>{{ $designations->firstItem() + $loop->index }}</td>
                                    <td><strong>{{ $designation->name }}</strong></td>
                                    <td>{{ Str::limit($designation->description, 50) ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-light-info">{{ $designation->staff_count }} staff</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $designation->is_active ? 'success' : 'danger' }}">
                                            {{ $designation->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            @can('edit staff')
                                                <a class="square-white" href="{{ route('admin.designations.edit', $designation) }}" title="Edit">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                                </a>
                                            @endcan
                                            @can('delete staff')
                                                <form action="{{ route('admin.designations.destroy', $designation) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $designation->name }}">
                                                        <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="award" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2 mb-0">No designations found.</p>
                                            @can('create staff')
                                                <a href="{{ route('admin.designations.create') }}" class="btn btn-primary mt-3">Add First Designation</a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $designations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
