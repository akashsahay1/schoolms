<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers.
     */
    public function index(Request $request)
    {
        $query = Driver::with('vehicles');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by license status
        if ($request->filled('license_status')) {
            if ($request->license_status === 'expired') {
                $query->where('license_expiry', '<', now());
            } elseif ($request->license_status === 'expiring') {
                $query->where('license_expiry', '>=', now())
                    ->where('license_expiry', '<=', now()->addDays(30));
            } elseif ($request->license_status === 'valid') {
                $query->where('license_expiry', '>', now()->addDays(30));
            }
        }

        $drivers = $query->latest()->paginate(15);

        return view('admin.transport.drivers.index', compact('drivers'));
    }

    /**
     * Show the form for creating a new driver.
     */
    public function create()
    {
        $employeeId = Driver::generateEmployeeId();
        return view('admin.transport.drivers.create', compact('employeeId'));
    }

    /**
     * Store a newly created driver.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:drivers,employee_id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female,other',
            'license_number' => 'required|string|max:50|unique:drivers,license_number',
            'license_type' => 'nullable|string|max:50',
            'license_expiry' => 'required|date|after:today',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_document' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'id_proof_document' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('drivers/photos', 'public');
        }

        // Handle license document upload
        if ($request->hasFile('license_document')) {
            $validated['license_document'] = $request->file('license_document')->store('drivers/documents', 'public');
        }

        // Handle ID proof upload
        if ($request->hasFile('id_proof_document')) {
            $validated['id_proof_document'] = $request->file('id_proof_document')->store('drivers/documents', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Driver::create($validated);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    /**
     * Display the specified driver.
     */
    public function show(Driver $driver)
    {
        $driver->load('vehicles.routes');
        return view('admin.transport.drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified driver.
     */
    public function edit(Driver $driver)
    {
        return view('admin.transport.drivers.edit', compact('driver'));
    }

    /**
     * Update the specified driver.
     */
    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'string', Rule::unique('drivers')->ignore($driver->id)],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'required|in:male,female,other',
            'license_number' => ['required', 'string', 'max:50', Rule::unique('drivers')->ignore($driver->id)],
            'license_type' => 'nullable|string|max:50',
            'license_expiry' => 'required|date',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_document' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'id_proof_document' => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            $validated['photo'] = $request->file('photo')->store('drivers/photos', 'public');
        }

        // Handle license document upload
        if ($request->hasFile('license_document')) {
            if ($driver->license_document) {
                Storage::disk('public')->delete($driver->license_document);
            }
            $validated['license_document'] = $request->file('license_document')->store('drivers/documents', 'public');
        }

        // Handle ID proof upload
        if ($request->hasFile('id_proof_document')) {
            if ($driver->id_proof_document) {
                Storage::disk('public')->delete($driver->id_proof_document);
            }
            $validated['id_proof_document'] = $request->file('id_proof_document')->store('drivers/documents', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $driver->update($validated);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    /**
     * Remove the specified driver.
     */
    public function destroy(Driver $driver)
    {
        // Check if driver is assigned to any vehicle
        if ($driver->vehicles()->exists()) {
            return back()->with('error', 'Cannot delete driver. Driver is assigned to vehicles.');
        }

        $driver->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }

    /**
     * Bulk delete drivers.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:drivers,id',
        ]);

        $driversWithVehicles = Driver::whereIn('id', $request->ids)
            ->whereHas('vehicles')
            ->count();

        if ($driversWithVehicles > 0) {
            return back()->with('error', "Cannot delete {$driversWithVehicles} driver(s) because they are assigned to vehicles.");
        }

        Driver::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', count($request->ids) . ' driver(s) deleted successfully.');
    }

    /**
     * Display trashed drivers.
     */
    public function trash()
    {
        $drivers = Driver::onlyTrashed()->latest('deleted_at')->paginate(15);
        return view('admin.transport.drivers.trash', compact('drivers'));
    }

    /**
     * Restore a trashed driver.
     */
    public function restore($id)
    {
        $driver = Driver::onlyTrashed()->findOrFail($id);
        $driver->restore();

        return redirect()->route('admin.drivers.trash')
            ->with('success', 'Driver restored successfully.');
    }

    /**
     * Permanently delete a driver.
     */
    public function forceDelete($id)
    {
        $driver = Driver::onlyTrashed()->findOrFail($id);

        // Delete uploaded files
        if ($driver->photo) {
            Storage::disk('public')->delete($driver->photo);
        }
        if ($driver->license_document) {
            Storage::disk('public')->delete($driver->license_document);
        }
        if ($driver->id_proof_document) {
            Storage::disk('public')->delete($driver->id_proof_document);
        }

        $driver->forceDelete();

        return redirect()->route('admin.drivers.trash')
            ->with('success', 'Driver permanently deleted.');
    }

    /**
     * Empty the trash.
     */
    public function emptyTrash()
    {
        $trashedDrivers = Driver::onlyTrashed()->get();

        foreach ($trashedDrivers as $driver) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            if ($driver->license_document) {
                Storage::disk('public')->delete($driver->license_document);
            }
            if ($driver->id_proof_document) {
                Storage::disk('public')->delete($driver->id_proof_document);
            }
        }

        Driver::onlyTrashed()->forceDelete();

        return redirect()->route('admin.drivers.trash')
            ->with('success', 'Trash emptied successfully.');
    }

    /**
     * Assign driver to vehicle.
     */
    public function assignVehicle(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $vehicle->update(['driver_id' => $request->driver_id]);

        return back()->with('success', 'Driver assigned to vehicle successfully.');
    }

    /**
     * Remove driver from vehicle.
     */
    public function unassignVehicle(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $vehicle->update(['driver_id' => null]);

        return back()->with('success', 'Driver removed from vehicle successfully.');
    }

    /**
     * Export drivers to CSV.
     */
    public function export(Request $request)
    {
        $query = Driver::with('vehicles');

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $drivers = $query->get();

        $filename = 'drivers_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($drivers) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Employee ID', 'Name', 'Phone', 'Email', 'License Number',
                'License Expiry', 'License Status', 'Joining Date', 'Status', 'Assigned Vehicles'
            ]);

            foreach ($drivers as $driver) {
                fputcsv($file, [
                    $driver->employee_id,
                    $driver->full_name,
                    $driver->phone,
                    $driver->email ?? '-',
                    $driver->license_number,
                    $driver->license_expiry->format('Y-m-d'),
                    $driver->getLicenseStatusLabel(),
                    $driver->joining_date->format('Y-m-d'),
                    $driver->is_active ? 'Active' : 'Inactive',
                    $driver->vehicles->pluck('vehicle_no')->implode(', ') ?: '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
