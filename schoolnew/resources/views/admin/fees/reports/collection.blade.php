@extends('layouts.app')

@section('title', 'Fee Collection Report')
@section('page-title', 'Fee Collection Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
    <li class="breadcrumb-item active">Collection Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.collection') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label">Fee Type</label>
                    <select name="fee_type_id" class="form-select">
                        <option value="">All Types</option>
                        @foreach($feeTypes as $type)
                            <option value="{{ $type->id }}" {{ request('fee_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment Mode</label>
                    <select name="payment_mode" class="form-select">
                        <option value="">All Modes</option>
                        <option value="cash" {{ request('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="online" {{ request('payment_mode') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="cheque" {{ request('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="card" {{ request('payment_mode') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="bank_transfer" {{ request('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-feather="filter" class="me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.reports.collection') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50">Total Collected</h6>
                    <h4 class="mb-0">{{ number_format($summary['total_amount'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="text-dark-50">Total Discount</h6>
                    <h4 class="mb-0">{{ number_format($summary['total_discount'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50">Total Fine</h6>
                    <h4 class="mb-0">{{ number_format($summary['total_fine'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50">Total Transactions</h6>
                    <h4 class="mb-0">{{ number_format($summary['total_transactions']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Collection List -->
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Collection Details</h6>
                    <div class="btn-group">
                        <a href="{{ route('admin.fees.reports.export', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'payment_mode'])) }}" class="btn btn-sm btn-outline-secondary" title="Export CSV">
                            <i data-feather="file-text" style="width: 14px; height: 14px;"></i> CSV
                        </a>
                        <a href="{{ route('admin.fees.reports.export-excel', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'payment_mode'])) }}" class="btn btn-sm btn-outline-success" title="Export Excel">
                            <i data-feather="file" style="width: 14px; height: 14px;"></i> Excel
                        </a>
                        <a href="{{ route('admin.fees.reports.export-pdf', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'payment_mode'])) }}" class="btn btn-sm btn-outline-danger" title="Export PDF">
                            <i data-feather="file-minus" style="width: 14px; height: 14px;"></i> PDF
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Fee Type</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collections as $collection)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.fees.receipt', $collection) }}">
                                                {{ $collection->receipt_no }}
                                            </a>
                                        </td>
                                        <td>{{ $collection->payment_date->format('d M Y') }}</td>
                                        <td>{{ $collection->student->full_name ?? 'N/A' }}</td>
                                        <td>{{ $collection->student->schoolClass->name ?? 'N/A' }}</td>
                                        <td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
                                        <td class="text-end fw-bold">{{ number_format($collection->paid_amount, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $collection->payment_mode == 'cash' ? 'success' : ($collection->payment_mode == 'online' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $collection->payment_mode)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No collections found for the selected period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($collections->hasPages())
                    <div class="card-footer">
                        {{ $collections->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Daily Breakdown -->
        <div class="col-xl-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Daily Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px;">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th>Date</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dailyData as $day)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                                        <td class="text-center">{{ $day->count }}</td>
                                        <td class="text-end fw-bold">{{ number_format($day->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($dailyData->count() > 0)
                                <tfoot class="bg-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-center">{{ $dailyData->sum('count') }}</th>
                                        <th class="text-end">{{ number_format($dailyData->sum('total'), 2) }}</th>
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
