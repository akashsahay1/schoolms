@extends('layouts.app')

@section('title', 'Transport Reports')

@section('page-title', 'Transport - Reports Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Transport</li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round primary">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#maps') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $activeVehicles }}/{{ $totalVehicles }}</h4>
                        <span class="f-light">Active Vehicles</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round success">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#task') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $activeRoutes }}/{{ $totalRoutes }}</h4>
                        <span class="f-light">Active Routes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round secondary">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#user') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>{{ $activeAssignments }}</h4>
                        <span class="f-light">Students Assigned</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card widget-1 widget-hover">
            <div class="card-body">
                <div class="widget-content">
                    <div class="widget-round warning">
                        <div class="bg-round">
                            <svg class="svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#ecommerce') }}"></use>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h4>₹{{ number_format($monthlyRevenue, 0) }}</h4>
                        <span class="f-light">Est. Monthly Revenue</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Links -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Quick Reports</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="{{ route('admin.transport.reports.route-wise') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i data-feather="map-pin" class="me-2" style="width: 16px; height: 16px;"></i> Route-wise Students</span>
                        <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                    </a>
                    <a href="{{ route('admin.transport.reports.class-wise') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i data-feather="book" class="me-2" style="width: 16px; height: 16px;"></i> Class-wise Students</span>
                        <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                    </a>
                    <a href="{{ route('admin.transport.reports.vehicle-wise') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i data-feather="truck" class="me-2" style="width: 16px; height: 16px;"></i> Vehicle-wise Students</span>
                        <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Route-wise Student Count -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Route-wise Student Count</h5>
            </div>
            <div class="card-body">
                @if($routeWiseCount->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Students</th>
                                    <th>Fare</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routeWiseCount as $route)
                                    <tr>
                                        <td><strong>{{ $route->route_name }}</strong></td>
                                        <td>
                                            @if($route->vehicle)
                                                <span class="badge badge-light-info">{{ $route->vehicle->vehicle_no }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ $route->assignments_count }}</span>
                                        </td>
                                        <td>₹{{ number_format($route->fare_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No active routes found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Vehicle Utilization -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Vehicle Capacity Utilization</h5>
            </div>
            <div class="card-body">
                @if($vehicleUtilization->count() > 0)
                    <div class="row">
                        @foreach($vehicleUtilization as $vehicle)
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>{{ $vehicle['vehicle_no'] }}</strong>
                                        <span class="badge badge-light-{{ $vehicle['utilization'] > 90 ? 'danger' : ($vehicle['utilization'] > 70 ? 'warning' : 'success') }}">
                                            {{ $vehicle['utilization'] }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-{{ $vehicle['utilization'] > 90 ? 'danger' : ($vehicle['utilization'] > 70 ? 'warning' : 'success') }}" role="progressbar" style="width: {{ min($vehicle['utilization'], 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $vehicle['assigned'] }} / {{ $vehicle['capacity'] }} seats used</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-4">No active vehicles found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
