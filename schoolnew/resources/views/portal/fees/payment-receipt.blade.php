@extends('layouts.portal')

@section('title', 'Payment Receipt')
@section('page-title', 'Payment Receipt')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.fees.overview') }}">Fees</a></li>
    <li class="breadcrumb-item active">Receipt</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card" id="receipt">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <h4 class="text-primary mb-0">{{ config('app.name') }}</h4>
                            <p class="text-muted mb-0">Fee Payment Receipt</p>
                        </div>
                        <div class="col-6 text-end">
                            <span class="badge bg-success fs-6">PAID</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Receipt Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Receipt Details</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Transaction ID:</td>
                                    <td><strong>{{ $payment->razorpay_payment_id }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Order ID:</td>
                                    <td>{{ $payment->razorpay_order_id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Payment Date:</td>
                                    <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : $payment->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Payment Mode:</td>
                                    <td>Online (Razorpay)</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Student Details</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Name:</td>
                                    <td><strong>{{ $student->name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Class:</td>
                                    <td>{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Roll No:</td>
                                    <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Admission No:</td>
                                    <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Fee Details -->
                    <h6 class="text-muted mb-3">Fee Details</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fee Type</th>
                                    <th>Group</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feeStructures as $index => $structure)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $structure->feeType->name ?? 'N/A' }}</td>
                                        <td>{{ $structure->feeGroup->name ?? '-' }}</td>
                                        <td class="text-end">Rs. {{ number_format($structure->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total Paid:</th>
                                    <th class="text-end text-success">Rs. {{ number_format($payment->amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Footer -->
                    <div class="row">
                        <div class="col-12 text-center">
                            <p class="text-muted mb-1">
                                <small>This is a computer-generated receipt and does not require a signature.</small>
                            </p>
                            <p class="text-muted mb-0">
                                <small>For any queries, please contact the school administration.</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <button class="btn btn-primary me-2" onclick="printReceipt()">
                        <i class="fa fa-print me-2"></i>Print Receipt
                    </button>
                    <a href="{{ route('portal.fees.overview') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-2"></i>Back to Fees
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function printReceipt() {
    var printContents = document.getElementById('receipt').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = '<div style="padding: 20px;">' + printContents + '</div>';
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>
@endpush
