<?php

namespace App\Http\Controllers\Portal\Traits;

use App\Models\Student;
use App\Models\ParentGuardian;
use Illuminate\Support\Facades\Auth;

trait PortalStudentTrait
{
    /**
     * Get the current student for portal views.
     * Works for both student users and parent users.
     */
    protected function getCurrentStudent()
    {
        $user = Auth::user();

        // First check if user is a student
        $student = Student::where('user_id', $user->id)
            ->with(['schoolClass', 'section', 'parent'])
            ->first();

        if ($student) {
            return $student;
        }

        // Check if user is a parent
        $parent = $this->getParent();

        if ($parent) {
            // Get selected child from session or use first child
            $selectedChildId = session('selected_child_id');

            if ($selectedChildId) {
                $student = Student::where('id', $selectedChildId)
                    ->where('parent_id', $parent->id)
                    ->where('status', 'active')
                    ->with(['schoolClass', 'section', 'parent'])
                    ->first();

                if ($student) {
                    return $student;
                }
            }

            // Fall back to first child
            $student = Student::where('parent_id', $parent->id)
                ->where('status', 'active')
                ->with(['schoolClass', 'section', 'parent'])
                ->first();

            if ($student) {
                session(['selected_child_id' => $student->id]);
                return $student;
            }
        }

        return null;
    }

    /**
     * Check if the current user is a parent.
     */
    protected function isParentUser(): bool
    {
        $user = Auth::user();

        // If user is directly a student, they're not a parent
        if (Student::where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check if user is a parent by user_id or email
        return $this->getParent() !== null;
    }

    /**
     * Get the parent record if user is a parent.
     * Checks by user_id first, then by email as fallback.
     */
    protected function getParent()
    {
        $user = Auth::user();

        // First try by user_id
        $parent = ParentGuardian::where('user_id', $user->id)->first();

        // If not found, try by email
        if (!$parent) {
            $parent = ParentGuardian::where('father_email', $user->email)
                ->orWhere('mother_email', $user->email)
                ->orWhere('guardian_email', $user->email)
                ->first();

            // Link the user_id if found by email
            if ($parent && !$parent->user_id) {
                $parent->update(['user_id' => $user->id]);
            }
        }

        return $parent;
    }

    /**
     * Get all children for parent user.
     */
    protected function getParentChildren()
    {
        $parent = $this->getParent();

        if (!$parent) {
            return collect();
        }

        return Student::where('parent_id', $parent->id)
            ->where('status', 'active')
            ->with(['schoolClass', 'section'])
            ->get();
    }
}
