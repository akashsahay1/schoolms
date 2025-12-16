@extends('layouts.app')

@section('title', 'Manage Periods')

@section('page-title', 'Manage Periods')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
    <li class="breadcrumb-item active">Periods</li>
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
                    <h5>All Periods</h5>
                    <a href="{{ route('admin.timetable.periods.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Add Period
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Name</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periods as $period)
                                <tr>
                                    <td>{{ $period->order }}</td>
                                    <td><strong>{{ $period->name }}</strong></td>
                                    <td>{{ $period->start_time->format('g:i A') }} - {{ $period->end_time->format('g:i A') }}</td>
                                    <td>
                                        <span class="badge badge-light-{{ $period->type == 'class' ? 'primary' : ($period->type == 'break' ? 'warning' : 'info') }}">
                                            {{ ucfirst($period->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $period->is_active ? 'success' : 'danger' }}">
                                            {{ $period->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.timetable.periods.edit', $period) }}" title="Edit">
                                                <svg><use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use></svg>
                                            </a>
                                            <form action="{{ route('admin.timetable.periods.destroy', $period) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $period->name }}">
                                                    <svg><use href="{{ asset('assets/svg/icon-sprite.svg#trash1') }}"></use></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="clock" style="width: 48px; height: 48px;"></i>
                                            <p class="mt-2 mb-0">No periods found.</p>
                                            <a href="{{ route('admin.timetable.periods.create') }}" class="btn btn-primary mt-3">Add First Period</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $periods->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
