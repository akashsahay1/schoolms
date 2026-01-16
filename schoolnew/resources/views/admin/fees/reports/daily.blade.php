@extends('layouts.admin')

@section('title', 'Daily Collection Report')
@section('page-title', 'Daily Collection Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
    <li class="breadcrumb-item active">Daily Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.daily') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="calendar" class="me-1"></i> View Report
                    </button>
                    <a href="{{ route('admin.fees.reports.daily') }}" class="btn btn-light">Today</a>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="{{ route('admin.fees.reports.export', ['type' => 'daily', 'date' => $date]) }}" class="btn btn-sm btn-outline-secondary" title="Export CSV">
                            <i data-feather="file-text" style="width: 14px; height: 14px;"></i> CSV
                        </a>
                        <a href="{{ route('admin.fees.reports.export-excel', ['type' => 'daily', 'date' => $date]) }}" class="btn btn-sm btn-outline-success" title="Export Excel">
                            <i data-feather="file" style="width: 14px; height: 14px;"></i> Excel
                        </a>
                        <a href="{{ route('admin.fees.reports.export-pdf', ['type' => 'daily', 'date' => $date]) }}" class="btn btn-sm btn-outline-danger" title="Export PDF">
                            <i data-feather="file-minus" style="width: 14px; height: 14px;"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Date Display -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-muted">
                <i data-feather="calendar" class="me-2"></i>
                {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
            </h5>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Total Collection</h6>
                    <h4 class="mb-0">{{ number_format($summary['total'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Cash</h6>
                    <h4 class="mb-0">{{ number_format($summary['cash'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Online</h6>
                    <h4 class="mb-0">{{ number_format($summary['online'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body text-center">
                    <h6 class="text-dark-50">Cheque</h6>
                    <h4 class="mb-0">{{ number_format($summary['cheque'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Card</h6>
                    <h4 class="mb-0">{{ number_format($summary['card'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body text-center">
                    <h6 class="text-white-50">Transactions</h6>
                    <h4 class="mb-0">{{ $summary['count'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Collection Details</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Receipt No</th>
                            <th>Time</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Fee Type</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Mode</th>
                            <th>Collected By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collections as $index => $collection)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.fees.receipt', $collection) }}" class="fw-bold">
                                        {{ $collection->receipt_no }}
                                    </a>
                                </td>
                                <td>{{ $collection->created_at->format('h:i A') }}</td>
                                <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $collection->student->schoolClass->name ?? 'N/A' }}</td>
                                <td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
                                <td class="text-end fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $modeColors = [
                                            'cash' => 'success',
                                            'online' => 'info',
                                            'cheque' => 'warning',
                                            'card' => 'secondary',
                                            'bank_transfer' => 'dark'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $modeColors[$collection->payment_mode] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $collection->payment_mode)) }}
                                    </span>
                                </td>
                                <td>{{ $collection->collectedBy->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.fees.receipt', $collection) }}" class="btn btn-sm btn-outline-primary" title="Print Receipt">
                                        <i data-feather="printer" style="width: 14px; height: 14px;"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                    <p class="mt-3 mb-0">No collections on this date</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($collections->count() > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="6" class="text-end">Total:</th>
                                <th class="text-end">{{ number_format($summary['total'], 2) }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
