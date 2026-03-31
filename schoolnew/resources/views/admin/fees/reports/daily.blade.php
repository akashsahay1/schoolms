@extends('layouts.app')

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
                        <a href="{{ route('admin.fees.reports.export-excel', ['type' => 'daily', 'date' => $date]) }}" class="btn btn-sm btn-outline-success" title="Export Excel">
                            <i data-feather="download" style="width: 14px; height: 14px;"></i> Excel
                        </a>
                        <a href="{{ route('admin.fees.reports.export-pdf', ['type' => 'daily', 'date' => $date]) }}" class="btn btn-sm btn-outline-danger" title="Export PDF">
                            <i data-feather="file-text" style="width: 14px; height: 14px;"></i> PDF
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
    <div class="card" style="border-radius: 12px; border: 1px solid #eee;">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: #f8f9fc; border-bottom: 1px solid #eee; border-radius: 12px 12px 0 0; padding: 14px 20px;">
            <h6 class="mb-0" style="font-weight: 700; color: #2c323f;"><i class="icon-list me-2" style="color: #7366ff;"></i>Collection Details</h6>
            <span class="badge" style="font-size: 12px; background: #7366ff; color: #fff;">{{ $collections->count() }} entries</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size: 13px;">
                    <thead>
                        <tr style="background: #f0efff;">
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">#</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Receipt</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Time</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Student</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Class</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Fee Type</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;" class="text-end">Amount</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;" class="text-center">Mode</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">By</th>
                            <th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collections as $index => $collection)
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 12px 16px; color: #aaa;">{{ $index + 1 }}</td>
                                <td style="padding: 12px 16px;">
                                    <a href="{{ route('admin.fees.receipt', $collection) }}" style="color: #7366ff; font-weight: 600;">{{ $collection->receipt_no }}</a>
                                </td>
                                <td style="padding: 12px 16px; color: #888;">{{ $collection->created_at->format('h:i A') }}</td>
                                <td style="padding: 12px 16px;">
                                    <strong>{{ $collection->student->full_name ?? 'N/A' }}</strong>
                                    <br><small style="color: #aaa;">{{ $collection->student->admission_no ?? '' }}</small>
                                </td>
                                <td style="padding: 12px 16px; color: #888;">{{ $collection->student->schoolClass->name ?? '-' }}</td>
                                <td style="padding: 12px 16px;"><span class="badge badge-light-primary" style="font-size: 11px;">{{ $collection->feeStructure->feeType->name ?? '-' }}</span></td>
                                <td style="padding: 12px 16px; font-weight: 700; color: #2c323f;" class="text-end">₹{{ number_format($collection->paid_amount, 2) }}</td>
                                <td style="padding: 12px 16px;" class="text-center">
                                    @php
                                        $modeColors = ['cash' => 'success', 'online' => 'info', 'cheque' => 'warning', 'card' => 'secondary', 'bank_transfer' => 'dark'];
                                    @endphp
                                    <span class="badge bg-{{ $modeColors[$collection->payment_mode] ?? 'secondary' }}" style="font-size: 10px;">
                                        {{ ucfirst(str_replace('_', ' ', $collection->payment_mode)) }}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px; color: #888; font-size: 12px;">{{ $collection->collectedBy->name ?? 'System' }}</td>
                                <td style="padding: 12px 16px;" class="text-center">
                                    <a href="{{ route('admin.fees.receipt', $collection) }}" class="btn btn-primary btn-sm" style="color: #fff; border-radius: 6px; padding: 5px 12px; font-size: 12px;" title="View Receipt">
                                        <i class="icon-eye me-1" style="color: #fff;"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5" style="color: #bbb;">
                                    <i class="icon-info-alt d-block mb-2" style="font-size: 28px;"></i>
                                    No collections on this date
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($collections->count() > 0)
                        <tfoot>
                            <tr style="background: #f8f9fc;">
                                <th colspan="6" style="padding: 12px 16px; font-size: 12px; color: #888;" class="text-end">Total:</th>
                                <th style="padding: 12px 16px; font-size: 15px; color: #54BA4A;" class="text-end">₹{{ number_format($summary['total'], 2) }}</th>
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
