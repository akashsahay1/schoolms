@extends('layouts.portal')

@section('title', 'Payment History')
@section('page-title', 'Payment History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.fees.overview') }}">Fees</a></li>
    <li class="breadcrumb-item active">Payment History</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>All Payments</h5>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Receipt No</th>
                                        <th>Fee Type</th>
                                        <th>Amount Paid</th>
                                        <th>Discount</th>
                                        <th>Payment Date</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->receipt_no ?? 'N/A' }}</td>
                                            <td>{{ $payment->feeStructure->feeType->name ?? 'N/A' }}</td>
                                            <td>Rs. {{ number_format($payment->paid_amount, 2) }}</td>
                                            <td>Rs. {{ number_format($payment->discount_amount ?? 0, 2) }}</td>
                                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ ucfirst($payment->payment_mode ?? 'N/A') }}</td>
                                            <td>
                                                <span class="badge badge-light-success">Paid</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('portal.fees.receipt', $payment) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i> Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payment Records</h5>
                            <p class="text-muted">You haven't made any payments yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
