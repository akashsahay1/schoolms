@extends('layouts.app')

@section('title', 'Transport Fee Reports')

@section('page-title', 'Transport - Fee Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transport.fees.index') }}">Transport Fees</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.transport.fees.reports') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Route-wise Collection Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Students</th>
                                <th>Total Due</th>
                                <th>Total Collected</th>
                                <th>Pending</th>
                                <th>Collection Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotalDue = 0; $grandTotalCollected = 0; @endphp
                            @forelse($routeSummary as $summary)
                                @php
                                    $grandTotalDue += $summary['total_due'];
                                    $grandTotalCollected += $summary['total_collected'];
                                    $collectionRate = $summary['total_due'] > 0 ? ($summary['total_collected'] / $summary['total_due']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $summary['route']->title }}</strong>
                                        <br><small class="text-muted">{{ $summary['route']->start_point }} - {{ $summary['route']->end_point }}</small>
                                    </td>
                                    <td>{{ $summary['students_count'] }}</td>
                                    <td>₹{{ number_format($summary['total_due'], 2) }}</td>
                                    <td class="text-success">₹{{ number_format($summary['total_collected'], 2) }}</td>
                                    <td>
                                        <span class="badge badge-light-warning">{{ $summary['pending_count'] }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $collectionRate >= 80 ? 'success' : ($collectionRate >= 50 ? 'warning' : 'danger') }}" style="width: {{ $collectionRate }}%">
                                                {{ number_format($collectionRate, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted">No data available.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($routeSummary->count() > 0)
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td>{{ $routeSummary->sum('students_count') }}</td>
                                    <td>₹{{ number_format($grandTotalDue, 2) }}</td>
                                    <td class="text-success">₹{{ number_format($grandTotalCollected, 2) }}</td>
                                    <td>{{ $routeSummary->sum('pending_count') }}</td>
                                    <td>
                                        @php $overallRate = $grandTotalDue > 0 ? ($grandTotalCollected / $grandTotalDue) * 100 : 0; @endphp
                                        {{ number_format($overallRate, 1) }}%
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Monthly Collection Trend</h5>
            </div>
            <div class="card-body">
                @if($monthlyTrend->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Collection</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $maxCollection = $monthlyTrend->max('total'); @endphp
                                @foreach($monthlyTrend as $trend)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trend->month . '-01')->format('F Y') }}</td>
                                        <td class="text-success">₹{{ number_format($trend->total, 2) }}</td>
                                        <td style="width: 50%;">
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-primary" style="width: {{ ($trend->total / $maxCollection) * 100 }}%">
                                                    ₹{{ number_format($trend->total, 0) }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No collection data available for this period.</p>
                @endif
            </div>
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
