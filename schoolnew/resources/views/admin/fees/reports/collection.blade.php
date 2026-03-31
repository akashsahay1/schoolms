@extends('layouts.app')

@section('title', 'Collection Report')
@section('page-title', 'Collection Report')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="{{ route('admin.fees.reports.index') }}">Fee Reports</a></li>
	<li class="breadcrumb-item active">Collection</li>
@endsection

@push('styles')
<style>
	.stat-card-sm .card-body { padding: 1.25rem; }
	.stat-card-sm .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
	.stat-card-sm p { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
	.stat-card-sm h4 { font-size: 1.4rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
	<!-- Filters -->
	<div class="card mb-4">
		<div class="card-header">
			<h6 class="mb-0">Filter Report</h6>
		</div>
		<div class="card-body">
			<form method="GET" action="{{ route('admin.fees.reports.collection') }}" class="row g-3 align-items-end">
				<div class="col-md-3">
					<label class="form-label">Search Student</label>
					<input type="text" name="search" class="form-control" placeholder="Name, admission no. or roll no." value="{{ request('search') }}">
				</div>
				<div class="col-md-2">
					<label class="form-label">From Date</label>
					<input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
				</div>
				<div class="col-md-2">
					<label class="form-label">To Date</label>
					<input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
				</div>
				<div class="col-md-2">
					<label class="form-label">Class</label>
					<select name="class_id" class="form-select">
						<option value="">All Classes</option>
						@foreach($classes as $class)
							<option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary flex-fill">
							<i class="icon-filter me-1"></i> Filter
						</button>
						<a href="{{ route('admin.fees.reports.collection') }}" class="btn btn-outline-secondary" title="Reset">
							<i class="icon-reload"></i>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Student Info Banner (when student selected) -->
	@if($selectedStudent)
	<div class="alert alert-primary d-flex align-items-center mb-4">
		<i class="icon-user me-2" style="font-size: 18px;"></i>
		<div>
			Showing results for <strong>{{ $selectedStudent->full_name }}</strong> ({{ $selectedStudent->admission_no }}) — {{ $selectedStudent->schoolClass->name ?? '' }}
			<a href="{{ route('admin.fees.reports.collection', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="ms-2 text-white">Clear Student Filter</a>
		</div>
	</div>
	@endif

	<!-- Summary Cards -->
	<div class="row mb-4">
		@if($studentStats)
			<!-- Student-specific stats -->
			<div class="col-md-4 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Fees</p>
								<h4 class="mb-0">₹{{ number_format($studentStats['total_fees'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-primary bg-opacity-10">
								<span class="text-primary fw-bold" style="font-size: 18px;">₹</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Paid</p>
								<h4 class="mb-0 text-success">₹{{ number_format($studentStats['total_paid'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-success bg-opacity-10">
								<i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Due Amount</p>
								@if($studentStats['total_due'] > 0)
									<h4 class="mb-0 text-danger">₹{{ number_format($studentStats['total_due'], 2) }}</h4>
								@elseif($studentStats['total_due'] < 0)
									<h4 class="mb-0 text-info">Advance ₹{{ number_format(abs($studentStats['total_due']), 2) }}</h4>
								@else
									<h4 class="mb-0 text-success">All Paid</h4>
								@endif
							</div>
							<div class="stat-icon {{ $studentStats['total_due'] > 0 ? 'bg-danger' : ($studentStats['total_due'] < 0 ? 'bg-info' : 'bg-success') }} bg-opacity-10">
								<i data-feather="{{ $studentStats['total_due'] > 0 ? 'alert-circle' : ($studentStats['total_due'] < 0 ? 'arrow-up-circle' : 'check') }}" class="{{ $studentStats['total_due'] > 0 ? 'text-danger' : ($studentStats['total_due'] < 0 ? 'text-info' : 'text-success') }}" style="width: 20px; height: 20px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		@else
			<!-- Overall stats -->
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Collected</p>
								<h4 class="mb-0 text-success">₹{{ number_format($summary['total_amount'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-success bg-opacity-10">
								<span class="text-success fw-bold" style="font-size: 18px;">₹</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Discount</p>
								<h4 class="mb-0 text-warning">₹{{ number_format($summary['total_discount'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-warning bg-opacity-10">
								<i data-feather="percent" class="text-warning" style="width: 18px; height: 18px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Fine</p>
								<h4 class="mb-0 text-danger">₹{{ number_format($summary['total_fine'], 2) }}</h4>
							</div>
							<div class="stat-icon bg-danger bg-opacity-10">
								<i data-feather="clock" class="text-danger" style="width: 18px; height: 18px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3 mb-3">
				<div class="card stat-card-sm border-0 shadow-sm h-100">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Transactions</p>
								<h4 class="mb-0 text-primary">{{ number_format($summary['total_transactions']) }}</h4>
							</div>
							<div class="stat-icon bg-primary bg-opacity-10">
								<i data-feather="hash" class="text-primary" style="width: 18px; height: 18px;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif
	</div>

	<div class="row">
		<!-- Collection Table -->
		<div class="col-xl-8 mb-4">
			<div class="card" style="border-radius: 12px; border: 1px solid #eee;">
				<div class="card-header d-flex justify-content-between align-items-center" style="background: #f8f9fc; border-bottom: 1px solid #eee; border-radius: 12px 12px 0 0; padding: 14px 20px;">
					<h6 class="mb-0" style="font-weight: 700; color: #2c323f;"><i class="icon-list me-2" style="color: #7366ff;"></i>Collection Details</h6>
					<div class="d-flex gap-2">
						<a href="{{ route('admin.fees.reports.export-excel', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'student_id', 'search'])) }}" class="btn btn-sm btn-outline-success" style="border-radius: 6px; font-size: 11px;">
							<i class="icon-import me-1"></i> Excel
						</a>
						<a href="{{ route('admin.fees.reports.export-pdf', ['type' => 'collection', 'from_date' => $fromDate, 'to_date' => $toDate] + request()->only(['class_id', 'fee_type_id', 'student_id', 'search'])) }}" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; font-size: 11px;">
							<i class="icon-file me-1"></i> PDF
						</a>
					</div>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table mb-0" style="font-size: 13px;">
							<thead>
								<tr style="background: #f0efff;">
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Receipt</th>
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Date</th>
									@if(!$selectedStudent)
										<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Student</th>
										<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Class</th>
									@endif
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;">Fee Type</th>
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #7366ff; font-weight: 700;" class="text-end">Amount</th>
								</tr>
							</thead>
							<tbody>
								@forelse($collections as $collection)
									<tr style="border-bottom: 1px solid #f0f0f0;">
										<td style="padding: 12px 16px;">
											<a href="{{ route('admin.fees.receipt', $collection) }}" style="color: #7366ff; font-weight: 600;">{{ $collection->receipt_no }}</a>
										</td>
										<td style="padding: 12px 16px; color: #888;">{{ $collection->payment_date->format('d M Y') }}</td>
										@if(!$selectedStudent)
											<td style="padding: 12px 16px;">
												<strong>{{ $collection->student->full_name ?? 'N/A' }}</strong>
												<br><small style="color: #aaa;">{{ $collection->student->admission_no ?? '' }}</small>
											</td>
											<td style="padding: 12px 16px; color: #888;">{{ $collection->student->schoolClass->name ?? '-' }}</td>
										@endif
										<td style="padding: 12px 16px;"><span class="badge badge-light-primary" style="font-size: 11px;">{{ $collection->feeStructure->feeType->name ?? '-' }}</span></td>
										<td style="padding: 12px 16px; font-weight: 700; color: #2c323f;" class="text-end">₹{{ number_format($collection->paid_amount, 2) }}</td>
									</tr>
								@empty
									<tr>
										<td colspan="{{ $selectedStudent ? 4 : 6 }}" class="text-center py-5" style="color: #bbb;">
											<i class="icon-info-alt d-block mb-2" style="font-size: 28px;"></i>
											No collections found for the selected filters.
										</td>
									</tr>
								@endforelse
							</tbody>
							@if($collections->count() > 0)
								<tfoot>
									<tr style="background: #f8f9fc;">
										<th colspan="{{ $selectedStudent ? 2 : 4 }}" style="padding: 12px 16px; font-size: 12px; color: #888;">Total ({{ $summary['total_transactions'] }} transactions)</th>
										<th style="padding: 12px 16px;"></th>
										<th style="padding: 12px 16px; font-size: 15px; color: #54BA4A;" class="text-end">₹{{ number_format($summary['total_amount'], 2) }}</th>
									</tr>
								</tfoot>
							@endif
						</table>
					</div>
				</div>
				@if($collections->hasPages())
					<div class="card-footer bg-white" style="border-radius: 0 0 12px 12px;">
						{{ $collections->appends(request()->query())->links() }}
					</div>
				@endif
			</div>
		</div>

		<!-- Daily Breakdown -->
		<div class="col-xl-4 mb-4">
			<div class="card" style="border-radius: 12px; border: 1px solid #eee;">
				<div class="card-header" style="background: #f8f9fc; border-bottom: 1px solid #eee; border-radius: 12px 12px 0 0; padding: 14px 20px;">
					<h6 class="mb-0" style="font-weight: 700; color: #2c323f;"><i class="icon-calendar me-2" style="color: #FFAA05;"></i>Daily Breakdown</h6>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive" style="max-height: 480px;">
						<table class="table mb-0" style="font-size: 13px;">
							<thead>
								<tr style="background: #fff8e6; position: sticky; top: 0; z-index: 2;">
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #FFAA05; font-weight: 700;">Date</th>
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #FFAA05; font-weight: 700;" class="text-center">Count</th>
									<th style="padding: 10px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #FFAA05; font-weight: 700;" class="text-end">Amount</th>
								</tr>
							</thead>
							<tbody>
								@forelse($dailyData as $day)
									<tr style="border-bottom: 1px solid #f0f0f0;">
										<td style="padding: 10px 16px; color: #555;">{{ \Carbon\Carbon::parse($day->date)->format('d M, D') }}</td>
										<td style="padding: 10px 16px;" class="text-center"><span class="badge bg-light text-dark" style="font-size: 11px; min-width: 28px;">{{ $day->count }}</span></td>
										<td style="padding: 10px 16px; font-weight: 700; color: #2c323f;" class="text-end">₹{{ number_format($day->total, 2) }}</td>
									</tr>
								@empty
									<tr>
										<td colspan="3" class="text-center py-5" style="color: #bbb;">
											<i class="icon-calendar d-block mb-2" style="font-size: 28px;"></i>
											No data for this period.
										</td>
									</tr>
								@endforelse
							</tbody>
							@if($dailyData->count() > 0)
								<tfoot>
									<tr style="background: #fff8e6; position: sticky; bottom: 0;">
										<th style="padding: 10px 16px; font-size: 12px; color: #888;">Total</th>
										<th style="padding: 10px 16px; font-size: 13px;" class="text-center">{{ $dailyData->sum('count') }}</th>
										<th style="padding: 10px 16px; font-size: 15px; color: #54BA4A;" class="text-end">₹{{ number_format($dailyData->sum('total'), 2) }}</th>
									</tr>
								</tfoot>
							@endif
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
