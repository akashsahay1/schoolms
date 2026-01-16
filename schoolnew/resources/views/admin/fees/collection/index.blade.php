@extends('layouts.app')

@section('title', 'Fee Collection')

@section('page-title', 'Fee Collection')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="#">Fees</a></li>
	<li class="breadcrumb-item active">Collection</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				{{ session('error') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		@endif

		<div class="row mb-3">
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<h6 class="text-muted">Total Collected</h6>
						<h3 class="mb-0">₹{{ number_format($totalCollected, 2) }}</h3>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Fee Collection Records</h5>
					<div>
						<a href="{{ route('admin.fees.outstanding') }}" class="btn btn-outline-warning me-2">
							<i data-feather="alert-circle" class="me-1"></i> Outstanding Fees
						</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form action="{{ route('admin.fees.collection') }}" method="GET" class="row g-3 mb-3" id="filter-form">
					<div class="col-md-2">
						<label class="form-label">Quick Filter</label>
						<select class="form-select" id="quick-filter">
							<option value="">Custom Range</option>
							<option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
							<option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
							<option value="this_quarter" {{ request('period') == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
							<option value="last_quarter" {{ request('period') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
							<option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
							<option value="last_year" {{ request('period') == 'last_year' ? 'selected' : '' }}>Last Year</option>
						</select>
						<input type="hidden" name="period" id="period-input" value="{{ request('period') }}">
					</div>
					<div class="col-md-2">
						<label class="form-label">From Date <span class="text-danger">*</span></label>
						<input type="date" name="from_date" id="from-date" class="form-control" value="{{ request('from_date', Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}" required>
					</div>
					<div class="col-md-2">
						<label class="form-label">To Date <span class="text-danger">*</span></label>
						<input type="date" name="to_date" id="to-date" class="form-control" value="{{ request('to_date', Carbon\Carbon::now()->format('Y-m-d')) }}" required>
					</div>
					<div class="col-md-2">
						<label class="form-label">Academic Year</label>
						<select name="academic_year" class="form-select">
							<option value="">All Years</option>
							@foreach($academicYears as $year)
								<option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Class</label>
						<select name="class" class="form-select">
							<option value="">All Classes</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="form-label">Payment Mode</label>
						<select name="payment_mode" class="form-select">
							<option value="">All Payments</option>
							<option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
							<option value="cheque" {{ request('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
							<option value="card" {{ request('payment_mode') === 'card' ? 'selected' : '' }}>Card</option>
							<option value="online" {{ request('payment_mode') === 'online' ? 'selected' : '' }}>Online</option>
							<option value="bank_transfer" {{ request('payment_mode') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Search Student</label>
						<input type="text" name="search" class="form-control" placeholder="Name or Admission No..." value="{{ request('search') }}">
					</div>
					<div class="col-md-3">
						<label class="form-label">&nbsp;</label>
						<div>
							<button type="submit" class="btn btn-primary">
								<i data-feather="filter" class="me-1"></i> Filter
							</button>
							<a href="{{ route('admin.fees.collection') }}" class="btn btn-light">Reset</a>
						</div>
					</div>
				</form>

				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Receipt No</th>
								<th>Date</th>
								<th>Student</th>
								<th>Fee Type</th>
								<th>Amount</th>
								<th>Paid</th>
								<th>Payment Mode</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($collections as $collection)
								<tr>
									<td><span class="badge badge-light-primary">{{ $collection->receipt_no }}</span></td>
									<td>{{ $collection->payment_date->format('d M Y') }}</td>
									<td>
										<strong>{{ $collection->student->full_name }}</strong><br>
										<small class="text-muted">{{ $collection->student->admission_no }}</small>
									</td>
									<td>{{ $collection->feeStructure->feeType->name }}</td>
									<td>₹{{ number_format($collection->amount, 2) }}</td>
									<td><strong>₹{{ number_format($collection->paid_amount, 2) }}</strong></td>
									<td>
										<span class="badge badge-light-info">
											{{ str_replace('_', ' ', ucfirst($collection->payment_mode)) }}
										</span>
									</td>
									<td>
										<div class="common-align gap-2 justify-content-start">
											<a class="square-white" href="{{ route('admin.fees.receipt', $collection) }}" title="View Receipt">
												<svg><use href="{{ asset('assets/svg/icon-sprite.svg#eye') }}"></use></svg>
											</a>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<div class="text-muted">
											<i data-feather="inbox" style="width: 48px; height: 48px;"></i>
											<p class="mt-2 mb-0">No fee collection records found.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($collections->hasPages())
					<div class="mt-3">
						{{ $collections->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	jQuery(document).ready(function() {
		// Quick filter functionality
		jQuery('#quick-filter').on('change', function() {
			var period = jQuery(this).val();
			var today = new Date();
			var fromDate, toDate;

			jQuery('#period-input').val(period);

			switch(period) {
				case 'this_month':
					fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
					toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
					break;
				case 'last_month':
					fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
					toDate = new Date(today.getFullYear(), today.getMonth(), 0);
					break;
				case 'this_quarter':
					var quarter = Math.floor(today.getMonth() / 3);
					fromDate = new Date(today.getFullYear(), quarter * 3, 1);
					toDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
					break;
				case 'last_quarter':
					var quarter = Math.floor(today.getMonth() / 3) - 1;
					var year = today.getFullYear();
					if (quarter < 0) {
						quarter = 3;
						year--;
					}
					fromDate = new Date(year, quarter * 3, 1);
					toDate = new Date(year, quarter * 3 + 3, 0);
					break;
				case 'this_year':
					fromDate = new Date(today.getFullYear(), 0, 1);
					toDate = new Date(today.getFullYear(), 11, 31);
					break;
				case 'last_year':
					fromDate = new Date(today.getFullYear() - 1, 0, 1);
					toDate = new Date(today.getFullYear() - 1, 11, 31);
					break;
				default:
					return;
			}

			jQuery('#from-date').val(formatDate(fromDate));
			jQuery('#to-date').val(formatDate(toDate));
		});

		function formatDate(date) {
			var year = date.getFullYear();
			var month = String(date.getMonth() + 1).padStart(2, '0');
			var day = String(date.getDate()).padStart(2, '0');
			return year + '-' + month + '-' + day;
		}
	});
</script>
@endpush
