@extends('layouts.app')

@section('title', 'Timetable Conflicts')
@section('page-title', 'Timetable Conflicts')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
    <li class="breadcrumb-item active">Conflicts</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        @if(!$activeYear)
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    No active academic year found. Please set an active academic year first.
                </div>
            </div>
        @else
            <!-- Conflicts Summary -->
            <div class="col-md-6 col-sm-12">
                <div class="card small-widget">
                    <div class="card-body {{ $conflicts->where('type', 'Teacher Conflict')->count() > 0 ? 'danger' : 'success' }}">
                        <span class="f-light">Teacher Conflicts</span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>{{ $conflicts->where('type', 'Teacher Conflict')->count() }}</h4>
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#user') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card small-widget">
                    <div class="card-body {{ $conflicts->where('type', 'Room Conflict')->count() > 0 ? 'warning' : 'success' }}">
                        <span class="f-light">Room Conflicts</span>
                        <div class="d-flex align-items-end gap-1">
                            <h4>{{ $conflicts->where('type', 'Room Conflict')->count() }}</h4>
                        </div>
                        <div class="bg-gradient">
                            <svg class="stroke-icon svg-fill">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#home') }}"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conflicts List -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Detected Conflicts</h5>
                            <a href="{{ route('admin.timetable.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-arrow-left me-1"></i> Back to Timetable
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($conflicts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 15%;">Type</th>
                                            <th style="width: 45%;">Description</th>
                                            <th style="width: 15%;">Day</th>
                                            <th style="width: 20%;">Period</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($conflicts as $index => $conflict)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if($conflict['type'] == 'Teacher Conflict')
                                                        <span class="badge bg-danger">{{ $conflict['type'] }}</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ $conflict['type'] }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $conflict['description'] }}</td>
                                                <td>{{ $conflict['day'] }}</td>
                                                <td>{{ $conflict['period'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <svg class="stroke-icon text-success" style="width: 60px; height: 60px;">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#check-circle') }}"></use>
                                </svg>
                                <h6 class="mt-3 text-success">No Conflicts Detected!</h6>
                                <p class="text-muted">Your timetable has no scheduling conflicts.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
