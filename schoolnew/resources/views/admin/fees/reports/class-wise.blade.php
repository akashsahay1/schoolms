@extends('layouts.app')

@section('title', 'Class Wise Report')
@section('page-title', 'Class Wise Collection Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
    <li class="breadcrumb-item active">Class Wise</li>
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
            <form method="GET" action="{{ route('admin.fees.reports.class-wise') }}" class="row g-3 align-items-end">
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
                    <a href="{{ route('admin.fees.reports.class-wise') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                <i data-feather="check-circle" class="text-white" style="width: 32px; height: 32px;"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="text-white-50 mb-0">Total Collected</h6>
                            <h2 class="mb-0">{{ number_format($totalCollected, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                <i data-feather="alert-circle" class="text-white" style="width: 32px; height: 32px;"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="text-white-50 mb-0">Total Outstanding</h6>
                            <h2 class="mb-0">{{ number_format($totalOutstanding, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-5 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Collection vs Outstanding</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="classWiseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="col-xl-7 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Class Wise Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Class</th>
                                    <th class="text-center">Students</th>
                                    <th class="text-end">Total Fee</th>
                                    <th class="text-end">Collected</th>
                                    <th class="text-end">Outstanding</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($classData as $data)
                                    <tr>
                                        <td><span class="fw-bold">{{ $data['class']->name }}</span></td>
                                        <td class="text-center">{{ $data['student_count'] }}</td>
                                        <td class="text-end">{{ number_format($data['total_fee'], 2) }}</td>
                                        <td class="text-end text-success fw-bold">{{ number_format($data['collected'], 2) }}</td>
                                        <td class="text-end">
                                            @if($data['outstanding'] > 0)
                                                <span class="text-danger fw-bold">{{ number_format($data['outstanding'], 2) }}</span>
                                            @else
                                                <span class="badge bg-success">Fully Paid</span>
                                            @endif
                                        </td>
                                        <td style="width: 150px;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $data['percentage'] >= 80 ? 'bg-success' : ($data['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                     style="width: {{ $data['percentage'] }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $data['percentage'] }}% collected</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($classData) > 0)
                                <tfoot class="bg-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center">{{ collect($classData)->sum('student_count') }}</th>
                                        <th class="text-end">{{ number_format(collect($classData)->sum('total_fee'), 2) }}</th>
                                        <th class="text-end text-success">{{ number_format($totalCollected, 2) }}</th>
                                        <th class="text-end text-danger">{{ number_format($totalOutstanding, 2) }}</th>
                                        <th></th>
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
    const classData = {!! json_encode($classData) !!};

    if (classData.length > 0) {
        const ctx = document.getElementById('classWiseChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: classData.map(item => item.class.name),
                datasets: [
                    {
                        label: 'Collected',
                        data: classData.map(item => parseFloat(item.collected)),
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderRadius: 5
                    },
                    {
                        label: 'Outstanding',
                        data: classData.map(item => parseFloat(item.outstanding)),
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + parseFloat(context.raw).toLocaleString('en-US', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false
                    },
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
    }
});
</script>
@endpush
