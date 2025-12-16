@extends('layouts.portal')

@section('title', 'Fee Receipt')
@section('page-title', 'Fee Receipt')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.fees.overview') }}">Fees</a></li>
    <li class="breadcrumb-item active">Receipt</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card" id="receipt-card">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logo/logo.png') }}" alt="School Logo" height="60" class="mb-2">
                        <h4 class="mb-1">{{ config('app.name') }}</h4>
                        <p class="text-muted mb-0">Fee Receipt</p>
                    </div>

                    <hr>

                    <!-- Receipt Info -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <strong>Receipt No:</strong> {{ $feeCollection->receipt_no ?? 'N/A' }}<br>
                            <strong>Date:</strong> {{ $feeCollection->payment_date ? $feeCollection->payment_date->format('M d, Y') : 'N/A' }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>Student:</strong> {{ $student->full_name }}<br>
                            <strong>Class:</strong> {{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }}
                        </div>
                    </div>

                    <!-- Fee Details -->
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $feeCollection->feeStructure->feeType->name ?? 'Fee Payment' }}</td>
                                <td class="text-end">Rs. {{ number_format($feeCollection->amount_paid + ($feeCollection->discount ?? 0), 2) }}</td>
                            </tr>
                            @if($feeCollection->discount > 0)
                                <tr>
                                    <td>Discount</td>
                                    <td class="text-end text-success">- Rs. {{ number_format($feeCollection->discount, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th>Total Paid</th>
                                <th class="text-end">Rs. {{ number_format($feeCollection->amount_paid, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Payment Info -->
                    <div class="row mt-4">
                        <div class="col-6">
                            <strong>Payment Mode:</strong> {{ ucfirst($feeCollection->payment_mode ?? 'N/A') }}<br>
                            @if($feeCollection->transaction_id)
                                <strong>Transaction ID:</strong> {{ $feeCollection->transaction_id }}
                            @endif
                        </div>
                        <div class="col-6 text-end">
                            <strong>Status:</strong>
                            @php
                                $statusClass = match($feeCollection->status) {
                                    'paid' => 'success',
                                    'partial' => 'warning',
                                    default => 'danger'
                                };
                            @endphp
                            <span class="badge badge-light-{{ $statusClass }}">{{ ucfirst($feeCollection->status) }}</span>
                        </div>
                    </div>

                    @if($feeCollection->remarks)
                        <div class="mt-3">
                            <strong>Remarks:</strong> {{ $feeCollection->remarks }}
                        </div>
                    @endif

                    <hr class="mt-4">

                    <div class="text-center text-muted small">
                        <p class="mb-1">This is a computer generated receipt and does not require signature.</p>
                        <p class="mb-0">Thank you for your payment!</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-3 no-print">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa fa-print me-1"></i> Print Receipt
                </button>
                <a href="{{ route('portal.fees.history') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back to History
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .sidebar-wrapper, .page-header, .breadcrumb, .no-print, .page-title { display: none !important; }
        .page-body { margin: 0 !important; padding: 0 !important; }
        #receipt-card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush
@endsection
