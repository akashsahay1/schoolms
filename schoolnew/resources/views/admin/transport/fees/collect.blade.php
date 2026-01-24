@extends('layouts.app')

@section('title', 'Collect Transport Fee')

@section('page-title', 'Transport - Collect Fee')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.fees.collections') }}">Transport Collections</a></li>
    <li class="breadcrumb-item active">Collect Fee</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Collect Transport Fee</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.transport.fees.collect', $student) }}" method="POST" id="collectForm">
                    @csrf

                    <input type="hidden" name="transport_fee_id" value="{{ $transportFee->id }}">
                    <input type="hidden" name="route_assignment_id" value="{{ $assignment->id }}">

                    <!-- Route & Fee Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="text-muted mb-2">Route Information</h6>
                                <p class="mb-1"><strong>Route:</strong> {{ $assignment->route->route_name }}</p>
                                <p class="mb-1"><strong>Pickup Point:</strong> {{ $assignment->pickup_point ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Drop Point:</strong> {{ $assignment->drop_point ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="text-muted mb-2">Fee Structure</h6>
                                <p class="mb-1"><strong>Fee Type:</strong> {{ ucfirst($transportFee->fee_type) }}</p>
                                <p class="mb-1"><strong>Base Amount:</strong> {{ number_format($transportFee->amount, 2) }}</p>
                                <p class="mb-0"><strong>Late Fee:</strong> {{ number_format($transportFee->late_fee, 2) }} (after {{ $transportFee->due_day ?? 10 }} days)</p>
                            </div>
                        </div>
                    </div>

                    @if($transportFee->fee_type === 'monthly' && count($pendingMonths) > 0)
                        <div class="mb-3">
                            <label class="form-label">Select Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" id="monthSelect" required>
                                <option value="">-- Select Month --</option>
                                @foreach($pendingMonths as $key => $label)
                                    <option value="{{ $key }}" {{ old('month') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($transportFee->fee_type === 'monthly')
                        <div class="alert alert-success">
                            <i data-feather="check-circle" class="me-2"></i> All monthly fees have been collected for this student.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $transportFee->amount) }}" step="0.01" min="0" required readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Discount</label>
                            <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount', 0) }}" step="0.01" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fine/Late Fee</label>
                            <input type="number" name="fine" id="fine" class="form-control" value="{{ old('fine', 0) }}" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Payable</label>
                            <input type="text" id="totalPayable" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount Paid <span class="text-danger">*</span></label>
                            <input type="number" name="paid_amount" id="paidAmount" class="form-control" value="{{ old('paid_amount') }}" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-select" required>
                                <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="online" {{ old('payment_mode') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI</option>
                                <option value="card" {{ old('payment_mode') == 'card' ? 'selected' : '' }}>Card</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Transaction ID / Reference</label>
                        <input type="text" name="transaction_id" class="form-control" value="{{ old('transaction_id') }}" placeholder="For online/cheque/bank transfer payments">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check" class="me-1"></i> Collect Fee
                        </button>
                        <a href="{{ route('admin.transport.fees.collections') }}" class="btn btn-secondary">
                            <i data-feather="x" class="me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Student Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Student Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($student->first_name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <table class="table table-borderless small">
                    <tr>
                        <td class="text-muted">Name</td>
                        <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Admission No</td>
                        <td>{{ $student->admission_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Class</td>
                        <td>{{ $student->class?->name }} - {{ $student->section?->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Father's Name</td>
                        <td>{{ $student->father_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone</td>
                        <td>{{ $student->phone ?? $student->parent_phone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Collection History -->
        @if($existingCollections->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5>Collection History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($existingCollections as $collection)
                                    <tr>
                                        <td>{{ $collection->month ? \Carbon\Carbon::parse($collection->month . '-01')->format('M Y') : 'Full Year' }}</td>
                                        <td>{{ number_format($collection->paid_amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-light-{{ $collection->status === 'paid' ? 'success' : ($collection->status === 'partial' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($collection->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();

    function calculateTotal() {
        var amount = parseFloat(jQuery('#amount').val()) || 0;
        var discount = parseFloat(jQuery('#discount').val()) || 0;
        var fine = parseFloat(jQuery('#fine').val()) || 0;

        var total = (amount + fine) - discount;
        jQuery('#totalPayable').val(total.toFixed(2));
        jQuery('#paidAmount').val(total.toFixed(2));
    }

    jQuery('#amount, #discount, #fine').on('input', calculateTotal);
    calculateTotal();
});
</script>
@endpush
