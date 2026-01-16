@extends('layouts.app')

@section('title', 'Timetable')

@section('page-title', 'Timetable')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Timetable</li>
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

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Class Timetable</h5>
                        <p class="mb-0">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.timetable.periods') }}" class="btn btn-outline-primary me-2">
                            <i data-feather="clock" class="me-1"></i> Manage Periods
                        </a>
                        <a href="{{ route('admin.timetable.create') }}" class="btn btn-primary">
                            <i data-feather="plus" class="me-1"></i> Add Entry
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.timetable.index') }}" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select" id="class-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" class="form-select" id="section-select" required>
                            <option value="">Select Section</option>
                            @if($selectedClass)
                                @foreach($selectedClass->sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">View Timetable</button>
                    </div>
                </form>

                @if($selectedClass && $selectedSection)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">{{ $selectedClass->name }} - Section {{ $selectedSection->name }}</h6>
                        <div>
                            <a href="{{ route('admin.timetable.print', ['class_id' => $selectedClass->id, 'section_id' => $selectedSection->id]) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                <i data-feather="printer" class="icon-xs"></i> Printable Version
                            </a>
                        </div>
                    </div>

                    @if($periods->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered timetable-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="120">Day / Period</th>
                                        @foreach($periods as $period)
                                            <th class="text-center {{ $period->type != 'class' ? 'bg-light' : '' }}">
                                                <div><strong>{{ $period->name }}</strong></div>
                                                <small class="text-muted">{{ $period->start_time->format('g:i A') }} - {{ $period->end_time->format('g:i A') }}</small>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($days as $dayKey => $dayName)
                                        <tr>
                                            <td class="fw-bold bg-light">{{ $dayName }}</td>
                                            @foreach($periods as $period)
                                                @php
                                                    $entry = null;
                                                    if(isset($timetableData[$dayKey])) {
                                                        $entry = $timetableData[$dayKey]->firstWhere('period_id', $period->id);
                                                    }
                                                @endphp
                                                <td class="text-center {{ $period->type != 'class' ? 'bg-light' : '' }}">
                                                    @if($period->type != 'class')
                                                        <span class="text-muted">{{ ucfirst($period->type) }}</span>
                                                    @elseif($entry)
                                                        <div class="timetable-entry">
                                                            <strong class="text-primary">{{ $entry->subject->name ?? 'N/A' }}</strong>
                                                            @if($entry->teacher)
                                                                <br><small>{{ $entry->teacher->full_name ?? 'N/A' }}</small>
                                                            @endif
                                                            @if($entry->room_number)
                                                                <br><small class="text-muted">Room: {{ $entry->room_number }}</small>
                                                            @endif
                                                            <div class="mt-1">
                                                                <form action="{{ route('admin.timetable.destroy', $entry) }}" method="POST" class="d-inline delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-link btn-sm text-danger p-0 delete-confirm" data-name="{{ $entry->subject->name }}">
                                                                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <a href="{{ route('admin.timetable.create', ['class_id' => $selectedClass->id, 'section_id' => $selectedSection->id, 'day' => $dayKey, 'period_id' => $period->id]) }}" class="btn btn-link btn-sm text-muted">
                                                            <i data-feather="plus" style="width: 16px; height: 16px;"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No periods defined. Please <a href="{{ route('admin.timetable.periods') }}">add periods</a> first.</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i data-feather="calendar" style="width: 48px; height: 48px;" class="text-muted"></i>
                        </div>
                        <h5 class="text-muted">Select Class and Section</h5>
                        <p class="text-muted">Choose a class and section to view their timetable.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const classesData = @json($classes);

    jQuery(document).ready(function() {
        jQuery('#class-select').on('change', function() {
            var classId = jQuery(this).val();
            var sectionSelect = jQuery('#section-select');

            sectionSelect.html('<option value="">Select Section</option>');

            if (classId) {
                var selectedClass = classesData.find(c => c.id == classId);
                if (selectedClass && selectedClass.sections) {
                    selectedClass.sections.forEach(function(section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .timetable-table td {
        vertical-align: middle;
        min-width: 120px;
    }
    .timetable-entry {
        font-size: 0.85rem;
    }
    @media print {
        .btn, .breadcrumb, form {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
