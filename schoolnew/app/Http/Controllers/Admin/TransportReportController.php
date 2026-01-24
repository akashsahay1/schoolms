<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RouteAssignment;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TransportReportController extends Controller
{
    /**
     * Transport reports dashboard
     */
    public function index()
    {
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Statistics
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        $totalRoutes = TransportRoute::count();
        $activeRoutes = TransportRoute::where('is_active', true)->count();

        $totalAssignmentsQuery = RouteAssignment::query();
        if ($currentAcademicYear) {
            $totalAssignmentsQuery->where('academic_year_id', $currentAcademicYear->id);
        }
        $totalAssignments = $totalAssignmentsQuery->count();
        $activeAssignments = (clone $totalAssignmentsQuery)->where('is_active', true)->count();

        // Route-wise student count
        $routeWiseCount = TransportRoute::withCount(['assignments' => function ($query) use ($currentAcademicYear) {
            $query->where('is_active', true);
            if ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id);
            }
        }])->where('is_active', true)->orderBy('route_name')->get();

        // Vehicle capacity utilization
        $vehicleUtilization = Vehicle::with(['routes' => function ($query) {
            $query->withCount(['assignments' => function ($q) {
                $q->where('is_active', true);
            }]);
        }])->where('status', 'active')->get()->map(function ($vehicle) {
            $assignedStudents = $vehicle->routes->sum('assignments_count');
            return [
                'vehicle_no' => $vehicle->vehicle_no,
                'capacity' => $vehicle->max_seating_capacity,
                'assigned' => $assignedStudents,
                'utilization' => $vehicle->max_seating_capacity > 0
                    ? round(($assignedStudents / $vehicle->max_seating_capacity) * 100, 1)
                    : 0
            ];
        });

        // Monthly revenue estimate
        $monthlyRevenue = RouteAssignment::where('is_active', true)
            ->with('route')
            ->get()
            ->sum(function ($assignment) {
                return $assignment->route->fare_amount ?? 0;
            });

        return view('admin.transport.reports.index', compact(
            'totalVehicles',
            'activeVehicles',
            'totalRoutes',
            'activeRoutes',
            'totalAssignments',
            'activeAssignments',
            'routeWiseCount',
            'vehicleUtilization',
            'monthlyRevenue',
            'currentAcademicYear'
        ));
    }

    /**
     * Route-wise student report
     */
    public function routeWise(Request $request)
    {
        $routes = TransportRoute::with('vehicle')->where('is_active', true)->orderBy('route_name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $selectedRoute = $request->route_id;
        $selectedYear = $request->academic_year_id ?? $currentAcademicYear?->id;

        $students = collect();

        if ($selectedRoute) {
            $query = RouteAssignment::with(['student.schoolClass', 'student.section', 'route.vehicle', 'academicYear'])
                ->where('transport_route_id', $selectedRoute)
                ->where('is_active', true);

            if ($selectedYear) {
                $query->where('academic_year_id', $selectedYear);
            }

            $students = $query->orderBy('created_at')->get();
        }

        $selectedRouteData = $selectedRoute ? TransportRoute::with('vehicle')->find($selectedRoute) : null;

        return view('admin.transport.reports.route-wise', compact(
            'routes',
            'academicYears',
            'students',
            'selectedRoute',
            'selectedYear',
            'selectedRouteData',
            'currentAcademicYear'
        ));
    }

    /**
     * Class-wise transport report
     */
    public function classWise(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $selectedClass = $request->class_id;
        $selectedYear = $request->academic_year_id ?? $currentAcademicYear?->id;

        $students = collect();

        if ($selectedClass) {
            $query = RouteAssignment::with(['student.schoolClass', 'student.section', 'route.vehicle', 'academicYear'])
                ->whereHas('student', function ($q) use ($selectedClass) {
                    $q->where('class_id', $selectedClass);
                })
                ->where('is_active', true);

            if ($selectedYear) {
                $query->where('academic_year_id', $selectedYear);
            }

            $students = $query->orderBy('created_at')->get();
        }

        // Summary by class
        $classSummary = SchoolClass::where('is_active', true)
            ->withCount(['students' => function ($query) use ($selectedYear) {
                $query->whereHas('routeAssignments', function ($q) use ($selectedYear) {
                    $q->where('is_active', true);
                    if ($selectedYear) {
                        $q->where('academic_year_id', $selectedYear);
                    }
                });
            }])
            ->orderBy('order')
            ->get();

        return view('admin.transport.reports.class-wise', compact(
            'classes',
            'academicYears',
            'students',
            'selectedClass',
            'selectedYear',
            'classSummary',
            'currentAcademicYear'
        ));
    }

    /**
     * Vehicle-wise report
     */
    public function vehicleWise(Request $request)
    {
        $vehicles = Vehicle::where('status', 'active')->orderBy('vehicle_no')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $selectedVehicle = $request->vehicle_id;
        $selectedYear = $request->academic_year_id ?? $currentAcademicYear?->id;

        $students = collect();
        $vehicleRoutes = collect();

        if ($selectedVehicle) {
            $vehicleRoutes = TransportRoute::where('vehicle_id', $selectedVehicle)
                ->where('is_active', true)
                ->get();

            $routeIds = $vehicleRoutes->pluck('id');

            $query = RouteAssignment::with(['student.schoolClass', 'student.section', 'route', 'academicYear'])
                ->whereIn('transport_route_id', $routeIds)
                ->where('is_active', true);

            if ($selectedYear) {
                $query->where('academic_year_id', $selectedYear);
            }

            $students = $query->orderBy('created_at')->get();
        }

        $selectedVehicleData = $selectedVehicle ? Vehicle::find($selectedVehicle) : null;

        return view('admin.transport.reports.vehicle-wise', compact(
            'vehicles',
            'academicYears',
            'students',
            'vehicleRoutes',
            'selectedVehicle',
            'selectedYear',
            'selectedVehicleData',
            'currentAcademicYear'
        ));
    }

    /**
     * Export route-wise report to CSV
     */
    public function exportRouteWise(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:transport_routes,id',
        ]);

        $route = TransportRoute::with('vehicle')->find($request->route_id);
        $academicYearId = $request->academic_year_id;

        $query = RouteAssignment::with(['student.schoolClass', 'student.section'])
            ->where('transport_route_id', $request->route_id)
            ->where('is_active', true);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $assignments = $query->get();

        $filename = 'transport_route_' . str_replace(' ', '_', $route->route_name) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($assignments, $route) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Route: ' . $route->route_name,
                'Vehicle: ' . ($route->vehicle->vehicle_no ?? 'N/A'),
                'Fare: ' . number_format($route->fare_amount, 2)
            ]);
            fputcsv($file, []);
            fputcsv($file, ['#', 'Admission No', 'Student Name', 'Class', 'Section', 'Pickup Point', 'Drop Point']);

            $count = 1;
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $count++,
                    $assignment->student->admission_no ?? '-',
                    ($assignment->student->first_name ?? '') . ' ' . ($assignment->student->last_name ?? ''),
                    $assignment->student->schoolClass->name ?? '-',
                    $assignment->student->section->name ?? '-',
                    $assignment->pickup_point ?? '-',
                    $assignment->drop_point ?? '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export class-wise report to CSV
     */
    public function exportClassWise(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
        ]);

        $class = SchoolClass::find($request->class_id);
        $academicYearId = $request->academic_year_id;

        $query = RouteAssignment::with(['student.schoolClass', 'student.section', 'route.vehicle'])
            ->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            })
            ->where('is_active', true);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $assignments = $query->get();

        $filename = 'transport_class_' . str_replace(' ', '_', $class->name) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($assignments, $class) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Class: ' . $class->name, 'Total Students: ' . $assignments->count()]);
            fputcsv($file, []);
            fputcsv($file, ['#', 'Admission No', 'Student Name', 'Section', 'Route', 'Vehicle', 'Pickup Point', 'Monthly Fare']);

            $count = 1;
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $count++,
                    $assignment->student->admission_no ?? '-',
                    ($assignment->student->first_name ?? '') . ' ' . ($assignment->student->last_name ?? ''),
                    $assignment->student->section->name ?? '-',
                    $assignment->route->route_name ?? '-',
                    $assignment->route->vehicle->vehicle_no ?? '-',
                    $assignment->pickup_point ?? '-',
                    number_format($assignment->route->fare_amount ?? 0, 2),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export vehicle-wise report to CSV
     */
    public function exportVehicleWise(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::find($request->vehicle_id);
        $academicYearId = $request->academic_year_id;

        $routeIds = TransportRoute::where('vehicle_id', $request->vehicle_id)
            ->where('is_active', true)
            ->pluck('id');

        $query = RouteAssignment::with(['student.schoolClass', 'student.section', 'route'])
            ->whereIn('transport_route_id', $routeIds)
            ->where('is_active', true);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $assignments = $query->get();

        $filename = 'transport_vehicle_' . str_replace(' ', '_', $vehicle->vehicle_no) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($assignments, $vehicle) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Vehicle: ' . $vehicle->vehicle_no,
                'Model: ' . $vehicle->vehicle_model,
                'Capacity: ' . $vehicle->max_seating_capacity,
                'Assigned: ' . $assignments->count()
            ]);
            fputcsv($file, []);
            fputcsv($file, ['#', 'Admission No', 'Student Name', 'Class', 'Section', 'Route', 'Pickup Point']);

            $count = 1;
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $count++,
                    $assignment->student->admission_no ?? '-',
                    ($assignment->student->first_name ?? '') . ' ' . ($assignment->student->last_name ?? ''),
                    $assignment->student->schoolClass->name ?? '-',
                    $assignment->student->section->name ?? '-',
                    $assignment->route->route_name ?? '-',
                    $assignment->pickup_point ?? '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
