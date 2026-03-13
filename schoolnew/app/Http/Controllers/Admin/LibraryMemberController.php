<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Staff;
use App\Models\BookIssue;
use Illuminate\Http\Request;

class LibraryMemberController extends Controller
{
    /**
     * Display a listing of library members.
     */
    public function index(Request $request)
    {
        $query = LibraryMember::with('memberable');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('member_id', 'like', "%{$search}%")
                    ->orWhereHasMorph('memberable', [Student::class], function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_no', 'like', "%{$search}%");
                    })
                    ->orWhereHasMorph('memberable', [Staff::class], function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Member type filter
        if ($request->filled('type')) {
            if ($request->type === 'student') {
                $query->where('memberable_type', Student::class);
            } elseif ($request->type === 'staff') {
                $query->where('memberable_type', Staff::class);
            }
        }

        $members = $query->latest()->paginate(15);

        // Get statistics
        $stats = [
            'total' => LibraryMember::count(),
            'active' => LibraryMember::active()->count(),
            'expired' => LibraryMember::expired()->count(),
            'suspended' => LibraryMember::suspended()->count(),
        ];

        $trashedCount = LibraryMember::onlyTrashed()->count();

        return view('admin.library.members.index', compact('members', 'stats', 'trashedCount'));
    }

    /**
     * Show the form for creating a new library member.
     */
    public function create()
    {
        // Get students without library membership
        $students = Student::whereDoesntHave('libraryMember')
            ->orderBy('first_name')
            ->get();

        // Get staff without library membership
        $staff = Staff::whereDoesntHave('libraryMember')
            ->orderBy('first_name')
            ->get();

        return view('admin.library.members.create', compact('students', 'staff'));
    }

    /**
     * Store a newly created library member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_type' => ['required', 'in:student,staff'],
            'member_id_ref' => ['required', 'integer'],
            'membership_start' => ['required', 'date'],
            'membership_end' => ['nullable', 'date', 'after:membership_start'],
            'max_books_allowed' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if member already exists
        $memberableType = $request->member_type === 'student' ? Student::class : Staff::class;
        $existing = LibraryMember::where('memberable_type', $memberableType)
            ->where('memberable_id', $request->member_id_ref)
            ->exists();

        if ($existing) {
            return back()->with('error', 'This ' . $request->member_type . ' already has a library membership.');
        }

        // Generate member ID
        $memberId = LibraryMember::generateMemberId($request->member_type);

        LibraryMember::create([
            'member_id' => $memberId,
            'memberable_type' => $memberableType,
            'memberable_id' => $request->member_id_ref,
            'membership_start' => $request->membership_start,
            'membership_end' => $request->membership_end,
            'max_books_allowed' => $request->max_books_allowed,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.library.members.index')
            ->with('success', 'Library membership created successfully. Member ID: ' . $memberId);
    }

    /**
     * Display the specified library member.
     */
    public function show(LibraryMember $member)
    {
        $member->load('memberable', 'createdBy');

        // Get book issue history for this member
        $bookIssues = collect();
        if ($member->memberable_type === Student::class) {
            $bookIssues = BookIssue::with('book')
                ->where('student_id', $member->memberable_id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        }

        return view('admin.library.members.show', compact('member', 'bookIssues'));
    }

    /**
     * Show the form for editing the specified library member.
     */
    public function edit(LibraryMember $member)
    {
        $member->load('memberable');
        return view('admin.library.members.edit', compact('member'));
    }

    /**
     * Update the specified library member.
     */
    public function update(Request $request, LibraryMember $member)
    {
        $request->validate([
            'membership_start' => ['required', 'date'],
            'membership_end' => ['nullable', 'date', 'after:membership_start'],
            'status' => ['required', 'in:active,expired,suspended'],
            'max_books_allowed' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $member->update([
            'membership_start' => $request->membership_start,
            'membership_end' => $request->membership_end,
            'status' => $request->status,
            'max_books_allowed' => $request->max_books_allowed,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.library.members.index')
            ->with('success', 'Library membership updated successfully.');
    }

    /**
     * Remove the specified library member.
     */
    public function destroy(LibraryMember $member)
    {
        // Check if member has active book issues
        if ($member->memberable_type === Student::class) {
            $activeIssues = BookIssue::where('student_id', $member->memberable_id)
                ->where('status', 'issued')
                ->count();

            if ($activeIssues > 0) {
                return redirect()->route('admin.library.members.index')
                    ->with('error', 'Cannot delete membership. Member has ' . $activeIssues . ' book(s) not returned.');
            }
        }

        $member->delete();

        return redirect()->route('admin.library.members.index')
            ->with('success', 'Library membership moved to trash.');
    }

    /**
     * Bulk delete library members.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['exists:library_members,id'],
        ]);

        try {
            $members = LibraryMember::whereIn('id', $request->member_ids)->get();
            $deletedCount = 0;
            $errors = [];

            foreach ($members as $member) {
                // Check for active book issues
                if ($member->memberable_type === Student::class) {
                    $activeIssues = BookIssue::where('student_id', $member->memberable_id)
                        ->where('status', 'issued')
                        ->count();

                    if ($activeIssues > 0) {
                        $errors[] = $member->member_id . ' has books not returned.';
                        continue;
                    }
                }

                $member->delete();
                $deletedCount++;
            }

            $message = "{$deletedCount} membership(s) moved to trash.";
            if (!empty($errors)) {
                $message .= " Skipped: " . implode(' ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show trashed members.
     */
    public function trash(Request $request)
    {
        $query = LibraryMember::onlyTrashed()->with('memberable');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('member_id', 'like', "%{$search}%");
        }

        $members = $query->latest('deleted_at')->paginate(15);
        $trashedCount = LibraryMember::onlyTrashed()->count();

        return view('admin.library.members.trash', compact('members', 'trashedCount'));
    }

    /**
     * Restore a trashed member.
     */
    public function restore($id)
    {
        try {
            $member = LibraryMember::onlyTrashed()->findOrFail($id);
            $member->restore();

            return redirect()->route('admin.library.members.trash')
                ->with('success', "Membership '{$member->member_id}' restored successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a member.
     */
    public function forceDelete($id)
    {
        try {
            $member = LibraryMember::onlyTrashed()->findOrFail($id);
            $memberId = $member->member_id;
            $member->forceDelete();

            return redirect()->route('admin.library.members.trash')
                ->with('success', "Membership '{$memberId}' permanently deleted.");

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Generate membership card.
     */
    public function card(LibraryMember $member)
    {
        $member->load('memberable');
        return view('admin.library.members.card', compact('member'));
    }

    /**
     * Renew membership.
     */
    public function renew(Request $request, LibraryMember $member)
    {
        $request->validate([
            'membership_end' => ['required', 'date', 'after:today'],
        ]);

        $member->update([
            'membership_end' => $request->membership_end,
            'status' => LibraryMember::STATUS_ACTIVE,
        ]);

        return redirect()->route('admin.library.members.show', $member)
            ->with('success', 'Membership renewed successfully until ' . $member->membership_end->format('M d, Y'));
    }
}
