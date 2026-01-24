<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\RouteAssignment;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\TransportRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = RouteAssignment::with(['student.schoolClass', 'student.section', 'route.vehicle', 'academicYear']);

        if ($request->filled('route')) {
            $query->where('transport_route_id', $request->route);
        }

        if ($request->filled('class')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);
        $routes = TransportRoute::active()->orderBy('route_name')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $trashedCount = RouteAssignment::onlyTrashed()->count();

        // Statistics
        $totalAssignments = RouteAssignment::count();
        $activeAssignments = RouteAssignment::where('is_active', true)->count();
        $totalStudentsWithTransport = RouteAssignment::distinct('student_id')->count('student_id');

        return view('admin.transport.assignments.index', compact(
            'assignments',
            'routes',
            'classes',
            'trashedCount',
            'totalAssignments',
            'activeAssignments',
            'totalStudentsWithTransport'
        ));
    }

    public function create()
    {
        $routes = TransportRoute::with('vehicle')->active()->orderBy('route_name')->get();
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        return view('admin.transport.assignments.create', compact('routes', 'classes', 'academicYears', 'currentAcademicYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transport_route_id' => ['required', 'exists:transport_routes,id'],
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'pickup_point' => ['nullable', 'string', 'max:255'],
            'drop_point' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check if student already has an active assignment for this academic year
        $existing = RouteAssignment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return back()->with('error', 'This student already has an active route assignment for this academic year.')->withInput();
        }

        try {
            RouteAssignment::create([
                'transport_route_id' => $validated['transport_route_id'],
                'student_id' => $validated['student_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'pickup_point' => $validated['pickup_point'] ?? null,
                'drop_point' => $validated['drop_point'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.transport.assignments.index')->with('success', 'Route assignment created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(RouteAssignment $assignment)
    {
        $assignment->load(['student.schoolClass', 'student.section', 'route.vehicle', 'academicYear']);
        $routes = TransportRoute::with('vehicle')->active()->orderBy('route_name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.transport.assignments.edit', compact('assignment', 'routes', 'academicYears'));
    }

    public function update(Request $request, RouteAssignment $assignment)
    {
        $validated = $request->validate([
            'transport_route_id' => ['required', 'exists:transport_routes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'pickup_point' => ['nullable', 'string', 'max:255'],
            'drop_point' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $assignment->update([
                'transport_route_id' => $validated['transport_route_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'pickup_point' => $validated['pickup_point'] ?? null,
                'drop_point' => $validated['drop_point'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.transport.assignments.index')->with('success', 'Route assignment updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(RouteAssignment $assignment)
    {
        try {
            $assignment->delete();
            return redirect()->route('admin.transport.assignments.index')->with('success', 'Route assignment moved to trash successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:route_assignments,id'],
        ]);

        try {
            DB::beginTransaction();
            RouteAssignment::whereIn('id', $request->ids)->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected assignments moved to trash successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function trash(Request $request)
    {
        $query = RouteAssignment::onlyTrashed()->with(['student.schoolClass', 'student.section', 'route.vehicle', 'academicYear']);

        if ($request->filled('route')) {
            $query->where('transport_route_id', $request->route);
        }

        $assignments = $query->orderBy('deleted_at', 'desc')->paginate(15);
        $routes = TransportRoute::orderBy('route_name')->get();

        return view('admin.transport.assignments.trash', compact('assignments', 'routes'));
    }

    public function restore($id)
    {
        try {
            $assignment = RouteAssignment::onlyTrashed()->findOrFail($id);
            $assignment->restore();

            return redirect()->route('admin.transport.assignments.trash')->with('success', 'Route assignment restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $assignment = RouteAssignment::onlyTrashed()->findOrFail($id);
            $assignment->forceDelete();

            return redirect()->route('admin.transport.assignments.trash')->with('success', 'Route assignment permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
        ]);

        try {
            DB::beginTransaction();
            RouteAssignment::onlyTrashed()->whereIn('id', $request->ids)->restore();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected assignments restored successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
        ]);

        try {
            DB::beginTransaction();
            RouteAssignment::onlyTrashed()->whereIn('id', $request->ids)->forceDelete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected assignments permanently deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function emptyTrash()
    {
        try {
            DB::beginTransaction();
            RouteAssignment::onlyTrashed()->forceDelete();
            DB::commit();

            return redirect()->route('admin.transport.assignments.trash')->with('success', 'Trash emptied successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Get students by class and section (for AJAX)
     */
    public function getStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $query = Student::where('class_id', $request->class_id)
            ->where('status', 'active');

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_no']);

        return response()->json($students);
    }

    /**
     * Get sections by class (for AJAX)
     */
    public function getSections(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $sections = Section::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($sections);
    }
}
