@extends('layouts.app')

@section('title', 'Transport Fees')

@section('page-title', 'Transport - Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.vehicles.index') }}">Transport</a></li>
    <li class="breadcrumb-item active">Fees</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Transport Fee Structure</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.transport.fees.collections') }}" class="btn btn-outline-primary">
                            <i data-feather="list" class="me-1"></i> Collections
                        </a>
                        <a href="{{ route('admin.transport.fees.reports') }}" class="btn btn-outline-success">
                            <i data-feather="bar-chart-2" class="me-1"></i> Reports
                        </a>
                        <a href="{{ route('admin.transport.fees.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add Fee
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filter -->
                <form action="{{ route('admin.transport.fees.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Route</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Fine/Day</th>
                                <th>Grace Days</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fees as $fee)
                                <tr>
                                    <td>{{ $fees->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $fee->route->title }}</strong>
                                        <br><small class="text-muted">{{ $fee->route->start_point }} - {{ $fee->route->end_point }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $fee->fee_type_label }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">₹{{ number_format($fee->amount, 2) }}</strong>
                                    </td>
                                    <td>₹{{ number_format($fee->fine_per_day, 2) }}</td>
                                    <td>{{ $fee->fine_grace_days }} days</td>
                                    <td>{{ $fee->due_date ? $fee->due_date->format('M d, Y') : '-' }}</td>
                                    <td>
                                        @if($fee->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="common-align gap-2 justify-content-start">
                                            <a class="square-white" href="{{ route('admin.transport.fees.edit', $fee) }}" title="Edit">
                                                <svg>
                                                    <use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.transport.fees.destroy', $fee) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $fee->route->title }}">
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
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="credit-card" class="mb-2" style="width: 48px; height: 48px;"></i>
                                            <p>No transport fees defined.</p>
                                            <a href="{{ route('admin.transport.fees.create') }}" class="btn btn-primary btn-sm">Add First Fee</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($fees->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $fees->withQueryString()->links() }}
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
