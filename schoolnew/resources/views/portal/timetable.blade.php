@extends('layouts.portal')

@section('title', 'Timetable')
@section('page-title', 'Class Timetable')

@section('breadcrumb')
    <li class="breadcrumb-item active">Timetable</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{ $student->schoolClass->name ?? 'Class' }} - {{ $student->section->name ?? 'Section' }} Timetable</h5>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fa fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th style="width: 10%;">Period</th>
                                    <th style="width: 10%;">Time</th>
                                    @foreach($days as $day)
                                        <th style="width: 13.33%;" class="{{ strtolower(now()->format('l')) === $day ? 'bg-light-primary' : '' }}">
                                            {{ ucfirst($day) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periods as $period)
                                    <tr class="{{ $period->type !== 'class' ? 'bg-light text-dark' : '' }}">
                                        <td class="fw-medium">{{ $period->name }}</td>
                                        <td class="text-muted small">
                                            {{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }}<br>
                                            {{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}
                                        </td>
                                        @foreach($days as $day)
                                            <td class="{{ strtolower(now()->format('l')) === $day ? 'bg-light-primary' : '' }}">
                                                @if($period->type === 'break' || $period->type === 'lunch')
                                                    <span class="text-muted">{{ ucfirst($period->type) }}</span>
                                                @else
                                                    @php
                                                        $entry = $timetable->get($day)?->firstWhere('period_id', $period->id);
                                                    @endphp
                                                    @if($entry)
                                                        <div class="fw-medium">{{ $entry->subject->name ?? '-' }}</div>
                                                        <small class="text-muted">{{ $entry->teacher->first_name ?? '' }} {{ $entry->teacher->last_name ?? '' }}</small>
                                                        @if($entry->room)
                                                            <br><small class="text-info">Room: {{ $entry->room }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .sidebar-wrapper, .page-header, .breadcrumb, .btn { display: none !important; }
        .page-body { margin: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush
@endsection
