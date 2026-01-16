@extends('layouts.admin')

@section('title', 'Outstanding Fees Report')
@section('page-title', 'Outstanding Fees Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
    <li class="breadcrumb-item active">Outstanding Report</li>
@endsection

@push('css')
<style>
    .progress-bar-animated {
        animation: progress-animation 1.5s ease-in-out;
    }
    @keyframes progress-animation {
        0% { width: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.outstanding') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="hide_paid" class="form-check-input" id="hidePaid" value="1" {{ request('hide_paid') ? 'checked' : '' }}>
                        <label class="form-check-label" for="hidePaid">Hide Fully Paid Students</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.reports.outstanding') }}" class="btn btn-light">Reset</a>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <a href="{{ route('admin.fees.reports.export', ['type' => 'outstanding'] + request()->only(['class_id', 'hide_paid'])) }}" class="btn btn-sm btn-outline-secondary" title="Export CSV">
                            <i data-feather="file-text" style="width: 14px; height: 14px;"></i> CSV
                        </a>
                        <a href="{{ route('admin.fees.reports.export-excel', ['type' => 'outstanding'] + request()->only(['class_id', 'hide_paid'])) }}" class="btn btn-sm btn-outline-success" title="Export Excel">
                            <i data-feather="file" style="width: 14px; height: 14px;"></i> Excel
                        </a>
                        <a href="{{ route('admin.fees.reports.export-pdf', ['type' => 'outstanding'] + request()->only(['class_id', 'hide_paid'])) }}" class="btn btn-sm btn-outline-danger" title="Export PDF">
                            <i data-feather="file-minus" style="width: 14px; height: 14px;"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Outstanding Card -->
    <div class="card bg-danger text-white mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i data-feather="alert-triangle" class="text-white" style="width: 32px; height: 32px;"></i>
                    </div>
                </div>
                <div class="col">
                    <h6 class="text-white-50 mb-0">Total Outstanding Amount</h6>
                    <h2 class="mb-0">{{ number_format($totalOutstanding, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student-wise Outstanding -->
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Student-wise Outstanding</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th>Class</th>
                                    <th class="text-end">Total Fee</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Outstanding</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outstandingData as $data)
                                    <tr>
                                        <td>{{ $data['student']->full_name }}</td>
                                        <td>{{ $data['student']->admission_no }}</td>
                                        <td>{{ $data['student']->schoolClass->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($data['total_fee'], 2) }}</td>
                                        <td class="text-end text-success">{{ number_format($data['paid_amount'], 2) }}</td>
                                        <td class="text-end">
                                            @if($data['outstanding'] > 0)
                                                <span class="fw-bold text-danger">{{ number_format($data['outstanding'], 2) }}</span>
                                            @else
                                                <span class="badge bg-success">Paid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($data['outstanding'] > 0)
                                                <a href="{{ route('admin.fees.collect', $data['student']) }}" class="btn btn-sm btn-primary">
                                                    <i data-feather="credit-card" style="width: 14px; height: 14px;"></i> Collect
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No students found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($total > $perPage)
                    <div class="card-footer">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                @for($i = 1; $i <= ceil($total / $perPage); $i++)
                                    <li class="page-item {{ $page == $i ? 'active' : '' }}">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>

        <!-- Class-wise Summary -->
        <div class="col-xl-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Class-wise Summary</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Class</th>
                                    <th class="text-end">Outstanding</th>
                                    <th style="width: 120px;">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classSummary as $summary)
                                    @if($summary['total_fee'] > 0)
                                        <tr>
                                            <td>{{ $summary['class']->name }}</td>
                                            <td class="text-end">
                                                <span class="fw-bold {{ $summary['outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($summary['outstanding'], 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar progress-bar-animated {{ $summary['percentage'] >= 80 ? 'bg-success' : ($summary['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                         role="progressbar"
                                                         style="width: {{ $summary['percentage'] }}%">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $summary['percentage'] }}% paid</small>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
