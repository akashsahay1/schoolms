@extends('layouts.app')

@section('title', 'Transport Reports')

@section('page-title', 'Transport Reports Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Transport</li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@push('styles')
<style>
    .transport-stat {
        border: none;
        border-radius: 14px;
        transition: transform 0.15s ease;
    }
    .transport-stat:hover {
        transform: translateY(-2px);
    }
    .transport-stat .card-body {
        padding: 1.5rem;
    }
    .transport-stat .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .transport-stat .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #7f8c8d;
        margin-bottom: 4px;
        font-weight: 500;
    }
    .transport-stat .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .report-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }
    .report-card:hover {
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transform: translateY(-2px);
        color: inherit;
    }
    .report-card .report-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 14px;
    }
    .report-card .report-title {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        margin-bottom: 2px;
    }
    .report-card .report-desc {
        font-size: 12px;
        color: #95a5a6;
        margin: 0;
    }
    .report-card .arrow-icon {
        color: #bdc3c7;
        transition: transform 0.2s ease;
    }
    .report-card:hover .arrow-icon {
        transform: translateX(4px);
        color: #7f8c8d;
    }
    .vehicle-card {
        border: 1px solid #f0f2f5;
        border-radius: 12px;
        padding: 1.25rem;
        transition: box-shadow 0.2s ease;
    }
    .vehicle-card:hover {
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }
    .vehicle-card .vehicle-no {
        font-weight: 700;
        font-size: 14px;
        color: #2c3e50;
    }
    .vehicle-card .vehicle-model {
        font-size: 11px;
        color: #95a5a6;
    }
    .vehicle-card .progress {
        height: 8px;
        border-radius: 4px;
        background: #f0f2f5;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card transport-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(115, 102, 255, 0.08);">
                            <i class="icon-car" style="font-size: 24px; color: #7366ff;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Active Vehicles</div>
                            <div class="stat-value" style="color: #7366ff;">{{ $activeVehicles }}<span class="text-muted fw-normal" style="font-size: 14px;">/{{ $totalVehicles }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card transport-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(39, 174, 96, 0.08);">
                            <i class="icon-direction-alt" style="font-size: 24px; color: #27ae60;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Active Routes</div>
                            <div class="stat-value text-success">{{ $activeRoutes }}<span class="text-muted fw-normal" style="font-size: 14px;">/{{ $totalRoutes }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card transport-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(52, 152, 219, 0.08);">
                            <i class="icon-user" style="font-size: 24px; color: #3498db;"></i>
                        </div>
                        <div>
                            <div class="stat-label">Students Assigned</div>
                            <div class="stat-value text-primary">{{ $activeAssignments }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card transport-stat shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background: rgba(230, 126, 34, 0.08);">
                            <span style="font-size: 22px; font-weight: 700; color: #e67e22;">₹</span>
                        </div>
                        <div>
                            <div class="stat-label">Est. Monthly Revenue</div>
                            <div class="stat-value" style="color: #e67e22;">₹{{ number_format($monthlyRevenue, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Quick Reports -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Quick Reports</h6>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    <a href="{{ route('admin.transport.reports.route-wise') }}" class="report-card">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: rgba(115, 102, 255, 0.08);">
                                <i class="icon-location-pin" style="font-size: 20px; color: #7366ff;"></i>
                            </div>
                            <div>
                                <div class="report-title">Route-wise Students</div>
                                <p class="report-desc">Students per transport route</p>
                            </div>
                        </div>
                        <i class="icon-angle-right arrow-icon" style="font-size: 18px;"></i>
                    </a>
                    <a href="{{ route('admin.transport.reports.class-wise') }}" class="report-card">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: rgba(39, 174, 96, 0.08);">
                                <i class="icon-book" style="font-size: 20px; color: #27ae60;"></i>
                            </div>
                            <div>
                                <div class="report-title">Class-wise Students</div>
                                <p class="report-desc">Transport usage by class</p>
                            </div>
                        </div>
                        <i class="icon-angle-right arrow-icon" style="font-size: 18px;"></i>
                    </a>
                    <a href="{{ route('admin.transport.reports.vehicle-wise') }}" class="report-card">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: rgba(230, 126, 34, 0.08);">
                                <i class="icon-car" style="font-size: 20px; color: #e67e22;"></i>
                            </div>
                            <div>
                                <div class="report-title">Vehicle-wise Students</div>
                                <p class="report-desc">Students assigned per vehicle</p>
                            </div>
                        </div>
                        <i class="icon-angle-right arrow-icon" style="font-size: 18px;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Route-wise Student Count -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Route-wise Student Count</h6>
                        <a href="{{ route('admin.transport.reports.route-wise') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="icon-angle-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($routeWiseCount->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 35%;">Route</th>
                                        <th style="width: 25%;">Vehicle</th>
                                        <th style="width: 20%;" class="text-center">Students</th>
                                        <th style="width: 20%;" class="text-end">Fare</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($routeWiseCount as $route)
                                        <tr>
                                            <td class="fw-medium">{{ $route->route_name }}</td>
                                            <td>
                                                @if($route->vehicle)
                                                    <span class="badge badge-light-info px-2">{{ $route->vehicle->vehicle_no }}</span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light-primary px-3 py-1">{{ $route->assignments_count }}</span>
                                            </td>
                                            <td class="text-end fw-medium">₹{{ number_format($route->fare_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="icon-direction-alt" style="font-size: 24px; color: #95a5a6;"></i>
                            </div>
                            <p class="text-muted mb-0">No active routes found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Vehicle Capacity Utilization -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Vehicle Capacity Utilization</h6>
                </div>
                <div class="card-body">
                    @if($vehicleUtilization->count() > 0)
                        <div class="row g-3">
                            @foreach($vehicleUtilization as $vehicle)
                                @php
                                    $util = $vehicle['utilization'];
                                    $colorClass = $util > 90 ? 'danger' : ($util > 70 ? 'warning' : 'success');
                                    $colorHex = $util > 90 ? '#e74c3c' : ($util > 70 ? '#e67e22' : '#27ae60');
                                @endphp
                                <div class="col-xl-3 col-md-4 col-sm-6">
                                    <div class="vehicle-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="vehicle-no">{{ $vehicle['vehicle_no'] }}</div>
                                                @if(!empty($vehicle['model']))
                                                    <div class="vehicle-model">{{ $vehicle['model'] }}</div>
                                                @endif
                                            </div>
                                            <span class="badge badge-light-{{ $colorClass }} px-2 py-1" style="font-size: 12px;">{{ $util }}%</span>
                                        </div>
                                        <div class="progress mb-2">
                                            <div class="progress-bar bg-{{ $colorClass }}" role="progressbar" style="width: {{ min($util, 100) }}%; border-radius: 4px;"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $vehicle['assigned'] }} / {{ $vehicle['capacity'] }} seats</small>
                                            <small style="color: {{ $colorHex }}; font-weight: 600;">
                                                @if($util > 90)
                                                    Near Full
                                                @elseif($util > 70)
                                                    Moderate
                                                @else
                                                    Available
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="icon-car" style="font-size: 24px; color: #95a5a6;"></i>
                            </div>
                            <p class="text-muted mb-0">No active vehicles found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
