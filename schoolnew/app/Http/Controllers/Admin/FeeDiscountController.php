<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeDiscount;
use Illuminate\Http\Request;

class FeeDiscountController extends Controller
{
	public function index(Request $request)
	{
		$query = FeeDiscount::query();

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('code', 'like', "%{$search}%");
			});
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('is_active', $request->status === 'active');
		}

		// Type filter
		if ($request->filled('type')) {
			$query->where('type', $request->type);
		}

		$discounts = $query->orderBy('name')->paginate(15);

		return view('admin.fees.discounts.index', compact('discounts'));
	}

	public function create()
	{
		return view('admin.fees.discounts.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'code' => ['required', 'string', 'max:20', 'unique:fee_discounts,code'],
			'type' => ['required', 'in:percentage,fixed'],
			'amount' => ['required', 'numeric', 'min:0'],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		// Validate percentage doesn't exceed 100
		if ($validated['type'] === 'percentage' && $validated['amount'] > 100) {
			return back()->with('error', 'Percentage discount cannot exceed 100%.')->withInput();
		}

		try {
			FeeDiscount::create([
				'name' => $validated['name'],
				'code' => strtoupper($validated['code']),
				'type' => $validated['type'],
				'amount' => $validated['amount'],
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.discounts.index')
				->with('success', 'Fee discount created successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function edit(FeeDiscount $discount)
	{
		return view('admin.fees.discounts.edit', compact('discount'));
	}

	public function update(Request $request, FeeDiscount $discount)
	{
		$validated = $request->validate([
			'name' => ['required', 'string', 'max:100'],
			'code' => ['required', 'string', 'max:20', 'unique:fee_discounts,code,' . $discount->id],
			'type' => ['required', 'in:percentage,fixed'],
			'amount' => ['required', 'numeric', 'min:0'],
			'description' => ['nullable', 'string', 'max:500'],
			'is_active' => ['nullable', 'boolean'],
		]);

		// Validate percentage doesn't exceed 100
		if ($validated['type'] === 'percentage' && $validated['amount'] > 100) {
			return back()->with('error', 'Percentage discount cannot exceed 100%.')->withInput();
		}

		try {
			$discount->update([
				'name' => $validated['name'],
				'code' => strtoupper($validated['code']),
				'type' => $validated['type'],
				'amount' => $validated['amount'],
				'description' => $validated['description'] ?? null,
				'is_active' => $request->has('is_active'),
			]);

			return redirect()->route('admin.fees.discounts.index')
				->with('success', 'Fee discount updated successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
		}
	}

	public function destroy(FeeDiscount $discount)
	{
		try {
			$discount->delete();

			return redirect()->route('admin.fees.discounts.index')
				->with('success', 'Fee discount deleted successfully.');

		} catch (\Exception $e) {
			return back()->with('error', 'An error occurred: ' . $e->getMessage());
		}
	}
}
