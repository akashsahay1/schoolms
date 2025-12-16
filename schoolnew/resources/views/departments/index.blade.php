@extends('layouts.app')

@section('title', 'Departments')

@section('page-title', 'Departments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Departments</li>
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
                    <h5>All Departments</h5>
                    @can('create staff')
                        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add Department
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
                            @forelse($departments as $department)
                                <tr>
                                    <td>{{ $departments->firstItem() + $loop->index }}</td>
                                    <td><strong>{{ $department->name }}</strong></td>
                                    <td>{{ Str::limit($department->description, 50) ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-light-info">{{ $department->staff_count }} staff</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $department->is_active ? 'success' : 'danger' }}">
                                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            @can('edit staff')
                                                <a class="square-white" href="{{ route('admin.departments.edit', $department) }}" title="Edit">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                                </a>
                                            @endcan
                                            @can('delete staff')
                                                <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $department->name }}">
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
                                            <i data-feather="briefcase" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2 mb-0">No departments found.</p>
                                            @can('create staff')
                                                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary mt-3">Add First Department</a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $departments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
