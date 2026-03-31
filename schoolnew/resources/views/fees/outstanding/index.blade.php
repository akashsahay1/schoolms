@extends('layouts.app')

@section('title', 'Outstanding Fees Report')

@section('page-title', 'Outstanding Fees Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Outstanding Fees</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Filter Outstanding Fees</h5>
                <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.fees.outstanding') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Search Student</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, admission no. or roll no." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill"><i class="icon-filter me-1"></i> Filter</button>
                            @if(request()->hasAny(['search', 'class_id']))
                                <a href="{{ route('admin.fees.outstanding') }}" class="btn btn-outline-secondary" title="Reset"><i class="icon-reload"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        @if($outstandingData->count() > 0)
                            <button type="button" class="btn btn-success w-100" onclick="window.print()">
                                <i class="icon-printer me-1"></i> Print
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($activeYear)
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Outstanding Fees Summary</h5>
                    <div class="text-end">
                        <h6 class="mb-0 text-primary">Total Outstanding: ₹{{ number_format($totalOutstanding, 2) }}</h6>
                        <small class="text-muted">{{ $outstandingData->count() }} students with pending fees</small>
                    </div>
                </div>
                
                @if($outstandingData->count() > 0)
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Student Details</th>
                                        <th>Class & Section</th>
                                        <th>Outstanding Fees</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($outstandingData as $index => $data)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($data['student']->photo)
                                                        <img src="{{ asset('storage/' . $data['student']->photo) }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 13px; flex-shrink: 0;">
                                                            {{ strtoupper(substr($data['student']->first_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $data['student']->full_name }}</strong>
                                                        <br><small class="text-muted">{{ $data['student']->admission_no }}@if($data['student']->roll_no) &bull; Roll: {{ $data['student']->roll_no }}@endif</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $data['student']->schoolClass->name ?? '-' }}{{ $data['student']->section ? ' - ' . $data['student']->section->name : '' }}</td>
                                            <td>
                                                @foreach($data['outstanding_fees'] as $fee)
                                                    <div class="d-flex align-items-center gap-2 py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                        <span class="badge badge-light-warning">{{ $fee->feeType->name }}</span>
                                                        <span class="text-danger">₹{{ number_format($fee->amount, 2) }}</span>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>
                                                <strong class="text-danger">₹{{ number_format($data['total_amount'], 2) }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.fees.collect', $data['student']) }}" class="btn btn-primary btn-sm">
                                                    <i data-feather="credit-card" class="icon-xs"></i> Collect
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i data-feather="check-circle" class="icon-lg text-success"></i>
                        </div>
                        <h5 class="text-success">No Outstanding Fees!</h5>
                        <p class="text-muted">
                            @if($selectedClass)
                                All students in {{ $selectedClass->name }} have paid their fees.
                            @else
                                All students have paid their fees for the current academic year.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        @else
            <div class="card mt-3">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i data-feather="alert-circle" class="icon-lg text-warning"></i>
                    </div>
                    <h5 class="text-warning">No Active Academic Year</h5>
                    <p class="text-muted">Please set an active academic year to generate outstanding fees report.</p>
                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-primary">Manage Academic Years</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .breadcrumb, .card-header h5:first-child {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background: none !important;
            border: none !important;
            text-align: center !important;
        }
        
        .table {
            font-size: 12px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        body {
            font-size: 12px;
        }
    }
</style>
@endpush