@extends('layouts.app')

@section('title', 'Transport Fee Collections')

@section('page-title', 'Transport - Fee Collections')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.fees.index') }}">Transport Fees</a></li>
    <li class="breadcrumb-item active">Collections</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-light-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Due</h6>
                        <h3 class="mb-0 text-primary">₹{{ number_format($stats['total_due'], 2) }}</h3>
                    </div>
                    <div class="bg-primary rounded-circle p-3">
                        <i data-feather="alert-circle" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Collected</h6>
                        <h3 class="mb-0 text-success">₹{{ number_format($stats['total_collected'], 2) }}</h3>
                    </div>
                    <div class="bg-success rounded-circle p-3">
                        <i data-feather="check-circle" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Pending Payments</h6>
                        <h3 class="mb-0 text-warning">{{ $stats['pending_count'] }}</h3>
                    </div>
                    <div class="bg-warning rounded-circle p-3">
                        <i data-feather="clock" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Fee Collections</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.transport.fees.export-collections', request()->query()) }}" class="btn btn-outline-success">
                            <i data-feather="download" class="me-1"></i> Export CSV
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateFeesModal">
                            <i data-feather="zap" class="me-1"></i> Generate Monthly Fees
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Filters -->
                <form action="{{ route('admin.transport.fees.collections') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Academic Year</label>
                            <select name="academic_year_id" class="form-select">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Route</label>
                            <select name="route_id" class="form-select">
                                <option value="">All Routes</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ $selectedRoute == $route->id ? 'selected' : '' }}>{{ $route->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Student name or admission no..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.transport.fees.collections') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Receipt No</th>
                                <th>Student</th>
                                <th>Route</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $collection)
                                <tr>
                                    <td>{{ $collections->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if($collection->receipt_number)
                                            <span class="badge badge-light-primary">{{ $collection->receipt_number }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $collection->student->first_name }} {{ $collection->student->last_name }}</strong>
                                        <br><small class="text-muted">{{ $collection->student->admission_no }}</small>
                                    </td>
                                    <td>{{ $collection->transportFee->route->title ?? '-' }}</td>
                                    <td>
                                        @if($collection->month)
                                            {{ \Carbon\Carbon::parse($collection->month . '-01')->format('M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>₹{{ number_format($collection->total_payable, 2) }}</td>
                                    <td class="text-success">₹{{ number_format($collection->paid_amount, 2) }}</td>
                                    <td class="text-danger">₹{{ number_format($collection->balance, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $collection->getStatusBadgeClass() }}">{{ $collection->getStatusLabel() }}</span>
                                    </td>
                                    <td>{{ $collection->payment_date?->format('M d, Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <p class="text-muted">No collections found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($collections->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $collections->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Generate Fees Modal -->
<div class="modal fade" id="generateFeesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.transport.fees.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Monthly Fees</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">This will create pending fee entries for all students assigned to transport routes.</p>

                    <div class="mb-3">
                        <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-select" required>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Month <span class="text-danger">*</span></label>
                        <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
