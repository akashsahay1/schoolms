@extends('layouts.portal')

@section('title', 'Pay Fees')
@section('page-title', 'Pay Fees Online')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('portal.fees.overview') }}">Fees</a></li>
    <li class="breadcrumb-item active">Checkout</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Fee Summary -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Pending Fees</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                        </div>
                                    </th>
                                    <th>Fee Type</th>
                                    <th>Group</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Discount</th>
                                    <th>Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingFees as $fee)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input fee-checkbox" type="checkbox" value="{{ $fee['id'] }}" data-amount="{{ $fee['due'] }}" checked>
                                            </div>
                                        </td>
                                        <td>{{ $fee['name'] }}</td>
                                        <td>{{ $fee['group'] ?: '-' }}</td>
                                        <td>Rs. {{ number_format($fee['total'], 2) }}</td>
                                        <td class="text-success">Rs. {{ number_format($fee['paid'], 2) }}</td>
                                        <td class="text-info">Rs. {{ number_format($fee['discount'], 2) }}</td>
                                        <td class="text-danger fw-bold">Rs. {{ number_format($fee['due'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Student Name:</span>
                            <strong>{{ $student->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Class:</span>
                            <strong>{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? '' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Roll No:</span>
                            <strong>{{ $student->roll_number ?? 'N/A' }}</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Selected Fees:</span>
                            <strong id="selectedCount">{{ count($pendingFees) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="h6">Total Amount:</span>
                            <strong class="h5 text-primary" id="totalAmount">Rs. {{ number_format($totalDue, 2) }}</strong>
                        </div>
                    </div>

                    <hr>

                    @if($razorpayConfigured ?? false)
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-lg" id="payNowBtn" data-total="{{ $totalDue }}">
                                <i class="fa fa-credit-card me-2"></i> Pay Now
                            </button>
                            <a href="{{ route('portal.fees.overview') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i> Back to Overview
                            </a>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fa fa-lock me-1"></i> Secured by Razorpay
                            </small>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Online payment is currently unavailable.</strong>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('portal.fees.overview') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i> Back to Overview
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card">
                <div class="card-body">
                    @if($razorpayConfigured ?? false)
                        <h6 class="mb-3"><i class="fa fa-info-circle me-2 text-info"></i>Payment Information</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Instant payment confirmation</li>
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Digital receipt generated</li>
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Multiple payment options</li>
                            <li><i class="fa fa-check text-success me-2"></i>100% Secure transaction</li>
                        </ul>
                    @else
                        <h6 class="mb-3"><i class="fa fa-building me-2 text-primary"></i>Pay at School Office</h6>
                        <p class="text-muted mb-3">Please visit the school accounts office to pay your fees. You can pay using:</p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fa fa-money-bill me-2 text-success"></i>Cash</li>
                            <li class="mb-2"><i class="fa fa-credit-card me-2 text-primary"></i>Card (Debit/Credit)</li>
                            <li class="mb-2"><i class="fa fa-university me-2 text-info"></i>Bank Transfer / Cheque</li>
                            <li class="mb-2"><i class="fa fa-mobile me-2 text-warning"></i>UPI Payment</li>
                        </ul>
                        <hr>
                        <p class="mb-0 small text-muted">
                            <i class="fa fa-clock me-1"></i> Office Hours: Mon-Sat, 9:00 AM - 4:00 PM
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if($razorpayConfigured ?? false)
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
jQuery(document).ready(function() {
    // Update totals when checkboxes change
    function updateTotals() {
        let total = 0;
        let count = 0;

        jQuery('.fee-checkbox:checked').each(function() {
            total += parseFloat(jQuery(this).data('amount'));
            count++;
        });

        jQuery('#totalAmount').text('Rs. ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        jQuery('#selectedCount').text(count);
        jQuery('#payNowBtn').data('total', total);

        if (count === 0) {
            jQuery('#payNowBtn').prop('disabled', true);
        } else {
            jQuery('#payNowBtn').prop('disabled', false);
        }
    }

    // Select all checkbox
    jQuery('#selectAll').on('change', function() {
        jQuery('.fee-checkbox').prop('checked', jQuery(this).prop('checked'));
        updateTotals();
    });

    // Individual checkbox
    jQuery('.fee-checkbox').on('change', function() {
        updateTotals();
        // Update select all checkbox
        if (jQuery('.fee-checkbox:checked').length === jQuery('.fee-checkbox').length) {
            jQuery('#selectAll').prop('checked', true);
        } else {
            jQuery('#selectAll').prop('checked', false);
        }
    });

    // Pay Now button
    jQuery('#payNowBtn').on('click', function() {
        let selectedFees = [];
        let total = jQuery(this).data('total');

        jQuery('.fee-checkbox:checked').each(function() {
            selectedFees.push(jQuery(this).val());
        });

        if (selectedFees.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Fees Selected',
                text: 'Please select at least one fee to pay.'
            });
            return;
        }

        if (total <= 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Amount Due',
                text: 'There is no amount due for payment.'
            });
            return;
        }

        // Create order
        jQuery.ajax({
            url: '{{ route("portal.payment.create-order") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                amount: total,
                fee_structure_ids: selectedFees
            },
            beforeSend: function() {
                jQuery('#payNowBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Processing...');
            },
            success: function(response) {
                // Check if demo mode
                if (response.demo_mode) {
                    // Show demo payment modal
                    Swal.fire({
                        title: 'Demo Payment',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-info mb-3">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <strong>Demo Mode</strong> - This is a simulated payment.
                                </div>
                                <p><strong>Amount:</strong> Rs. ${(response.amount / 100).toFixed(2)}</p>
                                <p><strong>Student:</strong> ${response.prefill.name}</p>
                                <p><strong>Description:</strong> ${response.description}</p>
                                <hr>
                                <p class="text-muted small mb-0">Click "Pay Now" to simulate a successful payment.</p>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#7366ff',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa fa-check me-2"></i> Pay Now (Demo)',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit demo payment
                            jQuery('<form action="{{ route("portal.payment.demo-success") }}" method="POST">' +
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                                '<input type="hidden" name="payment_id" value="' + response.payment_id + '">' +
                                '</form>').appendTo('body').submit();
                        } else {
                            jQuery('#payNowBtn').prop('disabled', false).html('<i class="fa fa-credit-card me-2"></i> Pay Now');
                        }
                    });
                    return;
                }

                // Open Razorpay checkout (for real payments)
                var options = {
                    key: response.key,
                    amount: response.amount,
                    currency: response.currency,
                    name: response.name,
                    description: response.description,
                    order_id: response.order_id,
                    prefill: response.prefill,
                    theme: {
                        color: '#7366ff'
                    },
                    handler: function(paymentResponse) {
                        // Payment successful, submit to server
                        jQuery('<form action="{{ route("portal.payment.success") }}" method="POST">' +
                            '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                            '<input type="hidden" name="razorpay_order_id" value="' + paymentResponse.razorpay_order_id + '">' +
                            '<input type="hidden" name="razorpay_payment_id" value="' + paymentResponse.razorpay_payment_id + '">' +
                            '<input type="hidden" name="razorpay_signature" value="' + paymentResponse.razorpay_signature + '">' +
                            '</form>').appendTo('body').submit();
                    },
                    modal: {
                        ondismiss: function() {
                            jQuery('#payNowBtn').prop('disabled', false).html('<i class="fa fa-credit-card me-2"></i> Pay Now');
                        }
                    }
                };

                var rzp = new Razorpay(options);
                rzp.on('payment.failed', function(response) {
                    jQuery('<form action="{{ route("portal.payment.failure") }}" method="POST">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                        '<input type="hidden" name="razorpay_order_id" value="' + response.error.metadata.order_id + '">' +
                        '<input type="hidden" name="error_code" value="' + response.error.code + '">' +
                        '<input type="hidden" name="error_description" value="' + response.error.description + '">' +
                        '</form>').appendTo('body').submit();
                });
                rzp.open();
            },
            error: function(xhr) {
                jQuery('#payNowBtn').prop('disabled', false).html('<i class="fa fa-credit-card me-2"></i> Pay Now');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.error || 'Failed to create order. Please try again.'
                });
            }
        });
    });
});
</script>
@endpush
@endif
