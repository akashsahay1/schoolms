<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsSettingController extends Controller
{
    /**
     * Display SMS settings.
     */
    public function index()
    {
        $settings = SmsSetting::getInstance();
        $providers = SmsSetting::PROVIDERS;

        // Statistics
        $stats = [
            'total_sent' => SmsLog::whereIn('status', ['sent', 'delivered'])->count(),
            'total_failed' => SmsLog::failed()->count(),
            'total_pending' => SmsLog::pending()->count(),
            'today_sent' => SmsLog::whereDate('sent_at', today())->whereIn('status', ['sent', 'delivered'])->count(),
        ];

        return view('admin.settings.sms.index', compact('settings', 'providers', 'stats'));
    }

    /**
     * Update SMS settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|in:' . implode(',', array_keys(SmsSetting::PROVIDERS)),
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'sender_id' => 'nullable|string|max:50',
            'account_sid' => 'nullable|string|max:100',
            'auth_token' => 'nullable|string|max:100',
            'from_number' => 'nullable|string|max:20',
            'is_enabled' => 'boolean',
            'send_on_admission' => 'boolean',
            'send_on_fee_collection' => 'boolean',
            'send_on_attendance' => 'boolean',
            'send_on_exam_result' => 'boolean',
            'send_on_leave_approval' => 'boolean',
            'admission_template' => 'nullable|string|max:500',
            'fee_template' => 'nullable|string|max:500',
            'attendance_template' => 'nullable|string|max:500',
            'result_template' => 'nullable|string|max:500',
            'leave_template' => 'nullable|string|max:500',
        ]);

        $settings = SmsSetting::getInstance();

        // Handle empty password fields (keep existing values)
        if (empty($validated['api_key'])) {
            unset($validated['api_key']);
        }
        if (empty($validated['api_secret'])) {
            unset($validated['api_secret']);
        }
        if (empty($validated['auth_token'])) {
            unset($validated['auth_token']);
        }

        $validated['is_enabled'] = $request->has('is_enabled');
        $validated['send_on_admission'] = $request->has('send_on_admission');
        $validated['send_on_fee_collection'] = $request->has('send_on_fee_collection');
        $validated['send_on_attendance'] = $request->has('send_on_attendance');
        $validated['send_on_exam_result'] = $request->has('send_on_exam_result');
        $validated['send_on_leave_approval'] = $request->has('send_on_leave_approval');

        $settings->update($validated);

        return back()->with('success', 'SMS settings updated successfully.');
    }

    /**
     * Test SMS configuration.
     */
    public function test(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        $settings = SmsSetting::getInstance();

        if (!$settings->isConfigured()) {
            return back()->with('error', 'SMS is not properly configured. Please check your settings.');
        }

        try {
            // Log the test SMS
            SmsLog::create([
                'recipient_phone' => $request->test_phone,
                'recipient_name' => 'Test',
                'recipient_type' => 'test',
                'message_type' => 'custom',
                'message' => 'This is a test message from your School Management System.',
                'status' => 'sent', // In real implementation, this would be updated based on API response
                'sent_by' => Auth::id(),
                'sent_at' => now(),
            ]);

            return back()->with('success', 'Test SMS sent successfully. Check your phone!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test SMS: ' . $e->getMessage());
        }
    }

    /**
     * Display SMS templates.
     */
    public function templates(Request $request)
    {
        $query = SmsTemplate::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates = $query->latest()->paginate(15);
        $categories = SmsTemplate::CATEGORIES;

        return view('admin.settings.sms.templates', compact('templates', 'categories'));
    }

    /**
     * Show create template form.
     */
    public function createTemplate()
    {
        $categories = SmsTemplate::CATEGORIES;
        $variables = SmsTemplate::DEFAULT_VARIABLES;

        return view('admin.settings.sms.create-template', compact('categories', 'variables'));
    }

    /**
     * Store template.
     */
    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'nullable|in:' . implode(',', array_keys(SmsTemplate::CATEGORIES)),
            'content' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        SmsTemplate::create($validated);

        return redirect()->route('admin.settings.sms.templates')
            ->with('success', 'SMS template created successfully.');
    }

    /**
     * Show edit template form.
     */
    public function editTemplate(SmsTemplate $template)
    {
        $categories = SmsTemplate::CATEGORIES;
        $variables = SmsTemplate::DEFAULT_VARIABLES;

        return view('admin.settings.sms.edit-template', compact('template', 'categories', 'variables'));
    }

    /**
     * Update template.
     */
    public function updateTemplate(Request $request, SmsTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'nullable|in:' . implode(',', array_keys(SmsTemplate::CATEGORIES)),
            'content' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $template->update($validated);

        return redirect()->route('admin.settings.sms.templates')
            ->with('success', 'SMS template updated successfully.');
    }

    /**
     * Delete template.
     */
    public function destroyTemplate(SmsTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.settings.sms.templates')
            ->with('success', 'SMS template deleted successfully.');
    }

    /**
     * Display SMS logs.
     */
    public function logs(Request $request)
    {
        $query = SmsLog::with('sender');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('message_type')) {
            $query->where('message_type', $request->message_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('recipient_phone', 'like', '%' . $request->search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->paginate(20);
        $messageTypes = SmsLog::MESSAGE_TYPES;

        return view('admin.settings.sms.logs', compact('logs', 'messageTypes'));
    }
}
