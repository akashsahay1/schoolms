@extends('layouts.portal')

@section('title', 'Fee Overview')
@section('page-title', 'Fee Overview')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Fees</a></li>
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Stats -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-primary">
                <div class="card-body text-center">
                    <h4 class="text-primary">Rs. {{ number_format($stats['total_fees'], 2) }}</h4>
                    <p class="mb-0 text-primary">Total Fees</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-success">
                <div class="card-body text-center">
                    <h4 class="text-success">Rs. {{ number_format($stats['total_paid'], 2) }}</h4>
                    <p class="mb-0 text-success">Total Paid</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-info">
                <div class="card-body text-center">
                    <h4 class="text-info">Rs. {{ number_format($stats['total_discount'], 2) }}</h4>
                    <p class="mb-0 text-info">Discount</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card bg-light-{{ $stats['total_due'] > 0 ? 'danger' : 'success' }}">
                <div class="card-body text-center">
                    <h4 class="text-{{ $stats['total_due'] > 0 ? 'danger' : 'success' }}">Rs. {{ number_format($stats['total_due'], 2) }}</h4>
                    <p class="mb-0 text-{{ $stats['total_due'] > 0 ? 'danger' : 'success' }}">{{ $stats['total_due'] > 0 ? 'Due Amount' : 'All Paid' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Structure -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Fee Structure for {{ $student->schoolClass->name ?? 'Class' }}</h5>
                </div>
                <div class="card-body">
                    @if($feeStructures->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Group</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeStructures as $structure)
                                        <tr>
                                            <td>{{ $structure->feeType->name ?? 'N/A' }}</td>
                                            <td>{{ $structure->feeGroup->name ?? 'N/A' }}</td>
                                            <td>Rs. {{ number_format($structure->amount, 2) }}</td>
                                            <td>{{ $structure->due_date ? $structure->due_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th>Rs. {{ number_format($feeStructures->sum('amount'), 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No fee structure available for your class</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Recent Payments</h5>
                        <a href="{{ route('portal.fees.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($feeCollections->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Receipt No</th>
                                        <th>Fee Type</th>
                                        <th>Amount Paid</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feeCollections->take(5) as $collection)
                                        <tr>
                                            <td>{{ $collection->receipt_no ?? 'N/A' }}</td>
                                            <td>{{ $collection->feeStructure->feeType->name ?? 'N/A' }}</td>
                                            <td>Rs. {{ number_format($collection->amount_paid, 2) }}</td>
                                            <td>{{ $collection->payment_date ? $collection->payment_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $statusClass = match($collection->status) {
                                                        'paid' => 'success',
                                                        'partial' => 'warning',
                                                        default => 'danger'
                                                    };
                                                @endphp
                                                <span class="badge badge-light-{{ $statusClass }}">{{ ucfirst($collection->status) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('portal.fees.receipt', $collection) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                    <i class="fa fa-eye"></i> Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No payment records found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
