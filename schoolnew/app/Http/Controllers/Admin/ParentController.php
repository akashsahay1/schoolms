<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use Illuminate\Http\Request;

class ParentController extends Controller
{
	public function index(Request $request)
	{
		$query = ParentGuardian::with('students');

		// Search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('father_name', 'like', "%{$search}%")
					->orWhere('mother_name', 'like', "%{$search}%")
					->orWhere('guardian_name', 'like', "%{$search}%")
					->orWhere('father_email', 'like', "%{$search}%")
					->orWhere('mother_email', 'like', "%{$search}%")
					->orWhere('father_phone', 'like', "%{$search}%")
					->orWhere('mother_phone', 'like', "%{$search}%");
			});
		}

		$parents = $query->latest()->paginate(15);

		return view('admin.parents.index', compact('parents'));
	}

	public function show(ParentGuardian $parent)
	{
		$parent->load(['students.schoolClass', 'students.section', 'user']);
		return view('admin.parents.show', compact('parent'));
	}
}
