<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
	public function index(Request $request)
	{
		$query = Vehicle::query();

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		$vehicles = $query->orderBy('vehicle_no')->paginate(15);

		return view('admin.transport.vehicles.index', compact('vehicles'));
	}

	public function create()
	{
		return view('admin.transport.vehicles.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'vehicle_no' => ['required', 'string', 'max:50', 'unique:vehicles,vehicle_no'],
			'vehicle_model' => ['required', 'string', 'max:100'],
			'year_made' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
			'registration_no' => ['required', 'string', 'max:50'],
			'chasis_no' => ['nullable', 'string', 'max:100'],
			'max_seating_capacity' => ['required', 'integer', 'min:1'],
			'driver_name' => ['nullable', 'string', 'max:100'],
			'driver_license' => ['nullable', 'string', 'max:50'],
			'driver_contact' => ['nullable', 'string', 'max:20'],
			'status' => ['required', 'in:active,inactive,maintenance'],
			'note' => ['nullable', 'string'],
		]);

		try {
			Vehicle::create($validated);

			return redirect()->route('admin.transport.vehicles.index')->with('success', 'Vehicle added successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(Vehicle $vehicle)
	{
		return view('admin.transport.vehicles.edit', compact('vehicle'));
	}

	public function update(Request $request, Vehicle $vehicle)
	{
		$validated = $request->validate([
			'vehicle_no' => ['required', 'string', 'max:50', 'unique:vehicles,vehicle_no,' . $vehicle->id],
			'vehicle_model' => ['required', 'string', 'max:100'],
			'max_seating_capacity' => ['required', 'integer', 'min:1'],
			'driver_name' => ['nullable', 'string', 'max:100'],
			'driver_contact' => ['nullable', 'string', 'max:20'],
			'status' => ['required', 'in:active,inactive,maintenance'],
		]);

		try {
			$vehicle->update($validated);

			return redirect()->route('admin.transport.vehicles.index')->with('success', 'Vehicle updated successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(Vehicle $vehicle)
	{
		try {
			if ($vehicle->routes()->count() > 0) {
				return back()->with('error', 'Cannot delete vehicle with assigned routes.');
			}

			$vehicle->delete();

			return redirect()->route('admin.transport.vehicles.index')->with('success', 'Vehicle deleted successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
