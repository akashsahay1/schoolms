@extends('layouts.app')

@section('title', 'Transport Fee Reports')

@section('page-title', 'Transport - Fee Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.fees.index') }}">Transport Fees</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@push('styles')
<style>
    .transport-report-stat {
        border: none;
        border-radius: 14px;
        transition: transform 0.15s ease;
    }
    .transport-report-stat:hover {
        transform: translateY(-2px);
    }
    .transport-report-stat .card-body {
        padding: 1.25rem 1.5rem;
    }
    .transport-report-stat .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .transport-report-stat .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #7f8c8d;
        margin-bottom: 2px;
        font-weight: 500;
    }
    .transport-report-stat .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.2;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Filter Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.transport.fees.reports') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        @foreach($availableMonths as $month)
                            <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Route</label>
                    <select name="route_id" class="form-select">
                        <option value="">All Routes</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->route_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="icon-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['month', 'route_id']))
                            <a href="{{ route('admin.transport.fees.reports', ['academic_year_id' => $selectedYear]) }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="icon-reload"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-xl col-md-4">
            <div class="card transport-report-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(52, 152, 219, 0.08);">
                            <i class="icon-user" style="font-size: 22px; color: #3498db;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Students</div>
                            <div class="stat-value text-primary">{{ $stats['total_students'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4">
            <div class="card transport-report-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(230, 126, 34, 0.08);">
                            <i class="icon-receipt" style="font-size: 22px; color: #e67e22;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Due</div>
                            <div class="stat-value" style="color: #e67e22;">₹{{ number_format($stats['total_due'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4">
            <div class="card transport-report-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(39, 174, 96, 0.08);">
                            <i class="icon-check" style="font-size: 22px; color: #27ae60;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Collected</div>
                            <div class="stat-value text-success">₹{{ number_format($stats['total_collected'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4">
            <div class="card transport-report-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(231, 76, 60, 0.08);">
                            <i class="icon-time" style="font-size: 22px; color: #e74c3c;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Pending</div>
                            <div class="stat-value text-danger">{{ $stats['total_pending'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4">
            <div class="card transport-report-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(115, 102, 255, 0.08);">
                            <i class="icon-bar-chart" style="font-size: 22px; color: #7366ff;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Collection %</div>
                            <div class="stat-value" style="color: #7366ff;">{{ $stats['collection_pct'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Route-wise Summary -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Route-wise Collection Summary</h6>
                        <a href="{{ route('admin.transport.fees.export-collections', ['academic_year_id' => $selectedYear] + request()->only(['month', 'route_id'])) }}" class="btn btn-sm btn-outline-success" title="Export Excel">
                            <i class="icon-download me-1"></i> Excel
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 25%;">Route</th>
                                    <th style="width: 10%;" class="text-center">Students</th>
                                    <th style="width: 12%;" class="text-end">Fare</th>
                                    <th style="width: 14%;" class="text-end">Due</th>
                                    <th style="width: 14%;" class="text-end">Collected</th>
                                    <th style="width: 10%;" class="text-center">Paid</th>
                                    <th style="width: 15%;">Collection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandDue = 0; $grandCollected = 0; @endphp
                                @forelse($routeSummary as $summary)
                                    @if($summary['students_count'] > 0 || $summary['total_due'] > 0)
                                    @php
                                        $grandDue += $summary['total_due'];
                                        $grandCollected += $summary['total_collected'];
                                        $rate = $summary['total_due'] > 0 ? ($summary['total_collected'] / $summary['total_due']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="line-height: 1.3;">
                                                <span class="fw-medium">{{ $summary['route']->route_name }}</span>
                                                <br><small class="text-muted">{{ $summary['route']->start_place }} → {{ $summary['route']->end_place }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light-primary px-2">{{ $summary['students_count'] }}</span>
                                        </td>
                                        <td class="text-end">₹{{ number_format($summary['fare'], 2) }}</td>
                                        <td class="text-end fw-medium">₹{{ number_format($summary['total_due'], 2) }}</td>
                                        <td class="text-end fw-bold text-success">₹{{ number_format($summary['total_collected'], 2) }}</td>
                                        <td class="text-center">
                                            @if($summary['paid_count'] > 0)
                                                <span class="badge badge-light-success px-2">{{ $summary['paid_count'] }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-fill" style="height: 8px; border-radius: 4px;">
                                                    <div class="progress-bar bg-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}" style="width: {{ min($rate, 100) }}%; border-radius: 4px;"></div>
                                                </div>
                                                <small class="fw-bold" style="min-width: 40px;">{{ number_format($rate, 0) }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                                    <i class="icon-direction-alt" style="font-size: 24px; color: #95a5a6;"></i>
                                                </div>
                                                <p class="text-muted mb-0">No transport fees generated for this period.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($grandDue > 0)
                                <tfoot class="bg-light">
                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td class="text-center">{{ $routeSummary->sum('students_count') }}</td>
                                        <td></td>
                                        <td class="text-end">₹{{ number_format($grandDue, 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format($grandCollected, 2) }}</td>
                                        <td class="text-center">{{ $routeSummary->sum('paid_count') }}</td>
                                        <td>
                                            @php $overallRate = $grandDue > 0 ? ($grandCollected / $grandDue) * 100 : 0; @endphp
                                            <span class="fw-bold">{{ number_format($overallRate, 1) }}%</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Monthly Collection Trend</h6>
                </div>
                <div class="card-body p-0">
                    @if($monthlyTrend->count() > 0)
                        <div class="table-responsive" style="max-height: 450px;">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-center">Txns</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyTrend as $trend)
                                        <tr>
                                            <td>
                                                @if($trend->month)
                                                    {{ \Carbon\Carbon::parse($trend->month . '-01')->format('M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light-primary px-2">{{ $trend->count }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">₹{{ number_format($trend->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if($monthlyTrend->count() > 1)
                                    <tfoot class="bg-light">
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td class="text-center">{{ $monthlyTrend->sum('count') }}</td>
                                            <td class="text-end text-success">₹{{ number_format($monthlyTrend->sum('total'), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="icon-bar-chart" style="font-size: 24px; color: #95a5a6;"></i>
                            </div>
                            <p class="text-muted mb-0">No collection data yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
