<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\StaffAttendance;
use App\Models\FeeCollection;
use App\Models\BookIssue;
use App\Models\StudentTransport;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Designation;
use App\Models\FeeType;
use App\Models\BookCategory;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomReportExport;

class CustomReportController extends Controller
{
    /**
     * Report Builder Index
     */
    public function index()
    {
        $templates = ReportTemplate::where('created_by', Auth::id())
            ->orWhere('is_public', true)
            ->orderBy('name')
            ->get();

        $dataSources = ReportTemplate::getDataSources();

        return view('admin.reports.builder.index', compact('templates', 'dataSources'));
    }

    /**
     * Create new report
     */
    public function create(Request $request)
    {
        $dataSources = ReportTemplate::getDataSources();
        $selectedSource = $request->data_source ?? 'students';
        $columns = ReportTemplate::getAvailableColumns($selectedSource);
        $filters = ReportTemplate::getAvailableFilters($selectedSource);
        $filterData = $this->getFilterData($selectedSource);

        return view('admin.reports.builder.create', compact('dataSources', 'selectedSource', 'columns', 'filters', 'filterData'));
    }

    /**
     * Generate report preview
     */
    public function preview(Request $request)
    {
        $request->validate([
            'data_source' => 'required|string',
            'columns' => 'required|array|min:1',
        ]);

        $dataSource = $request->data_source;
        $selectedColumns = $request->columns;
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? [];

        $data = $this->fetchReportData($dataSource, $selectedColumns, $filters, $sort);
        $columnLabels = ReportTemplate::getAvailableColumns($dataSource);

        return response()->json([
            'success' => true,
            'data' => $data->toArray(),
            'columns' => array_intersect_key($columnLabels, array_flip($selectedColumns)),
            'total' => $data->count(),
        ]);
    }

    /**
     * Export report to CSV
     */
    public function exportCsv(Request $request)
    {
        $request->validate([
            'data_source' => 'required|string',
            'columns' => 'required|array|min:1',
        ]);

        $dataSource = $request->data_source;
        $selectedColumns = $request->columns;
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? [];

        $data = $this->fetchReportData($dataSource, $selectedColumns, $filters, $sort);
        $columnLabels = ReportTemplate::getAvailableColumns($dataSource);

        $headings = [];
        foreach ($selectedColumns as $col) {
            $headings[] = $columnLabels[$col] ?? $col;
        }

        $rows = [];
        foreach ($data as $row) {
            $dataRow = [];
            foreach ($selectedColumns as $col) {
                $dataRow[] = $row[$col] ?? '';
            }
            $rows[] = $dataRow;
        }

        $filename = 'custom_report_' . $dataSource . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new CustomReportExport($rows, $headings), $filename);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'data_source' => 'required|string',
            'columns' => 'required|array|min:1',
        ]);

        $dataSource = $request->data_source;
        $selectedColumns = $request->columns;
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? [];

        $data = $this->fetchReportData($dataSource, $selectedColumns, $filters, $sort);
        $columnLabels = ReportTemplate::getAvailableColumns($dataSource);
        $dataSources = ReportTemplate::getDataSources();

        $pdf = Pdf::loadView('admin.reports.builder.pdf', [
            'data' => $data,
            'columns' => $selectedColumns,
            'columnLabels' => $columnLabels,
            'dataSourceName' => $dataSources[$dataSource] ?? $dataSource,
            'generatedAt' => now()->format('M d, Y H:i'),
        ]);

        $pdf->setPaper('a4', count($selectedColumns) > 6 ? 'landscape' : 'portrait');

        return $pdf->download('custom_report_' . $dataSource . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Save report template
     */
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'data_source' => 'required|string',
            'columns' => 'required|array|min:1',
        ]);

        $template = ReportTemplate::create([
            'name' => $request->name,
            'data_source' => $request->data_source,
            'columns' => $request->columns,
            'filters' => $request->filters ?? [],
            'sort' => $request->sort ?? [],
            'created_by' => Auth::id(),
            'is_public' => $request->is_public ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report template saved successfully.',
            'template' => $template,
        ]);
    }

    /**
     * Load saved template
     */
    public function loadTemplate(ReportTemplate $template)
    {
        $columns = ReportTemplate::getAvailableColumns($template->data_source);
        $filters = ReportTemplate::getAvailableFilters($template->data_source);
        $filterData = $this->getFilterData($template->data_source);

        return response()->json([
            'success' => true,
            'template' => $template,
            'availableColumns' => $columns,
            'availableFilters' => $filters,
            'filterData' => $filterData,
        ]);
    }

    /**
     * Delete template
     */
    public function deleteTemplate(ReportTemplate $template)
    {
        if ($template->created_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully.',
        ]);
    }

    /**
     * Get columns and filters for a data source (AJAX)
     */
    public function getSourceConfig(Request $request)
    {
        $dataSource = $request->data_source;
        $columns = ReportTemplate::getAvailableColumns($dataSource);
        $filters = ReportTemplate::getAvailableFilters($dataSource);
        $filterData = $this->getFilterData($dataSource);

        return response()->json([
            'success' => true,
            'columns' => $columns,
            'filters' => $filters,
            'filterData' => $filterData,
        ]);
    }

    /**
     * Fetch report data based on parameters
     */
    private function fetchReportData(string $dataSource, array $columns, array $filters, array $sort)
    {
        switch ($dataSource) {
            case 'students':
                return $this->fetchStudentData($columns, $filters, $sort);
            case 'staff':
                return $this->fetchStaffData($columns, $filters, $sort);
            case 'attendance':
                return $this->fetchAttendanceData($columns, $filters, $sort);
            case 'staff_attendance':
                return $this->fetchStaffAttendanceData($columns, $filters, $sort);
            case 'fees':
                return $this->fetchFeesData($columns, $filters, $sort);
            case 'library':
                return $this->fetchLibraryData($columns, $filters, $sort);
            case 'transport':
                return $this->fetchTransportData($columns, $filters, $sort);
            default:
                return collect();
        }
    }

    /**
     * Fetch student data
     */
    private function fetchStudentData(array $columns, array $filters, array $sort)
    {
        $query = Student::with(['schoolClass', 'section', 'academicYear', 'parent']);

        // Apply filters
        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }
        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }
        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['admission_date_from'])) {
            $query->whereDate('admission_date', '>=', $filters['admission_date_from']);
        }
        if (!empty($filters['admission_date_to'])) {
            $query->whereDate('admission_date', '<=', $filters['admission_date_to']);
        }

        // Apply sorting
        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->orderBy('first_name');
        }

        $students = $query->get();

        return $students->map(function ($student) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                $row[$col] = $this->getStudentColumnValue($student, $col);
            }
            return $row;
        });
    }

    /**
     * Get student column value
     */
    private function getStudentColumnValue($student, $column)
    {
        switch ($column) {
            case 'full_name':
                return $student->full_name;
            case 'class_name':
                return $student->schoolClass->name ?? '';
            case 'section_name':
                return $student->section->name ?? '';
            case 'academic_year':
                return $student->academicYear->name ?? '';
            case 'parent_name':
                return $student->parent ? $student->parent->father_name : '';
            case 'parent_phone':
                return $student->parent->phone ?? '';
            case 'parent_email':
                return $student->parent->email ?? '';
            case 'date_of_birth':
                return $student->date_of_birth?->format('Y-m-d');
            case 'admission_date':
                return $student->admission_date?->format('Y-m-d');
            case 'created_at':
                return $student->created_at?->format('Y-m-d');
            case 'gender':
                return ucfirst($student->gender ?? '');
            case 'status':
                return ucfirst($student->status ?? '');
            default:
                return $student->{$column} ?? '';
        }
    }

    /**
     * Fetch staff data
     */
    private function fetchStaffData(array $columns, array $filters, array $sort)
    {
        $query = Staff::with(['department', 'designation']);

        // Apply filters
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['designation_id'])) {
            $query->where('designation_id', $filters['designation_id']);
        }
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['joining_date_from'])) {
            $query->whereDate('joining_date', '>=', $filters['joining_date_from']);
        }
        if (!empty($filters['joining_date_to'])) {
            $query->whereDate('joining_date', '<=', $filters['joining_date_to']);
        }

        // Apply sorting
        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->orderBy('first_name');
        }

        $staff = $query->get();

        return $staff->map(function ($member) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                $row[$col] = $this->getStaffColumnValue($member, $col);
            }
            return $row;
        });
    }

    /**
     * Get staff column value
     */
    private function getStaffColumnValue($staff, $column)
    {
        switch ($column) {
            case 'full_name':
                return $staff->full_name;
            case 'department_name':
                return $staff->department->name ?? '';
            case 'designation_name':
                return $staff->designation->name ?? '';
            case 'date_of_birth':
                return $staff->date_of_birth?->format('Y-m-d');
            case 'joining_date':
                return $staff->joining_date?->format('Y-m-d');
            case 'created_at':
                return $staff->created_at?->format('Y-m-d');
            case 'gender':
                return ucfirst($staff->gender ?? '');
            case 'status':
                return ucfirst($staff->status ?? '');
            case 'salary':
                return number_format($staff->salary ?? 0, 2);
            default:
                return $staff->{$column} ?? '';
        }
    }

    /**
     * Fetch attendance data
     */
    private function fetchAttendanceData(array $columns, array $filters, array $sort)
    {
        $query = Attendance::with(['student.schoolClass', 'student.section']);

        // Apply filters
        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }
        if (!empty($filters['section_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('attendance_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('attendance_date', '<=', $filters['date_to']);
        }

        // Apply sorting
        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->orderBy('attendance_date', 'desc');
        }

        $attendance = $query->limit(1000)->get();

        return $attendance->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                switch ($col) {
                    case 'student_admission_no':
                        $row[$col] = $record->student->admission_no ?? '';
                        break;
                    case 'student_name':
                        $row[$col] = $record->student->full_name ?? '';
                        break;
                    case 'class_name':
                        $row[$col] = $record->student->schoolClass->name ?? '';
                        break;
                    case 'section_name':
                        $row[$col] = $record->student->section->name ?? '';
                        break;
                    case 'date':
                        $row[$col] = $record->attendance_date?->format('Y-m-d');
                        break;
                    case 'status':
                        $row[$col] = ucfirst($record->status ?? '');
                        break;
                    default:
                        $row[$col] = $record->{$col} ?? '';
                }
            }
            return $row;
        });
    }

    /**
     * Fetch staff attendance data
     */
    private function fetchStaffAttendanceData(array $columns, array $filters, array $sort)
    {
        $query = StaffAttendance::with(['staff.department']);

        // Apply filters
        if (!empty($filters['department_id'])) {
            $query->whereHas('staff', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        // Apply sorting
        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $attendance = $query->limit(1000)->get();

        return $attendance->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                switch ($col) {
                    case 'staff_employee_id':
                        $row[$col] = $record->staff->employee_id ?? '';
                        break;
                    case 'staff_name':
                        $row[$col] = $record->staff->full_name ?? '';
                        break;
                    case 'department_name':
                        $row[$col] = $record->staff->department->name ?? '';
                        break;
                    case 'date':
                        $row[$col] = $record->date?->format('Y-m-d');
                        break;
                    case 'check_in':
                        $row[$col] = $record->check_in;
                        break;
                    case 'check_out':
                        $row[$col] = $record->check_out;
                        break;
                    case 'status':
                        $row[$col] = ucfirst($record->status ?? '');
                        break;
                    default:
                        $row[$col] = $record->{$col} ?? '';
                }
            }
            return $row;
        });
    }

    /**
     * Fetch fees data
     */
    private function fetchFeesData(array $columns, array $filters, array $sort)
    {
        $query = FeeCollection::with(['student.schoolClass', 'student.section', 'feeStructure.feeType']);

        // Apply filters
        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }
        if (!empty($filters['section_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }
        if (!empty($filters['fee_type_id'])) {
            $query->whereHas('feeStructure', function ($q) use ($filters) {
                $q->where('fee_type_id', $filters['fee_type_id']);
            });
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        // Apply sorting — only use columns that exist on fee_collections table
        $sortableColumns = ['payment_date', 'paid_amount', 'amount', 'discount_amount', 'fine_amount', 'created_at'];
        if (!empty($sort['field']) && in_array($sort['field'], $sortableColumns)) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->orderBy('payment_date', 'desc');
        }

        $collections = $query->limit(1000)->get();

        return $collections->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                switch ($col) {
                    case 'student_admission_no':
                        $row[$col] = $record->student->admission_no ?? '';
                        break;
                    case 'student_name':
                        $row[$col] = $record->student->full_name ?? '';
                        break;
                    case 'class_name':
                        $row[$col] = $record->student->schoolClass->name ?? '';
                        break;
                    case 'section_name':
                        $row[$col] = $record->student->section->name ?? '';
                        break;
                    case 'fee_type':
                        $row[$col] = $record->feeStructure->feeType->name ?? '';
                        break;
                    case 'amount':
                        $row[$col] = number_format($record->amount ?? 0, 2);
                        break;
                    case 'discount':
                        $row[$col] = number_format($record->discount_amount ?? 0, 2);
                        break;
                    case 'fine':
                        $row[$col] = number_format($record->fine_amount ?? 0, 2);
                        break;
                    case 'paid_amount':
                        $row[$col] = number_format($record->paid_amount ?? 0, 2);
                        break;
                    case 'payment_date':
                        $row[$col] = $record->payment_date?->format('Y-m-d');
                        break;
                    case 'status':
                        $row[$col] = 'Paid';
                        break;
                    case 'payment_method':
                        $row[$col] = ucfirst($record->payment_method ?? '');
                        break;
                    default:
                        $row[$col] = $record->{$col} ?? '';
                }
            }
            return $row;
        });
    }

    /**
     * Fetch library data
     */
    private function fetchLibraryData(array $columns, array $filters, array $sort)
    {
        $query = BookIssue::with(['book.category', 'member']);

        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->whereHas('book', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['date_to']);
        }

        // Apply sorting
        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->orderBy('issue_date', 'desc');
        }

        $issues = $query->limit(1000)->get();

        return $issues->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                switch ($col) {
                    case 'book_title':
                        $row[$col] = $record->book->title ?? '';
                        break;
                    case 'book_isbn':
                        $row[$col] = $record->book->isbn ?? '';
                        break;
                    case 'book_category':
                        $row[$col] = $record->book->category->name ?? '';
                        break;
                    case 'member_name':
                        $row[$col] = $record->member->name ?? '';
                        break;
                    case 'member_type':
                        $row[$col] = ucfirst($record->member->member_type ?? '');
                        break;
                    case 'issue_date':
                        $row[$col] = $record->issue_date?->format('Y-m-d');
                        break;
                    case 'due_date':
                        $row[$col] = $record->due_date?->format('Y-m-d');
                        break;
                    case 'return_date':
                        $row[$col] = $record->return_date?->format('Y-m-d');
                        break;
                    case 'fine_amount':
                        $row[$col] = number_format($record->fine_amount ?? 0, 2);
                        break;
                    case 'status':
                        $row[$col] = ucfirst($record->status ?? '');
                        break;
                    default:
                        $row[$col] = $record->{$col} ?? '';
                }
            }
            return $row;
        });
    }

    /**
     * Fetch transport data
     */
    private function fetchTransportData(array $columns, array $filters, array $sort)
    {
        $query = \App\Models\RouteAssignment::with(['student.schoolClass', 'student.section', 'route.vehicle'])
            ->where('is_active', true);

        if (!empty($filters['class_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }
        if (!empty($filters['section_id'])) {
            $query->whereHas('student', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }
        if (!empty($filters['route_id'])) {
            $query->where('transport_route_id', $filters['route_id']);
        }
        if (!empty($filters['vehicle_id'])) {
            $query->whereHas('route', function ($q) use ($filters) {
                $q->where('vehicle_id', $filters['vehicle_id']);
            });
        }

        if (!empty($sort['field'])) {
            $query->orderBy($sort['field'], $sort['direction'] ?? 'asc');
        } else {
            $query->latest();
        }

        $assignments = $query->get();

        return $assignments->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $col) {
                switch ($col) {
                    case 'student_admission_no':
                        $row[$col] = $record->student->admission_no ?? '';
                        break;
                    case 'student_name':
                        $row[$col] = $record->student->full_name ?? '';
                        break;
                    case 'class_name':
                        $row[$col] = $record->student->schoolClass->name ?? '';
                        break;
                    case 'route_name':
                        $row[$col] = $record->route->route_name ?? '';
                        break;
                    case 'vehicle_number':
                        $row[$col] = $record->route->vehicle->vehicle_no ?? '';
                        break;
                    case 'driver_name':
                        $row[$col] = $record->route->vehicle->driver_name ?? '';
                        break;
                    case 'pickup_point':
                        $row[$col] = $record->pickup_point ?? '';
                        break;
                    case 'transport_fee':
                        $row[$col] = number_format($record->route->fare_amount ?? 0, 2);
                        break;
                    default:
                        $row[$col] = $record->{$col} ?? '';
                }
            }
            return $row;
        });
    }

    /**
     * Get filter data (dropdown options)
     */
    private function getFilterData(string $dataSource): array
    {
        $data = [];

        switch ($dataSource) {
            case 'students':
            case 'attendance':
            case 'fees':
            case 'transport':
                $data['classes'] = SchoolClass::where('is_active', true)->orderBy('order')->get(['id', 'name']);
                $data['sections'] = Section::where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->unique('name')
                    ->values();
                break;
            case 'staff':
            case 'staff_attendance':
                $data['departments'] = Department::where('is_active', true)->orderBy('name')->get(['id', 'name']);
                $data['designations'] = Designation::where('is_active', true)->orderBy('name')->get(['id', 'name']);
                break;
        }

        if ($dataSource === 'students') {
            $data['academic_years'] = AcademicYear::orderBy('start_date', 'desc')->get(['id', 'name']);
        }

        if ($dataSource === 'fees') {
            $data['fee_types'] = FeeType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        }

        if ($dataSource === 'library') {
            $data['categories'] = BookCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        }

        if ($dataSource === 'transport') {
            $data['routes'] = TransportRoute::where('is_active', true)->orderBy('route_name')->get(['id', 'route_name as name']);
            $data['vehicles'] = Vehicle::where('status', 'active')->orderBy('vehicle_no')->get(['id', 'vehicle_no as name']);
        }

        return $data;
    }
}
