@extends('layouts.app')

@section('title', 'Financial Analytics Dashboard')
@section('page-title', 'Fee Reports & Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Fee Reports</li>
@endsection

@push('styles')
<style>
    .stat-card {
        border-radius: 10px;
        height: 100%;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .stat-card .card-body {
        padding: 1.5rem;
    }
    .stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-card .stat-icon svg {
        width: 24px;
        height: 24px;
    }
    .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 0.25rem;
    }
    .stat-card .stat-label {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .stat-card small.text-muted {
        font-size: 0.75rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .report-link-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    .report-link-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    /* Bootstrap subtle color fallbacks */
    .bg-success-subtle { background-color: rgba(101, 193, 92, 0.15) !important; }
    .bg-danger-subtle { background-color: rgba(252, 86, 74, 0.15) !important; }
    .bg-primary-subtle { background-color: rgba(115, 102, 255, 0.15) !important; }
    .bg-info-subtle { background-color: rgba(64, 184, 245, 0.15) !important; }
    .bg-warning-subtle { background-color: rgba(255, 184, 41, 0.15) !important; }
    .bg-secondary-subtle { background-color: rgba(131, 131, 131, 0.15) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Collected</div>
                            <div class="stat-value text-success">{{ number_format($stats['total_collected'], 2) }}</div>
                            <small class="text-muted">{{ $activeYear->name ?? 'All Years' }}</small>
                        </div>
                        <div class="stat-icon bg-success-subtle">
                            <span class="text-success" style="font-size: 20px; font-weight: bold;">₹</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Outstanding</div>
                            <div class="stat-value text-danger">{{ number_format($stats['total_outstanding'], 2) }}</div>
                            <small class="text-muted">Pending Fees</small>
                        </div>
                        <div class="stat-icon bg-danger-subtle">
                            <i data-feather="alert-circle" class="text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">This Month</div>
                            <div class="stat-value text-primary">{{ number_format($stats['this_month'], 2) }}</div>
                            <small class="text-muted">{{ now()->format('F Y') }}</small>
                        </div>
                        <div class="stat-icon bg-primary-subtle">
                            <i data-feather="calendar" class="text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Today's Collection</div>
                            <div class="stat-value text-info">{{ number_format($stats['today'], 2) }}</div>
                            <small class="text-muted">{{ now()->format('d M Y') }}</small>
                        </div>
                        <div class="stat-icon bg-info-subtle">
                            <i data-feather="trending-up" class="text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Discount Given</div>
                            <div class="stat-value text-warning">{{ number_format($stats['total_discount'], 2) }}</div>
                            <small class="text-muted">{{ $activeYear->name ?? 'All Years' }}</small>
                        </div>
                        <div class="stat-icon bg-warning-subtle">
                            <i data-feather="percent" class="text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Fine Collected</div>
                            <div class="stat-value text-secondary">{{ number_format($stats['total_fine'], 2) }}</div>
                            <small class="text-muted">Late payment fines</small>
                        </div>
                        <div class="stat-icon bg-secondary-subtle">
                            <i data-feather="clock" class="text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Active Students</div>
                            <div class="stat-value text-dark">{{ number_format($stats['total_students']) }}</div>
                            <small class="text-muted">Currently enrolled</small>
                        </div>
                        <div class="stat-icon bg-light">
                            <i data-feather="users" class="text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Collection Chart -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 py-1">Monthly Fee Collection (Last 12 Months)</h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary chart-type-btn active" data-type="bar">Bar</button>
                            <button type="button" class="btn btn-outline-primary chart-type-btn" data-type="line">Line</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyCollectionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-4">
        <!-- Fee Type Distribution -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0 py-1">Collection by Fee Type</h6>
                </div>
                <div class="card-body">
                    @if($feeTypeData->count() > 0)
                        <div class="chart-container">
                            <canvas id="feeTypeChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="pie-chart" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
                            <p class="text-muted mb-0">No fee collection data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Class-wise Collection -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0 py-1">Class-wise Collection</h6>
                </div>
                <div class="card-body">
                    @if($classWiseData->count() > 0)
                        <div class="chart-container">
                            <canvas id="classWiseChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="bar-chart-2" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
                            <p class="text-muted mb-0">No class-wise collection data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports Links -->
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">Quick Reports</h6>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.fees.reports.collection') }}" class="card report-link-card h-100 text-decoration-none">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="file-text" class="text-primary"></i>
                    </div>
                    <h6 class="mb-0 text-dark">Collection Report</h6>
                    <small class="text-muted">Date wise collection</small>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.fees.reports.outstanding') }}" class="card report-link-card h-100 text-decoration-none">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-danger-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="alert-triangle" class="text-danger"></i>
                    </div>
                    <h6 class="mb-0 text-dark">Outstanding Report</h6>
                    <small class="text-muted">Pending fees</small>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.fees.reports.fee-type-wise') }}" class="card report-link-card h-100 text-decoration-none">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="layers" class="text-success"></i>
                    </div>
                    <h6 class="mb-0 text-dark">Fee Type Wise</h6>
                    <small class="text-muted">By fee category</small>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.fees.reports.class-wise') }}" class="card report-link-card h-100 text-decoration-none">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="grid" class="text-warning"></i>
                    </div>
                    <h6 class="mb-0 text-dark">Class Wise</h6>
                    <small class="text-muted">By class</small>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.fees.reports.daily') }}" class="card report-link-card h-100 text-decoration-none">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="calendar" class="text-info"></i>
                    </div>
                    <h6 class="mb-0 text-dark">Daily Report</h6>
                    <small class="text-muted">Today's collection</small>
                </div>
            </a>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('admin.fees.reports.export-excel', ['type' => 'collection']) }}" class="card report-link-card h-100 text-decoration-none">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success-subtle d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="file" class="text-success"></i>
                    </div>
                    <h6 class="mb-0 text-dark">Export Excel</h6>
                    <small class="text-muted">Download report</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Collections & Class Summary -->
    <div class="row">
        <!-- Recent Collections -->
        <div class="col-xl-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Collections</h6>
                    <a href="{{ route('admin.fees.reports.collection') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Receipt</th>
                                    <th>Student</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCollections as $collection)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.fees.receipt', $collection) }}">
                                                {{ $collection->receipt_no }}
                                            </a>
                                        </td>
                                        <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                                        <td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
                                        <td class="fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
                                        <td>{{ $collection->payment_date->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No recent collections</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class-wise Summary -->
        <div class="col-xl-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Class-wise Collection Summary</h6>
                    <a href="{{ route('admin.fees.reports.class-wise') }}" class="btn btn-sm btn-outline-primary">Details</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Class</th>
                                    <th class="text-end">Collected</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classWiseData as $item)
                                    <tr>
                                        <td>{{ $item->class_name }}</td>
                                        <td class="text-end fw-bold">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted">No data available</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
jQuery(document).ready(function() {
    // Monthly Collection Chart
    const monthlyCtx = document.getElementById('monthlyCollectionChart').getContext('2d');
    let monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Collection',
                data: {!! json_encode($chartData) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + parseFloat(context.raw).toLocaleString('en-US', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Chart Type Toggle
    jQuery('.chart-type-btn').on('click', function() {
        jQuery('.chart-type-btn').removeClass('active');
        jQuery(this).addClass('active');
        const type = jQuery(this).data('type');
        monthlyChart.config.type = type;
        monthlyChart.update();
    });

    // Fee Type Chart
    if (document.getElementById('feeTypeChart')) {
    const feeTypeCtx = document.getElementById('feeTypeChart').getContext('2d');
    const feeTypeData = {!! json_encode($feeTypeData) !!};
    new Chart(feeTypeCtx, {
        type: 'bar',
        data: {
            labels: feeTypeData.map(item => item.name),
            datasets: [{
                label: 'Collection',
                data: feeTypeData.map(item => parseFloat(item.total)),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)',
                    'rgba(83, 102, 255, 0.7)',
                    'rgba(255, 99, 255, 0.7)',
                    'rgba(99, 255, 132, 0.7)'
                ],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    } // end feeTypeChart if

    // Class-wise Chart
    if (document.getElementById('classWiseChart')) {
    const classWiseCtx = document.getElementById('classWiseChart').getContext('2d');
    const classWiseData = {!! json_encode($classWiseData) !!};
    new Chart(classWiseCtx, {
        type: 'bar',
        data: {
            labels: classWiseData.map(item => item.class_name),
            datasets: [{
                label: 'Collection',
                data: classWiseData.map(item => parseFloat(item.total)),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    } // end classWiseChart if
});
</script>
@endpush
