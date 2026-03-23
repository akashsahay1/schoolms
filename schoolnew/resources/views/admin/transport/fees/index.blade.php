@extends('layouts.app')

@section('title', 'Transport Fees')

@section('page-title', 'Transport - Fee Structure')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Transport</li>
    <li class="breadcrumb-item active">Fees</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Info Banner -->
    <div class="alert alert-light border mb-4 py-2 px-3" style="font-size: 13px; border-radius: 8px;">
        <i class="icon-info-alt me-1 text-primary"></i>
        Transport charges are <strong>automatically managed</strong> based on route fare. When you create or update a route, the fee syncs here. Academic fees (Tuition, Exam, Library) are managed separately in <a href="{{ route('admin.fees.structure') }}">Fees → Fee Structure</a>.
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0 fw-bold">Transport Fee Structure</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.transport.fees.collections') }}" class="btn btn-outline-primary">
                        <i class="icon-list me-1"></i> Collections
                    </a>
                    <a href="{{ route('admin.transport.fees.reports') }}" class="btn btn-outline-success">
                        <i class="icon-bar-chart me-1"></i> Reports
                    </a>
                    <a href="{{ route('admin.transport.fees.create') }}" class="btn btn-outline-secondary btn-sm" title="Manually add a fee (for non-standard cases)">
                        <i class="icon-plus me-1"></i> Add Manual
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
                    <i class="icon-check me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 13px; border-radius: 8px;">
                    <i class="icon-alert me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
                </div>
            @endif

            <!-- Filter -->
            <form action="{{ route('admin.transport.fees.index') }}" method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">Route</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 12%;" class="text-end">Amount</th>
                            <th style="width: 10%;" class="text-end">Fine/Day</th>
                            <th style="width: 10%;">Grace</th>
                            <th style="width: 10%;">Due Date</th>
                            <th style="width: 8%;" class="text-center">Status</th>
                            <th style="width: 10%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $fee)
                            <tr>
                                <td class="text-muted">{{ $fees->firstItem() + $loop->index }}</td>
                                <td>
                                    <div style="line-height: 1.3;">
                                        <span class="fw-medium">{{ $fee->route->route_name ?? 'N/A' }}</span>
                                        <br><small class="text-muted">{{ $fee->route->start_place ?? '' }} → {{ $fee->route->end_place ?? '' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary px-2">{{ $fee->fee_type_label }}</span>
                                </td>
                                <td class="text-end fw-bold text-success">₹{{ number_format($fee->amount, 2) }}</td>
                                <td class="text-end">₹{{ number_format($fee->fine_per_day, 2) }}</td>
                                <td>{{ $fee->fine_grace_days }} days</td>
                                <td>{{ $fee->due_date ? $fee->due_date->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ $fee->is_active ? 'success' : 'danger' }} px-2">
                                        {{ $fee->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="common-align gap-2 justify-content-center">
                                        <a class="square-white" href="{{ route('admin.transport.fees.edit', $fee) }}" title="Edit">
                                            <svg>
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#edit-content') }}"></use>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.transport.fees.destroy', $fee) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="square-white trash-7 border-0 bg-transparent p-0 delete-confirm" title="Delete" data-name="{{ $fee->route->route_name ?? 'this fee' }}">
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
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                            <i class="icon-credit-card" style="font-size: 24px; color: #95a5a6;"></i>
                                        </div>
                                        <h6 class="mb-1">No Transport Fees</h6>
                                        <p class="text-muted mb-2">Fees are auto-created when you add routes. <a href="{{ route('admin.transport.routes.index') }}">Manage Routes</a></p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($fees->hasPages())
            <div class="card-footer bg-white">
                {{ $fees->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
