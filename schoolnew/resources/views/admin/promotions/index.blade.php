@extends('layouts.app')

@section('title', 'Student Promotion')
@section('page-title', 'Student Promotion')

@section('breadcrumb')
    <li class="breadcrumb-item active">Student Promotion</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body success">
                    <span class="f-light">Promoted</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['total_promoted'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#arrow-up-circle') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body danger">
                    <span class="f-light">Retained</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['total_retained'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#refresh-cw') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body info">
                    <span class="f-light">Alumni</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['total_alumni'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#award') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card small-widget">
                <div class="card-body warning">
                    <span class="f-light">Pending</span>
                    <div class="d-flex align-items-end gap-1">
                        <h4>{{ $stats['pending'] }}</h4>
                    </div>
                    <div class="bg-gradient">
                        <svg class="stroke-icon svg-fill">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Student Promotion Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary w-100 py-3">
                                <i class="fa fa-plus-circle mb-2 d-block fa-2x"></i>
                                New Promotion
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.promotions.rules') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fa fa-cogs mb-2 d-block fa-2x"></i>
                                Promotion Rules
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.promotions.history') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fa fa-history mb-2 d-block fa-2x"></i>
                                Promotion History
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fa fa-users mb-2 d-block fa-2x"></i>
                                View Students
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Batches -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Recent Promotion Batches</h5>
                        <a href="{{ route('admin.promotions.history') }}" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentBatches->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>From Year</th>
                                        <th>To Year</th>
                                        <th>Class</th>
                                        <th>Total</th>
                                        <th>Promoted</th>
                                        <th>Retained</th>
                                        <th>Alumni</th>
                                        <th>Status</th>
                                        <th>Processed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBatches as $index => $batch)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $batch->fromAcademicYear->name ?? '-' }}</td>
                                            <td>{{ $batch->toAcademicYear->name ?? '-' }}</td>
                                            <td>{{ $batch->fromClass->name ?? '-' }}</td>
                                            <td>{{ $batch->total_students }}</td>
                                            <td><span class="badge bg-success">{{ $batch->promoted_count }}</span></td>
                                            <td><span class="badge bg-danger">{{ $batch->retained_count }}</span></td>
                                            <td><span class="badge bg-info">{{ $batch->alumni_count }}</span></td>
                                            <td>{!! $batch->status_badge !!}</td>
                                            <td>{{ $batch->processed_at ? $batch->processed_at->format('M d, Y H:i') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#users') }}"></use>
                            </svg>
                            <h6 class="mt-3 text-muted">No Promotion Batches</h6>
                            <p class="text-muted">No promotions have been processed yet.</p>
                            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-2"></i> Start New Promotion
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
