@extends('layouts.app')

@section('title', 'New Promotion')
@section('page-title', 'New Student Promotion')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Promotions</a></li>
    <li class="breadcrumb-item active">New Promotion</li>
@endsection

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.promotions.process') }}" method="POST" id="promotionForm">
        @csrf
        <div class="row">
            <!-- Selection Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Promotion Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">From Academic Year <span class="text-danger">*</span></label>
                            <select name="from_academic_year_id" id="fromAcademicYear" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $currentAcademicYear && $currentAcademicYear->id == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} {{ $year->is_active ? '(Active)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To Academic Year <span class="text-danger">*</span></label>
                            <select name="to_academic_year_id" id="toAcademicYear" class="form-select" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">From Class <span class="text-danger">*</span></label>
                            <select name="from_class_id" id="fromClass" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">From Section</label>
                            <select name="from_section_id" id="fromSection" class="form-select">
                                <option value="">All Sections</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To Class <span class="text-danger">*</span></label>
                            <select name="to_class_id" id="toClass" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To Section</label>
                            <select name="to_section_id" id="toSection" class="form-select">
                                <option value="">Select Section</option>
                            </select>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes about this promotion batch..."></textarea>
                        </div>

                        <button type="button" class="btn btn-primary w-100 mb-2" id="loadStudents">
                            <i class="fa fa-search me-2"></i> Load Students
                        </button>
                    </div>
                </div>

                <!-- Promotion Rule Info -->
                <div class="card" id="ruleCard" style="display: none;">
                    <div class="card-header pb-0">
                        <h5>Promotion Criteria</h5>
                    </div>
                    <div class="card-body">
                        <div id="ruleInfo"></div>
                    </div>
                </div>
            </div>

            <!-- Students Panel -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Students (<span id="studentCount">0</span>)</h5>
                            <div id="bulkActions" style="display: none;">
                                <button type="button" class="btn btn-sm btn-success" id="promoteAll">Promote All Eligible</button>
                                <button type="button" class="btn btn-sm btn-danger" id="retainAll">Retain All</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="studentsContainer">
                            <div class="text-center py-5">
                                <svg class="stroke-icon" style="width: 60px; height: 60px; opacity: 0.5;">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#users') }}"></use>
                                </svg>
                                <h6 class="mt-3 text-muted">Select Options to Load Students</h6>
                                <p class="text-muted">Choose academic year and class, then click "Load Students"</p>
                            </div>
                        </div>

                        <div id="studentsTable" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th style="width: 5%;">#</th>
                                            <th style="width: 25%;">Student</th>
                                            <th style="width: 10%;">Roll No</th>
                                            <th style="width: 12%;">Attendance</th>
                                            <th style="width: 12%;">Marks</th>
                                            <th style="width: 10%;">Eligible</th>
                                            <th style="width: 18%;">Action</th>
                                            <th style="width: 8%;">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody">
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-check-circle me-2"></i> Process Promotion
                                </button>
                                <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
    var sectionsUrl = '{{ url("admin/promotions/sections") }}';

    // Load sections when class changes
    jQuery('#fromClass').on('change', function() {
        var classId = jQuery(this).val();
        if (classId) {
            jQuery.get(sectionsUrl + '/' + classId, function(sections) {
                var options = '<option value="">All Sections</option>';
                sections.forEach(function(section) {
                    options += '<option value="' + section.id + '">' + section.name + '</option>';
                });
                jQuery('#fromSection').html(options);
            });
        } else {
            jQuery('#fromSection').html('<option value="">All Sections</option>');
        }
    });

    jQuery('#toClass').on('change', function() {
        var classId = jQuery(this).val();
        if (classId) {
            jQuery.get(sectionsUrl + '/' + classId, function(sections) {
                var options = '<option value="">Select Section</option>';
                sections.forEach(function(section) {
                    options += '<option value="' + section.id + '">' + section.name + '</option>';
                });
                jQuery('#toSection').html(options);
            });
        } else {
            jQuery('#toSection').html('<option value="">Select Section</option>');
        }
    });

    // Load students
    jQuery('#loadStudents').on('click', function() {
        var academicYearId = jQuery('#fromAcademicYear').val();
        var classId = jQuery('#fromClass').val();
        var sectionId = jQuery('#fromSection').val();

        if (!academicYearId || !classId) {
            Swal.fire('Error', 'Please select Academic Year and Class', 'error');
            return;
        }

        jQuery(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Loading...');

        jQuery.ajax({
            url: '{{ route("admin.promotions.students") }}',
            method: 'GET',
            data: {
                academic_year_id: academicYearId,
                class_id: classId,
                section_id: sectionId
            },
            success: function(response) {
                displayStudents(response);
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load students', 'error');
            },
            complete: function() {
                jQuery('#loadStudents').prop('disabled', false).html('<i class="fa fa-search me-2"></i> Load Students');
            }
        });
    });

    function displayStudents(response) {
        var students = response.students;
        var rule = response.rule;

        jQuery('#studentCount').text(students.length);

        if (students.length === 0) {
            jQuery('#studentsContainer').html('<div class="text-center py-5"><h6 class="text-muted">No students found for the selected criteria.</h6></div>');
            jQuery('#studentsTable').hide();
            jQuery('#bulkActions').hide();
            return;
        }

        // Show rule info
        if (rule) {
            var ruleHtml = '<table class="table table-sm table-borderless mb-0">';
            ruleHtml += '<tr><td>Min Attendance:</td><td><strong>' + rule.min_attendance_percentage + '%</strong></td></tr>';
            ruleHtml += '<tr><td>Min Marks:</td><td><strong>' + rule.min_marks_percentage + '%</strong></td></tr>';
            ruleHtml += '<tr><td>Consider Attendance:</td><td>' + (rule.consider_attendance ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>') + '</td></tr>';
            ruleHtml += '<tr><td>Consider Marks:</td><td>' + (rule.consider_marks ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>') + '</td></tr>';
            ruleHtml += '</table>';
            jQuery('#ruleInfo').html(ruleHtml);
            jQuery('#ruleCard').show();
        } else {
            jQuery('#ruleCard').hide();
        }

        var tbody = '';
        students.forEach(function(student, index) {
            var eligibleBadge = student.eligible
                ? '<span class="badge bg-success">Yes</span>'
                : '<span class="badge bg-danger" title="' + student.reason + '">No</span>';

            var attendanceClass = student.attendance_percentage >= (rule ? rule.min_attendance_percentage : 75) ? 'text-success' : 'text-danger';
            var marksClass = student.marks_percentage >= (rule ? rule.min_marks_percentage : 33) ? 'text-success' : 'text-danger';

            tbody += '<tr data-eligible="' + (student.eligible ? '1' : '0') + '">';
            tbody += '<td>' + (index + 1) + '</td>';
            tbody += '<td>';
            tbody += '<strong>' + student.name + '</strong><br>';
            tbody += '<small class="text-muted">' + student.admission_no + '</small>';
            tbody += '</td>';
            tbody += '<td>' + (student.roll_no || '-') + '</td>';
            tbody += '<td class="' + attendanceClass + '">' + student.attendance_percentage + '%</td>';
            tbody += '<td class="' + marksClass + '">' + student.marks_percentage + '%</td>';
            tbody += '<td>' + eligibleBadge + '</td>';
            tbody += '<td>';
            tbody += '<input type="hidden" name="students[' + index + '][id]" value="' + student.id + '">';
            tbody += '<select name="students[' + index + '][action]" class="form-select form-select-sm action-select">';
            tbody += '<option value="promote"' + (student.eligible ? ' selected' : '') + '>Promote</option>';
            tbody += '<option value="retain"' + (!student.eligible ? ' selected' : '') + '>Retain</option>';
            tbody += '<option value="alumni">Mark Alumni</option>';
            tbody += '<option value="skip">Skip</option>';
            tbody += '</select>';
            tbody += '</td>';
            tbody += '<td>';
            tbody += '<input type="text" name="students[' + index + '][remarks]" class="form-control form-control-sm" placeholder="...">';
            tbody += '</td>';
            tbody += '</tr>';
        });

        jQuery('#studentsTableBody').html(tbody);
        jQuery('#studentsContainer').hide();
        jQuery('#studentsTable').show();
        jQuery('#bulkActions').show();
    }

    // Promote all eligible
    jQuery('#promoteAll').on('click', function() {
        jQuery('#studentsTableBody tr').each(function() {
            if (jQuery(this).data('eligible') == 1) {
                jQuery(this).find('.action-select').val('promote');
            }
        });
    });

    // Retain all
    jQuery('#retainAll').on('click', function() {
        jQuery('#studentsTableBody tr').each(function() {
            jQuery(this).find('.action-select').val('retain');
        });
    });

    // Form validation
    jQuery('#promotionForm').on('submit', function(e) {
        var toAcademicYear = jQuery('#toAcademicYear').val();
        var toClass = jQuery('#toClass').val();

        if (!toAcademicYear || !toClass) {
            e.preventDefault();
            Swal.fire('Error', 'Please select "To Academic Year" and "To Class"', 'error');
            return false;
        }

        var hasStudents = jQuery('#studentsTableBody tr').length > 0;
        if (!hasStudents) {
            e.preventDefault();
            Swal.fire('Error', 'Please load students first', 'error');
            return false;
        }

        return true;
    });
});
</script>
@endpush
