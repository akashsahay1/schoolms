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
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Class Timetable</h5>
                        <p class="mb-0 small text-muted">Academic Year: {{ $activeYear ? $activeYear->name : 'No Active Academic Year' }}</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.timetable.conflicts') }}" class="btn btn-outline-warning btn-sm">
                            <i data-feather="alert-triangle" class="me-1"></i> Conflicts
                        </a>
                        <a href="{{ route('admin.timetable.teacher') }}" class="btn btn-outline-info btn-sm">
                            <i data-feather="user" class="me-1"></i> Teacher View
                        </a>
                        <a href="{{ route('admin.timetable.periods') }}" class="btn btn-outline-primary btn-sm">
                            <i data-feather="clock" class="me-1"></i> Manage Periods
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Class/Section Selector -->
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                        <select class="form-select" id="class-select">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Section <span class="text-danger">*</span></label>
                        <select class="form-select" id="section-select">
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
                    <div class="col-md-4">
                        @if($selectedClass && $selectedSection)
                            <a href="{{ route('admin.timetable.print', ['class_id' => $selectedClass->id, 'section_id' => $selectedSection->id]) }}" target="_blank" class="btn btn-outline-success">
                                <i data-feather="printer" class="me-1"></i> Print
                            </a>
                        @endif
                    </div>
                </div>

                @if($selectedClass && $selectedSection)
                    <div class="d-flex align-items-center mb-3 gap-2">
                        <h6 class="mb-0 fw-bold">{{ $selectedClass->name }} - Section {{ $selectedSection->name }}</h6>
                        <span class="badge bg-light text-dark">Click any cell to add or edit</span>
                    </div>

                    @if($periods->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered timetable-grid mb-0">
                                <thead>
                                    <tr>
                                        <th class="tt-day-header" width="110">Day</th>
                                        @foreach($periods as $period)
                                            <th class="text-center tt-period-header {{ $period->type != 'class' ? 'tt-break-header' : '' }}">
                                                <div class="fw-bold">{{ $period->name }}</div>
                                                <small>{{ $period->start_time->format('g:i A') }} - {{ $period->end_time->format('g:i A') }}</small>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($days as $dayKey => $dayName)
                                        <tr>
                                            <td class="tt-day-cell">
                                                <strong>{{ $dayName }}</strong>
                                            </td>
                                            @foreach($periods as $period)
                                                @php
                                                    $entry = null;
                                                    if(isset($timetableData[$dayKey])) {
                                                        $entry = $timetableData[$dayKey]->firstWhere('period_id', $period->id);
                                                    }
                                                @endphp
                                                @if($period->type != 'class')
                                                    <td class="text-center tt-break-cell">
                                                        <div class="tt-break-label">
                                                            <i data-feather="{{ $period->type == 'lunch' ? 'coffee' : 'pause' }}" style="width: 14px; height: 14px;"></i>
                                                            <span>{{ ucfirst($period->type) }}</span>
                                                        </div>
                                                    </td>
                                                @elseif($entry)
                                                    <td class="text-center tt-filled-cell" data-entry-id="{{ $entry->id }}" data-day="{{ $dayKey }}" data-period="{{ $period->id }}" data-subject="{{ $entry->subject_id }}" data-teacher="{{ $entry->teacher_id ?? '' }}" data-room="{{ $entry->room_number ?? '' }}" data-notes="{{ $entry->notes ?? '' }}" role="button">
                                                        <div class="tt-entry">
                                                            <div class="tt-subject">{{ $entry->subject->name ?? 'N/A' }}</div>
                                                            @if($entry->teacher)
                                                                <div class="tt-teacher">{{ $entry->teacher->full_name }}</div>
                                                            @endif
                                                            @if($entry->room_number)
                                                                <div class="tt-room">{{ $entry->room_number }}</div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="text-center tt-empty-cell" data-day="{{ $dayKey }}" data-period="{{ $period->id }}" role="button">
                                                        <div class="tt-add-hint">
                                                            <i data-feather="plus" style="width: 18px; height: 18px;"></i>
                                                        </div>
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex align-items-center gap-4 mt-3">
                            <small class="text-muted"><span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #f0eeff;"></span> Click empty cell to add</small>
                            <small class="text-muted"><span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #e8f5e9;"></span> Click filled cell to edit</small>
                            <small class="text-muted"><span class="d-inline-block rounded" style="width: 12px; height: 12px; background: #fff3e0;"></span> Break / Lunch</small>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i data-feather="clock" style="width: 48px; height: 48px;" class="text-muted"></i>
                            <p class="mt-3 text-muted mb-2">No periods defined yet.</p>
                            <a href="{{ route('admin.timetable.periods') }}" class="btn btn-primary btn-sm">
                                <i data-feather="plus" class="me-1"></i> Add Periods First
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i data-feather="calendar" style="width: 48px; height: 48px;" class="text-muted"></i>
                        </div>
                        <h5 class="text-muted">Select Class and Section</h5>
                        <p class="text-muted mb-0">Choose a class and section above to view and manage their timetable.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Timetable Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryModalTitle">Add Timetable Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="modal-error"></div>
                <input type="hidden" id="entry-id" value="">
                <input type="hidden" id="entry-day" value="">
                <input type="hidden" id="entry-period" value="">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Day & Period</label>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary fs-6 px-3 py-2" id="modal-day-label"></span>
                        <span class="badge bg-secondary fs-6 px-3 py-2" id="modal-period-label"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                    <select class="form-select" id="entry-subject" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Teacher</label>
                    <select class="form-select" id="entry-teacher">
                        <option value="">Select Teacher (Optional)</option>
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->full_name }}{{ $teacher->designation ? ' (' . $teacher->designation->name . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Room Number</label>
                        <input type="text" class="form-control" id="entry-room" placeholder="e.g., Room 101">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" class="form-control" id="entry-notes" placeholder="Optional notes">
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-danger d-none" id="btn-delete-entry">
                        <i data-feather="trash-2" class="me-1"></i> Delete
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btn-save-entry">
                        <i data-feather="save" class="me-1"></i> <span id="btn-save-text">Save</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timetable-grid {
        border-collapse: separate;
        border-spacing: 0;
    }

    .timetable-grid th,
    .timetable-grid td {
        vertical-align: middle;
        border: 1px solid #e8e8e8;
    }

    .tt-day-header {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        padding: 12px !important;
    }

    .tt-period-header {
        background: #f8f9fa;
        padding: 10px 8px !important;
        min-width: 120px;
        font-size: 0.85rem;
    }

    .tt-period-header small {
        color: #6c757d;
        font-size: 0.75rem;
    }

    .tt-break-header {
        background: #fff8e1 !important;
    }

    .tt-day-cell {
        background: #f8f9fa;
        padding: 12px !important;
        font-size: 0.9rem;
    }

    .tt-empty-cell {
        background: #fafafe;
        cursor: pointer;
        padding: 15px 8px !important;
        transition: all 0.2s ease;
    }

    .tt-empty-cell:hover {
        background: #f0eeff;
    }

    .tt-empty-cell:hover .tt-add-hint {
        opacity: 1;
        transform: scale(1);
    }

    .tt-add-hint {
        opacity: 0.3;
        transition: all 0.2s ease;
        color: #7366ff;
        transform: scale(0.8);
    }

    .tt-filled-cell {
        cursor: pointer;
        padding: 8px !important;
        transition: all 0.2s ease;
        background: #fafffe;
    }

    .tt-filled-cell:hover {
        background: #e8f5e9;
        box-shadow: inset 0 0 0 2px #7366ff;
    }

    .tt-entry {
        line-height: 1.4;
    }

    .tt-subject {
        font-weight: 600;
        color: #7366ff;
        font-size: 0.85rem;
        margin-bottom: 2px;
    }

    .tt-teacher {
        font-size: 0.78rem;
        color: #555;
    }

    .tt-room {
        font-size: 0.72rem;
        color: #999;
    }

    .tt-break-cell {
        background: #fff8e1;
        padding: 12px 8px !important;
    }

    .tt-break-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #e65100;
        font-size: 0.8rem;
        font-weight: 500;
    }

    #entryModal .modal-header {
        background: #f8f9fa;
        border-bottom: 2px solid #7366ff;
    }

    #entryModal .modal-title {
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .tt-period-header {
            min-width: 100px;
            font-size: 0.78rem;
        }

        .tt-subject {
            font-size: 0.78rem;
        }

        .tt-teacher,
        .tt-room {
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    var classesData = @json($classes);
    var daysMap = @json($days);
    var periodsMap = {};
    @if(isset($periods) && $periods->count() > 0)
        @foreach($periods as $p)
            periodsMap[{{ $p->id }}] = "{{ $p->name }} ({{ $p->start_time->format('g:i A') }} - {{ $p->end_time->format('g:i A') }})";
        @endforeach
    @endif

    jQuery(document).ready(function() {
        var classSelect = jQuery('#class-select');
        var sectionSelect = jQuery('#section-select');
        var entryModal = new bootstrap.Modal(document.getElementById('entryModal'));
        var selectedClassId = classSelect.val();
        var selectedSectionId = sectionSelect.val();

        // Class change → load sections → auto-navigate
        classSelect.on('change', function() {
            var classId = jQuery(this).val();
            sectionSelect.html('<option value="">Select Section</option>');

            if (classId) {
                var selectedClass = classesData.find(function(c) { return c.id == classId; });
                if (selectedClass && selectedClass.sections) {
                    selectedClass.sections.forEach(function(section) {
                        sectionSelect.append('<option value="' + section.id + '">' + section.name + '</option>');
                    });
                    // Auto-select first section if only one
                    if (selectedClass.sections.length === 1) {
                        sectionSelect.val(selectedClass.sections[0].id).trigger('change');
                    }
                }
            }
        });

        // Section change → navigate to timetable
        sectionSelect.on('change', function() {
            var classId = classSelect.val();
            var sectionId = jQuery(this).val();
            if (classId && sectionId) {
                window.location.href = '{{ route("admin.timetable.index") }}?class_id=' + classId + '&section_id=' + sectionId;
            }
        });

        // Click empty cell → open Add modal
        jQuery('.tt-empty-cell').on('click', function() {
            var day = jQuery(this).data('day');
            var periodId = jQuery(this).data('period');

            jQuery('#entry-id').val('');
            jQuery('#entry-day').val(day);
            jQuery('#entry-period').val(periodId);
            jQuery('#entry-subject').val('');
            jQuery('#entry-teacher').val('');
            jQuery('#entry-room').val('');
            jQuery('#entry-notes').val('');
            jQuery('#modal-error').addClass('d-none');
            jQuery('#entryModalTitle').text('Add Timetable Entry');
            jQuery('#btn-save-text').text('Save');
            jQuery('#btn-delete-entry').addClass('d-none');
            jQuery('#modal-day-label').text(daysMap[day] || day);
            jQuery('#modal-period-label').text(periodsMap[periodId] || '');

            entryModal.show();
        });

        // Click filled cell → open Edit modal
        jQuery('.tt-filled-cell').on('click', function() {
            var entryId = jQuery(this).data('entry-id');
            var day = jQuery(this).data('day');
            var periodId = jQuery(this).data('period');

            jQuery('#entry-id').val(entryId);
            jQuery('#entry-day').val(day);
            jQuery('#entry-period').val(periodId);
            jQuery('#entry-subject').val(jQuery(this).data('subject'));
            jQuery('#entry-teacher').val(jQuery(this).data('teacher'));
            jQuery('#entry-room').val(jQuery(this).data('room'));
            jQuery('#entry-notes').val(jQuery(this).data('notes'));
            jQuery('#modal-error').addClass('d-none');
            jQuery('#entryModalTitle').text('Edit Timetable Entry');
            jQuery('#btn-save-text').text('Update');
            jQuery('#btn-delete-entry').removeClass('d-none');
            jQuery('#modal-day-label').text(daysMap[day] || day);
            jQuery('#modal-period-label').text(periodsMap[periodId] || '');

            entryModal.show();
        });

        // Save / Update entry
        jQuery('#btn-save-entry').on('click', function() {
            var entryId = jQuery('#entry-id').val();
            var subjectId = jQuery('#entry-subject').val();

            if (!subjectId) {
                jQuery('#modal-error').text('Please select a subject.').removeClass('d-none');
                return;
            }

            var btn = jQuery(this);
            btn.prop('disabled', true);
            jQuery('#modal-error').addClass('d-none');

            if (entryId) {
                // UPDATE existing entry
                jQuery.ajax({
                    url: '/admin/timetable/' + entryId,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        subject_id: subjectId,
                        teacher_id: jQuery('#entry-teacher').val() || null,
                        room_number: jQuery('#entry-room').val() || null,
                        notes: jQuery('#entry-notes').val() || null
                    },
                    success: function(res) {
                        entryModal.hide();
                        location.reload();
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                        jQuery('#modal-error').text(msg).removeClass('d-none');
                        btn.prop('disabled', false);
                    }
                });
            } else {
                // CREATE new entry
                jQuery.ajax({
                    url: '{{ route("admin.timetable.store") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        class_id: '{{ $selectedClass->id ?? '' }}',
                        section_id: '{{ $selectedSection->id ?? '' }}',
                        day: jQuery('#entry-day').val(),
                        period_id: jQuery('#entry-period').val(),
                        subject_id: subjectId,
                        teacher_id: jQuery('#entry-teacher').val() || null,
                        room_number: jQuery('#entry-room').val() || null,
                        notes: jQuery('#entry-notes').val() || null
                    },
                    success: function(res) {
                        entryModal.hide();
                        location.reload();
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                        jQuery('#modal-error').text(msg).removeClass('d-none');
                        btn.prop('disabled', false);
                    }
                });
            }
        });

        // Delete entry
        jQuery('#btn-delete-entry').on('click', function() {
            var entryId = jQuery('#entry-id').val();
            if (!entryId) return;

            Swal.fire({
                title: 'Delete this entry?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fc564a',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    jQuery.ajax({
                        url: '/admin/timetable/' + entryId,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            entryModal.hide();
                            location.reload();
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete entry.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
