<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
	public function index(Request $request)
	{
		$query = TransportRoute::with('vehicle');

		if ($request->filled('vehicle')) {
			$query->where('vehicle_id', $request->vehicle);
		}

		$routes = $query->orderBy('route_name')->paginate(15);
		$vehicles = Vehicle::active()->orderBy('vehicle_no')->get();

		return view('admin.transport.routes.index', compact('routes', 'vehicles'));
	}

	public function create()
	{
		$vehicles = Vehicle::active()->orderBy('vehicle_no')->get();
		return view('admin.transport.routes.create', compact('vehicles'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'vehicle_id' => ['required', 'exists:vehicles,id'],
			'route_name' => ['required', 'string', 'max:255'],
			'start_place' => ['required', 'string', 'max:255'],
			'end_place' => ['required', 'string', 'max:255'],
			'fare_amount' => ['required', 'numeric', 'min:0'],
			'start_time' => ['nullable', 'date_format:H:i'],
			'end_time' => ['nullable', 'date_format:H:i'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			TransportRoute::create([
				'vehicle_id' => $validated['vehicle_id'],
				'route_name' => $validated['route_name'],
				'start_place' => $validated['start_place'],
				'end_place' => $validated['end_place'],
				'fare_amount' => $validated['fare_amount'],
				'start_time' => $validated['start_time'] ?? null,
				'end_time' => $validated['end_time'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.transport.routes')->with('success', 'Route added successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(TransportRoute $route)
	{
		$vehicles = Vehicle::active()->orderBy('vehicle_no')->get();
		return view('admin.transport.routes.edit', compact('route', 'vehicles'));
	}

	public function update(Request $request, TransportRoute $route)
	{
		$validated = $request->validate([
			'vehicle_id' => ['required', 'exists:vehicles,id'],
			'route_name' => ['required', 'string', 'max:255'],
			'fare_amount' => ['required', 'numeric', 'min:0'],
			'is_active' => ['nullable', 'boolean'],
		]);

		try {
			$route->update([
				'vehicle_id' => $validated['vehicle_id'],
				'route_name' => $validated['route_name'],
				'fare_amount' => $validated['fare_amount'],
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.transport.routes')->with('success', 'Route updated successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(TransportRoute $route)
	{
		try {
			$route->delete();
			return redirect()->route('admin.transport.routes')->with('success', 'Route deleted successfully.');
		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
