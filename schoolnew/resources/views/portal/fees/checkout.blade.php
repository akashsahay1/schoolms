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
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Pending Fees</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                        </div>
                                    </th>
                                    <th>Fee Type</th>
                                    <th>Category</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $hasAcademic = false; $hasTransport = false; @endphp
                                @foreach($pendingFees as $fee)
                                    @if($fee['type'] === 'academic' && !$hasAcademic)
                                        @php $hasAcademic = true; @endphp
                                        <tr class="bg-light">
                                            <td colspan="6"><strong class="text-primary" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Academic Fees</strong></td>
                                        </tr>
                                    @endif
                                    @if($fee['type'] === 'transport' && !$hasTransport)
                                        @php $hasTransport = true; @endphp
                                        <tr class="bg-light">
                                            <td colspan="6"><strong class="text-warning" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Transport Fees</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input fee-checkbox" type="checkbox" value="{{ $fee['id'] }}" data-amount="{{ $fee['due'] }}" data-type="{{ $fee['type'] }}" checked>
                                            </div>
                                        </td>
                                        <td>{{ $fee['name'] }}</td>
                                        <td><span class="text-muted">{{ $fee['group'] ?: '-' }}</span></td>
                                        <td class="text-end">₹{{ number_format($fee['total'], 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format($fee['paid'], 2) }}</td>
                                        <td class="text-end text-danger fw-bold">₹{{ number_format($fee['due'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">Total Due:</td>
                                    <td class="text-end text-danger" id="tableTotal">₹{{ number_format($totalDue, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Payment Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Student:</span>
                            <strong>{{ $student->full_name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Class:</span>
                            <strong>{{ $student->schoolClass->name ?? 'N/A' }}{{ $student->section ? ' - ' . $student->section->name : '' }}</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        @if($academicDue > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Academic Fees:</span>
                            <span id="academicTotal">₹{{ number_format($academicDue, 2) }}</span>
                        </div>
                        @endif
                        @if($transportDue > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Transport Fees:</span>
                            <span id="transportTotal">₹{{ number_format($transportDue, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Selected Items:</span>
                            <strong id="selectedCount">{{ count($pendingFees) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="h6 mb-0">Total Amount:</span>
                            <strong class="h5 text-primary mb-0" id="totalAmount">₹{{ number_format($totalDue, 2) }}</strong>
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
                            <small class="text-muted"><i class="fa fa-lock me-1"></i> Secured by Razorpay</small>
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

            <div class="card">
                <div class="card-body">
                    @if($razorpayConfigured ?? false)
                        <h6 class="mb-3"><i class="fa fa-info-circle me-2 text-info"></i>Payment Information</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Academic + Transport fees in one payment</li>
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Instant confirmation & digital receipt</li>
                            <li class="mb-2"><i class="fa fa-check text-success me-2"></i>Multiple payment options (UPI, Card, Net Banking)</li>
                            <li><i class="fa fa-check text-success me-2"></i>100% Secure transaction</li>
                        </ul>
                    @else
                        <h6 class="mb-3"><i class="fa fa-building me-2 text-primary"></i>Pay at School Office</h6>
                        <p class="text-muted mb-3">Visit the school accounts office to pay fees via Cash, Card, Bank Transfer, or UPI.</p>
                        <p class="mb-0 small text-muted"><i class="fa fa-clock me-1"></i> Office Hours: Mon-Sat, 9:00 AM - 4:00 PM</p>
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
    function updateTotals() {
        var total = 0;
        var count = 0;
        var academicTotal = 0;
        var transportTotal = 0;

        jQuery('.fee-checkbox:checked').each(function() {
            var amount = parseFloat(jQuery(this).data('amount'));
            var type = jQuery(this).data('type');
            total += amount;
            count++;
            if (type === 'academic') academicTotal += amount;
            if (type === 'transport') transportTotal += amount;
        });

        var formatted = '₹' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        jQuery('#totalAmount').text(formatted);
        jQuery('#tableTotal').text(formatted);
        jQuery('#selectedCount').text(count);
        jQuery('#payNowBtn').data('total', total);
        jQuery('#payNowBtn').prop('disabled', count === 0);

        if (jQuery('#academicTotal').length) {
            jQuery('#academicTotal').text('₹' + academicTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
        if (jQuery('#transportTotal').length) {
            jQuery('#transportTotal').text('₹' + transportTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        }
    }

    jQuery('#selectAll').on('change', function() {
        jQuery('.fee-checkbox').prop('checked', jQuery(this).prop('checked'));
        updateTotals();
    });

    jQuery(document).on('change', '.fee-checkbox', function() {
        updateTotals();
        jQuery('#selectAll').prop('checked', jQuery('.fee-checkbox:checked').length === jQuery('.fee-checkbox').length);
    });

    jQuery('#payNowBtn').on('click', function() {
        var selectedAcademic = [];
        var selectedTransport = [];
        var total = jQuery(this).data('total');

        jQuery('.fee-checkbox:checked').each(function() {
            if (jQuery(this).data('type') === 'academic') {
                selectedAcademic.push(jQuery(this).val());
            } else {
                selectedTransport.push(jQuery(this).val());
            }
        });

        if (selectedAcademic.length === 0 && selectedTransport.length === 0) {
            Swal.fire({ icon: 'warning', title: 'No Fees Selected', text: 'Please select at least one fee to pay.' });
            return;
        }

        if (total <= 0) {
            Swal.fire({ icon: 'info', title: 'No Amount Due', text: 'There is no amount due for payment.' });
            return;
        }

        jQuery.ajax({
            url: '{{ route("portal.payment.create-order") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                amount: total,
                fee_structure_ids: selectedAcademic,
                transport_collection_ids: selectedTransport
            },
            beforeSend: function() {
                jQuery('#payNowBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Processing...');
            },
            success: function(response) {
                if (response.demo_mode) {
                    Swal.fire({
                        title: 'Demo Payment',
                        html: '<div class="text-start">' +
                            '<div class="alert alert-info mb-3"><i class="fa fa-info-circle me-2"></i><strong>Demo Mode</strong> - Simulated payment.</div>' +
                            '<p><strong>Amount:</strong> ₹' + (response.amount / 100).toFixed(2) + '</p>' +
                            '<p><strong>Student:</strong> ' + response.prefill.name + '</p>' +
                            '<hr><p class="text-muted small mb-0">Click "Pay Now" to simulate a successful payment.</p></div>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#7366ff',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa fa-check me-2"></i> Pay Now (Demo)',
                        cancelButtonText: 'Cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
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

                var options = {
                    key: response.key,
                    amount: response.amount,
                    currency: response.currency,
                    name: response.name,
                    description: response.description,
                    order_id: response.order_id,
                    prefill: response.prefill,
                    theme: { color: '#7366ff' },
                    handler: function(paymentResponse) {
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
                rzp.on('payment.failed', function(failResponse) {
                    jQuery('<form action="{{ route("portal.payment.failure") }}" method="POST">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                        '<input type="hidden" name="razorpay_order_id" value="' + failResponse.error.metadata.order_id + '">' +
                        '</form>').appendTo('body').submit();
                });
                rzp.open();
            },
            error: function(xhr) {
                jQuery('#payNowBtn').prop('disabled', false).html('<i class="fa fa-credit-card me-2"></i> Pay Now');
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.error || 'Failed to create order. Please try again.' });
            }
        });
    });
});
</script>
@endpush
@endif
