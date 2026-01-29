<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    protected function getStaff()
    {
        return Staff::where('user_id', Auth::id())->first();
    }

    /**
     * List notices for staff.
     */
    public function index()
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        $notices = Notice::published()
            ->active()
            ->where(function ($q) {
                $q->where('audience', 'all')
                    ->orWhere('audience', 'staff')
                    ->orWhere('audience', 'teachers');
            })
            ->latest('publish_date')
            ->paginate(15);

        return view('teacher.notices.index', compact('staff', 'notices'));
    }

    /**
     * View a single notice.
     */
    public function show(Notice $notice)
    {
        $staff = $this->getStaff();
        if (!$staff) {
            return redirect()->route('teacher.dashboard')->with('error', 'Staff profile not found.');
        }

        // Check if notice is for staff
        if (!in_array($notice->audience, ['all', 'staff', 'teachers'])) {
            return redirect()->route('teacher.notices')->with('error', 'Notice not found.');
        }

        return view('teacher.notices.show', compact('staff', 'notice'));
    }
}
