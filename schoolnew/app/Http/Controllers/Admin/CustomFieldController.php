<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Setting;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomField::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('applies_to')) {
            $query->where('applies_to', $request->applies_to);
        }

        if ($request->filled('field_type')) {
            $query->where('field_type', $request->field_type);
        }

        $customFields = $query->ordered()->paginate(15);
        $trashedCount = CustomField::onlyTrashed()->count();

        return view('admin.custom-fields.index', compact('customFields', 'trashedCount'));
    }

    public function create()
    {
        return view('admin.custom-fields.create');
    }

    public function store(Request $request)
    {
        $needsOptions = in_array($request->input('field_type'), ['select', 'radio', 'checkbox']);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:text,textarea,number,date,select,checkbox,radio,file'],
            'applies_to' => ['required', 'in:student,teacher,all'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($needsOptions) {
            $rules['options'] = ['required', 'array', 'min:1'];
            $rules['options.*'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        if ($needsOptions) {
            $validated['options'] = array_values(array_filter($validated['options']));
            if (empty($validated['options'])) {
                return back()->withInput()->withErrors(['options' => 'At least one option is required for this field type.']);
            }
        } else {
            $validated['options'] = null;
        }

        $validated['is_required'] = $request->boolean('is_required');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        CustomField::create($validated);

        return redirect()->route('admin.custom-fields.index')
            ->with('success', 'Custom field created successfully.');
    }

    public function edit(CustomField $customField)
    {
        return view('admin.custom-fields.edit', compact('customField'));
    }

    public function update(Request $request, CustomField $customField)
    {
        $needsOptions = in_array($request->input('field_type'), ['select', 'radio', 'checkbox']);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:text,textarea,number,date,select,checkbox,radio,file'],
            'applies_to' => ['required', 'in:student,teacher,all'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        if ($needsOptions) {
            $rules['options'] = ['required', 'array', 'min:1'];
            $rules['options.*'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        if ($needsOptions) {
            $validated['options'] = array_values(array_filter($validated['options']));
            if (empty($validated['options'])) {
                return back()->withInput()->withErrors(['options' => 'At least one option is required for this field type.']);
            }
        } else {
            $validated['options'] = null;
        }

        $validated['is_required'] = $request->boolean('is_required');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $customField->update($validated);

        return redirect()->route('admin.custom-fields.index')
            ->with('success', 'Custom field updated successfully.');
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();

        return redirect()->route('admin.custom-fields.index')
            ->with('success', 'Custom field moved to trash.');
    }

    public function trash()
    {
        $customFields = CustomField::onlyTrashed()->ordered()->paginate(15);

        return view('admin.custom-fields.trash', compact('customFields'));
    }

    public function restore($id)
    {
        $field = CustomField::onlyTrashed()->findOrFail($id);
        $field->restore();

        return redirect()->route('admin.custom-fields.trash')
            ->with('success', 'Custom field restored successfully.');
    }

    public function forceDelete($id)
    {
        $field = CustomField::onlyTrashed()->findOrFail($id);
        $field->values()->delete();
        $field->forceDelete();

        return redirect()->route('admin.custom-fields.trash')
            ->with('success', 'Custom field permanently deleted.');
    }

    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No items selected.'], 400);
        }

        CustomField::onlyTrashed()->whereIn('id', $ids)->restore();

        return response()->json(['message' => count($ids) . ' custom field(s) restored successfully.']);
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'No items selected.'], 400);
        }

        $fields = CustomField::onlyTrashed()->whereIn('id', $ids)->get();
        foreach ($fields as $field) {
            $field->values()->delete();
            $field->forceDelete();
        }

        return response()->json(['message' => count($ids) . ' custom field(s) permanently deleted.']);
    }

    public function formSettings()
    {
        $studentFields = $this->getDefaultStudentFields();
        $teacherFields = $this->getDefaultTeacherFields();

        $studentConfig = json_decode(Setting::get('student_form_fields', '{}'), true) ?: [];
        $teacherConfig = json_decode(Setting::get('teacher_form_fields', '{}'), true) ?: [];

        return view('admin.custom-fields.form-settings', compact(
            'studentFields', 'teacherFields',
            'studentConfig', 'teacherConfig'
        ));
    }

    public function updateFormSettings(Request $request)
    {
        $configs = [];
        foreach (['student', 'teacher'] as $type) {
            $config = [];
            foreach ($request->input($type, []) as $field => $settings) {
                $config[$field] = [
                    'visible' => isset($settings['visible']),
                    'required' => isset($settings['required']),
                ];
            }
            $configs[$type] = $config;
        }

        Setting::set('student_form_fields', json_encode($configs['student']));
        Setting::set('teacher_form_fields', json_encode($configs['teacher']));

        return redirect()->route('admin.custom-fields.form-settings')
            ->with('success', 'Form field settings updated successfully.');
    }

    private function getDefaultStudentFields(): array
    {
        return [
            'last_name' => ['label' => 'Last Name', 'section' => 'Student Information'],
            'blood_group' => ['label' => 'Blood Group', 'section' => 'Student Information'],
            'religion' => ['label' => 'Religion', 'section' => 'Student Information'],
            'nationality' => ['label' => 'Nationality', 'section' => 'Student Information'],
            'mother_tongue' => ['label' => 'Mother Tongue', 'section' => 'Student Information'],
            'roll_no' => ['label' => 'Roll No', 'section' => 'Academic Information'],
            'previous_school' => ['label' => 'Previous School', 'section' => 'Academic Information'],
            'email' => ['label' => 'Email', 'section' => 'Contact Information'],
            'phone' => ['label' => 'Phone', 'section' => 'Contact Information'],
            'current_address' => ['label' => 'Current Address', 'section' => 'Contact Information'],
            'permanent_address' => ['label' => 'Permanent Address', 'section' => 'Contact Information'],
            'father_phone' => ['label' => "Father's Phone", 'section' => 'Parent Information'],
            'father_email' => ['label' => "Father's Email", 'section' => 'Parent Information'],
            'father_occupation' => ['label' => "Father's Occupation", 'section' => 'Parent Information'],
            'mother_name' => ['label' => "Mother's Name", 'section' => 'Parent Information'],
            'mother_phone' => ['label' => "Mother's Phone", 'section' => 'Parent Information'],
            'mother_email' => ['label' => "Mother's Email", 'section' => 'Parent Information'],
            'mother_occupation' => ['label' => "Mother's Occupation", 'section' => 'Parent Information'],
            'aadhaar_number' => ['label' => 'Aadhaar Card Number', 'section' => 'Aadhaar Card Details'],
            'aadhaar_front' => ['label' => 'Aadhaar Card Front', 'section' => 'Aadhaar Card Details'],
            'aadhaar_back' => ['label' => 'Aadhaar Card Back', 'section' => 'Aadhaar Card Details'],
            'photo' => ['label' => 'Photo Upload', 'section' => 'Sidebar'],
        ];
    }

    private function getDefaultTeacherFields(): array
    {
        return [
            'last_name' => ['label' => 'Last Name', 'section' => 'Basic Information'],
            'blood_group' => ['label' => 'Blood Group', 'section' => 'Basic Information'],
            'religion' => ['label' => 'Religion', 'section' => 'Basic Information'],
            'marital_status' => ['label' => 'Marital Status', 'section' => 'Basic Information'],
            'nationality' => ['label' => 'Nationality', 'section' => 'Basic Information'],
            'emergency_contact' => ['label' => 'Emergency Contact', 'section' => 'Contact Information'],
            'current_address' => ['label' => 'Current Address', 'section' => 'Contact Information'],
            'permanent_address' => ['label' => 'Permanent Address', 'section' => 'Contact Information'],
            'basic_salary' => ['label' => 'Basic Salary', 'section' => 'Employment Information'],
            'aadhaar_number' => ['label' => 'Aadhaar Card Number', 'section' => 'Aadhaar & PAN Card'],
            'aadhaar_front' => ['label' => 'Aadhaar Card Front', 'section' => 'Aadhaar & PAN Card'],
            'aadhaar_back' => ['label' => 'Aadhaar Card Back', 'section' => 'Aadhaar & PAN Card'],
            'pan_number' => ['label' => 'PAN Card Number', 'section' => 'Aadhaar & PAN Card'],
            'qualification' => ['label' => 'Qualification', 'section' => 'Qualifications'],
            'experience' => ['label' => 'Experience', 'section' => 'Qualifications'],
            'photo' => ['label' => 'Photo Upload', 'section' => 'Sidebar'],
        ];
    }
}
