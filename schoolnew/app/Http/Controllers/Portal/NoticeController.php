<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * Display list of notices.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        $audience = 'students';
        $classId = null;

        if (!$student) {
            // Check if parent
            $parent = \App\Models\ParentGuardian::where('user_id', $user->id)->first();
            if ($parent) {
                $audience = 'parents';
            }
        } else {
            $classId = $student->class_id;
        }

        $query = Notice::published()
            ->active()
            ->forAudience($audience);

        if ($classId) {
            $query->forClass($classId);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notices = $query->latest('publish_date')
            ->paginate(10);

        return view('portal.notices', compact('notices', 'student'));
    }

    /**
     * Display a single notice.
     */
    public function show(Notice $notice)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Verify access
        if (!$notice->is_published) {
            abort(404);
        }

        return view('portal.notice-show', compact('notice', 'student'));
    }
}
