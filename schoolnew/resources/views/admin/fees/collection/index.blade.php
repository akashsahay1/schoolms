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
				<form action="{{ route('admin.fees.collection') }}" method="GET" class="row g-3 mb-3">
					<div class="col-md-2">
						<select name="academic_year" class="form-select" onchange="this.form.submit()">
							<option value="">All Years</option>
							@foreach($academicYears as $year)
								<option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<select name="class" class="form-select" onchange="this.form.submit()">
							<option value="">All Classes</option>
							@foreach($classes as $class)
								<option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<input type="text" name="search" class="form-control" placeholder="Search student..." value="{{ request('search') }}">
					</div>
					<div class="col-md-2">
						<select name="payment_mode" class="form-select" onchange="this.form.submit()">
							<option value="">All Payments</option>
							<option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
							<option value="cheque" {{ request('payment_mode') === 'cheque' ? 'selected' : '' }}>Cheque</option>
							<option value="card" {{ request('payment_mode') === 'card' ? 'selected' : '' }}>Card</option>
							<option value="online" {{ request('payment_mode') === 'online' ? 'selected' : '' }}>Online</option>
							<option value="bank_transfer" {{ request('payment_mode') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
						</select>
					</div>
					<div class="col-md-2">
						<input type="date" name="from_date" class="form-control" placeholder="From Date" value="{{ request('from_date') }}">
					</div>
					<div class="col-md-2">
						<input type="date" name="to_date" class="form-control" placeholder="To Date" value="{{ request('to_date') }}">
					</div>
					<div class="col-12">
						<button type="submit" class="btn btn-primary">Filter</button>
						<a href="{{ route('admin.fees.collection') }}" class="btn btn-light">Reset</a>
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
