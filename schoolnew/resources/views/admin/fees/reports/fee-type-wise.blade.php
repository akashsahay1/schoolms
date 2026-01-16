@extends('layouts.admin')

@section('title', 'Fee Type Wise Report')
@section('page-title', 'Fee Type Wise Collection Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
    <li class="breadcrumb-item active">Fee Type Wise</li>
@endsection

@push('css')
<style>
    .chart-container {
        position: relative;
        height: 350px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.fee-type-wise') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.reports.fee-type-wise') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Collected Card -->
    <div class="card bg-primary text-white mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3">
                        <i data-feather="layers" class="text-white" style="width: 32px; height: 32px;"></i>
                    </div>
                </div>
                <div class="col">
                    <h6 class="text-white-50 mb-0">Total Collection by Fee Types</h6>
                    <h2 class="mb-0">{{ number_format($totalCollected, 2) }}</h2>
                    <small class="text-white-50">{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-5 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Collection Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="feeTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="col-xl-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Fee Type Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fee Type</th>
                                    <th class="text-center">Students</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-end">Total Collected</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeTypeData as $index => $type)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $type->name }}</span>
                                        </td>
                                        <td class="text-center">{{ $type->student_count }}</td>
                                        <td class="text-center">{{ $type->transaction_count }}</td>
                                        <td class="text-end fw-bold">{{ number_format($type->total_collected, 2) }}</td>
                                        <td class="text-end">
                                            @php
                                                $percentage = $totalCollected > 0 ? ($type->total_collected / $totalCollected) * 100 : 0;
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 60px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span>{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No data found for the selected period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($feeTypeData->count() > 0)
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center">{{ $feeTypeData->sum('student_count') }}</th>
                                        <th class="text-center">{{ $feeTypeData->sum('transaction_count') }}</th>
                                        <th class="text-end">{{ number_format($totalCollected, 2) }}</th>
                                        <th class="text-end">100%</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
jQuery(document).ready(function() {
    const feeTypeData = {!! json_encode($feeTypeData) !!};

    if (feeTypeData.length > 0) {
        const ctx = document.getElementById('feeTypeChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: feeTypeData.map(item => item.name),
                datasets: [{
                    data: feeTypeData.map(item => parseFloat(item.total_collected)),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)',
                        'rgba(255, 99, 255, 0.8)',
                        'rgba(99, 255, 132, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.label + ': ' + parseFloat(context.raw).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
