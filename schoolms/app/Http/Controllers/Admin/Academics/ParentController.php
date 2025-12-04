<?php

namespace App\Http\Controllers\Admin\Academics;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\SmParent;
use App\SmStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class ParentController extends Controller
{
    const PARENT_ROLE_ID = 3;

    public function __construct()
    {
        $this->middleware('PM');
    }

    /**
     * Display list of all parents
     * Parents cannot be created or edited directly - they are linked to students
     */
    public function index()
    {
        try {
            $parents = SmParent::where('school_id', Auth::user()->school_id)
                ->with('parent_user')
                ->orderBy('id', 'desc')
                ->get();

            // Get children count for each parent
            foreach ($parents as $parent) {
                $parent->children_count = SmStudent::where('parent_id', $parent->id)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('active_status', 1)
                    ->count();
            }

            return view('backEnd.academics.parents.index', compact('parents'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Display the specified parent with their children
     */
    public function show($id)
    {
        try {
            $parent = SmParent::where('id', $id)
                ->where('school_id', Auth::user()->school_id)
                ->with('parent_user')
                ->firstOrFail();

            // Get parent's children
            $children = SmStudent::where('parent_id', $parent->id)
                ->where('school_id', Auth::user()->school_id)
                ->with(['class', 'section'])
                ->get();

            return view('backEnd.academics.parents.show', compact('parent', 'children'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Toggle parent account status (enable/disable login)
     */
    public function toggleStatus(Request $request)
    {
        try {
            $parent = SmParent::where('id', $request->id)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            // Update user status
            $user = User::find($parent->user_id);
            if ($user) {
                $status = $request->status == 'on' ? 1 : 0;
                $user->active_status = $status;
                $user->save();

                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed'], 500);
        }
    }

    /**
     * Search parents
     */
    public function search(Request $request)
    {
        try {
            $query = SmParent::where('school_id', Auth::user()->school_id);

            if ($request->filled('search_name')) {
                $query->where(function ($q) use ($request) {
                    $q->where('fathers_name', 'like', '%' . $request->search_name . '%')
                      ->orWhere('mothers_name', 'like', '%' . $request->search_name . '%')
                      ->orWhere('guardians_name', 'like', '%' . $request->search_name . '%');
                });
            }

            if ($request->filled('search_phone')) {
                $query->where(function ($q) use ($request) {
                    $q->where('fathers_mobile', 'like', '%' . $request->search_phone . '%')
                      ->orWhere('mothers_mobile', 'like', '%' . $request->search_phone . '%')
                      ->orWhere('guardians_mobile', 'like', '%' . $request->search_phone . '%');
                });
            }

            if ($request->filled('search_email')) {
                $query->where(function ($q) use ($request) {
                    $q->where('guardians_email', 'like', '%' . $request->search_email . '%');
                });
            }

            $parents = $query->with('parent_user')
                ->orderBy('id', 'desc')
                ->get();

            // Get children count for each parent
            foreach ($parents as $parent) {
                $parent->children_count = SmStudent::where('parent_id', $parent->id)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('active_status', 1)
                    ->count();
            }

            return view('backEnd.academics.parents.index', compact('parents'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Get students (children) of a parent - for AJAX
     */
    public function getChildren($parentId)
    {
        try {
            $children = SmStudent::where('parent_id', $parentId)
                ->where('school_id', Auth::user()->school_id)
                ->with(['class', 'section'])
                ->get();

            return response()->json($children);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed'], 500);
        }
    }
}
