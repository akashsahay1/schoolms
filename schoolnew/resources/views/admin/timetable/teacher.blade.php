@extends('layouts.app')

@section('title', 'Teacher Timetable')
@section('page-title', 'Teacher Timetable')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.timetable.index') }}">Timetable</a></li>
    <li class="breadcrumb-item active">Teacher Timetable</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Selection Panel -->
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Select Teacher</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.timetable.teacher') }}" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Teacher --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }}
                                        @if($teacher->designation)
                                            ({{ $teacher->designation->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">View Timetable</button>
                        </div>
                        @if($selectedTeacher)
                            <div class="col-md-3 text-end">
                                <a href="{{ route('admin.timetable.teacher.print', ['teacher_id' => $selectedTeacher->id]) }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fa fa-print me-1"></i> Print Timetable
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        @if($selectedTeacher)
            <!-- Teacher Info -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ $selectedTeacher->first_name }} {{ $selectedTeacher->last_name }}'s Timetable</h5>
                            <span class="badge bg-primary">{{ $activeYear->name ?? 'No Active Year' }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($periods->count() > 0 && $timetableData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th style="width: 10%;">Period</th>
                                            <th style="width: 10%;">Time</th>
                                            @foreach($days as $day)
                                                <th style="width: 13.33%;" class="{{ strtolower(now()->format('l')) === $day ? 'bg-info' : '' }}">
                                                    {{ ucfirst($day) }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($periods as $period)
                                            <tr class="{{ $period->type !== 'class' ? 'bg-light' : '' }}">
                                                <td class="fw-medium">{{ $period->name }}</td>
                                                <td class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($period->start_time)->format('h:i A') }}<br>
                                                    {{ \Carbon\Carbon::parse($period->end_time)->format('h:i A') }}
                                                </td>
                                                @foreach($days as $day)
                                                    <td class="{{ strtolower(now()->format('l')) === $day ? 'bg-light-info' : '' }}">
                                                        @if($period->type === 'break' || $period->type === 'lunch')
                                                            <span class="text-muted">{{ ucfirst($period->type) }}</span>
                                                        @else
                                                            @php
                                                                $entry = $timetableData->get($day)?->firstWhere('period_id', $period->id);
                                                            @endphp
                                                            @if($entry)
                                                                <div class="fw-medium">{{ $entry->subject->name ?? '-' }}</div>
                                                                <small class="text-muted">
                                                                    {{ $entry->schoolClass->name ?? '' }} - {{ $entry->section->name ?? '' }}
                                                                </small>
                                                                @if($entry->room)
                                                                    <br><small class="text-info">Room: {{ $entry->room }}</small>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">Free</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#clock') }}"></use>
                                </svg>
                                <h6 class="mt-3 text-muted">No Timetable Entries</h6>
                                <p class="text-muted">This teacher has no classes assigned yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                            <use href="{{ asset('assets/svg/icon-sprite.svg#users') }}"></use>
                        </svg>
                        <h6 class="mt-3 text-muted">Select a Teacher</h6>
                        <p class="text-muted">Choose a teacher from the dropdown to view their timetable.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
