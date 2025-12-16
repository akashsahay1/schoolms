<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices.
     */
    public function index(Request $request)
    {
        $query = Notice::with('creator')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'expired') {
                $query->where('expiry_date', '<', now());
            }
        }

        $notices = $query->paginate(15);

        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new notice.
     */
    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $types = Notice::TYPES;
        $audiences = Notice::AUDIENCES;

        return view('admin.notices.create', compact('classes', 'types', 'audiences'));
    }

    /**
     * Store a newly created notice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(Notice::TYPES)),
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'target_audience' => 'required|array',
            'target_classes' => 'nullable|array',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'is_published' => 'boolean',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('notices', 'public');
        }

        $academicYear = AcademicYear::where('is_active', true)->first();

        Notice::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'publish_date' => $validated['publish_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'target_audience' => $validated['target_audience'],
            'target_classes' => $validated['target_classes'] ?? null,
            'attachment' => $attachmentPath,
            'is_published' => $request->boolean('is_published', true),
            'send_email' => $request->boolean('send_email'),
            'send_sms' => $request->boolean('send_sms'),
            'created_by' => Auth::id(),
            'academic_year_id' => $academicYear?->id,
        ]);

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice created successfully.');
    }

    /**
     * Display the specified notice.
     */
    public function show(Notice $notice)
    {
        return view('admin.notices.show', compact('notice'));
    }

    /**
     * Show the form for editing the notice.
     */
    public function edit(Notice $notice)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('order')->get();
        $types = Notice::TYPES;
        $audiences = Notice::AUDIENCES;

        return view('admin.notices.edit', compact('notice', 'classes', 'types', 'audiences'));
    }

    /**
     * Update the specified notice.
     */
    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:' . implode(',', array_keys(Notice::TYPES)),
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'target_audience' => 'required|array',
            'target_classes' => 'nullable|array',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'is_published' => 'boolean',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
        ]);

        $attachmentPath = $notice->attachment;
        if ($request->hasFile('attachment')) {
            if ($notice->attachment) {
                Storage::disk('public')->delete($notice->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('notices', 'public');
        }

        $notice->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'publish_date' => $validated['publish_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'target_audience' => $validated['target_audience'],
            'target_classes' => $validated['target_classes'] ?? null,
            'attachment' => $attachmentPath,
            'is_published' => $request->boolean('is_published', true),
            'send_email' => $request->boolean('send_email'),
            'send_sms' => $request->boolean('send_sms'),
        ]);

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice updated successfully.');
    }

    /**
     * Remove the specified notice.
     */
    public function destroy(Notice $notice)
    {
        if ($notice->attachment) {
            Storage::disk('public')->delete($notice->attachment);
        }

        $notice->delete();

        return redirect()->route('admin.notices.index')
            ->with('success', 'Notice deleted successfully.');
    }
}
