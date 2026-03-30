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
        $sections = CustomField::SECTIONS;
        return view('admin.custom-fields.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $needsOptions = in_array($request->input('field_type'), ['select', 'radio', 'checkbox']);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:text,textarea,number,date,select,checkbox,radio,file'],
            'applies_to' => ['required', 'in:student,teacher,all'],
            'section' => ['nullable', 'string', 'max:100'],
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
        $validated['section'] = $validated['section'] ?? 'additional_information';

        CustomField::create($validated);

        return redirect()->route('admin.custom-fields.index')
            ->with('success', 'Custom field created successfully.');
    }

    public function edit(CustomField $customField)
    {
        $sections = CustomField::SECTIONS;
        return view('admin.custom-fields.edit', compact('customField', 'sections'));
    }

    public function update(Request $request, CustomField $customField)
    {
        $needsOptions = in_array($request->input('field_type'), ['select', 'radio', 'checkbox']);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:text,textarea,number,date,select,checkbox,radio,file'],
            'applies_to' => ['required', 'in:student,teacher,all'],
            'section' => ['nullable', 'string', 'max:100'],
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
        $validated['section'] = $validated['section'] ?? 'additional_information';

        $customField->update($validated);

        return redirect()->route('admin.custom-fields.index')
            ->with('success', 'Custom field updated successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No fields selected.'], 422);
        }

        $count = CustomField::whereIn('id', $ids)->count();
        CustomField::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} custom field(s) moved to trash.",
        ]);
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
        $staffFields = $this->getDefaultStaffFields();

        $studentConfig = json_decode(Setting::get('student_form_fields', '{}'), true) ?: [];
        $teacherConfig = json_decode(Setting::get('teacher_form_fields', '{}'), true) ?: [];
        $staffConfig = json_decode(Setting::get('staff_form_fields', '{}'), true) ?: [];

        // Get custom fields for each scope
        $studentCustomFields = CustomField::withTrashed()
            ->whereIn('applies_to', ['student', 'all'])
            ->ordered()->get();
        $teacherCustomFields = CustomField::withTrashed()
            ->whereIn('applies_to', ['teacher', 'all'])
            ->ordered()->get();
        $staffCustomFields = CustomField::withTrashed()
            ->whereIn('applies_to', ['staff', 'all'])
            ->ordered()->get();

        return view('admin.custom-fields.form-settings', compact(
            'studentFields', 'teacherFields', 'staffFields',
            'studentConfig', 'teacherConfig', 'staffConfig',
            'studentCustomFields', 'teacherCustomFields', 'staffCustomFields'
        ));
    }

    public function updateFormSettings(Request $request)
    {
        $configs = [];
        foreach (['student', 'teacher', 'staff'] as $type) {
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
        Setting::set('staff_form_fields', json_encode($configs['staff']));

        // Update custom fields is_active and is_required
        $customFieldSettings = $request->input('custom_field', []);
        $allCustomFields = CustomField::withTrashed()->get();
        foreach ($allCustomFields as $cf) {
            $settings = $customFieldSettings[$cf->id] ?? [];
            $isVisible = isset($settings['visible']);
            $isRequired = isset($settings['required']) && $isVisible;

            $cf->is_active = $isVisible;
            $cf->is_required = $isRequired;
            // Restore if was trashed and now visible, or soft delete if not visible
            if ($isVisible && $cf->trashed()) {
                $cf->restore();
            }
            $cf->save();
        }

        return redirect()->route('admin.custom-fields.form-settings')
            ->with('success', 'Form field settings updated successfully.');
    }

    private function getDefaultStudentFields(): array
    {
        return [
            'first_name' => ['label' => 'First Name', 'section' => 'Student Information'],
            'gender' => ['label' => 'Gender', 'section' => 'Student Information'],
            'date_of_birth' => ['label' => 'Date of Birth', 'section' => 'Student Information'],
            'last_name' => ['label' => 'Last Name', 'section' => 'Student Information'],
            'blood_group' => ['label' => 'Blood Group', 'section' => 'Student Information'],
            'religion' => ['label' => 'Religion', 'section' => 'Student Information'],
            'nationality' => ['label' => 'Nationality', 'section' => 'Student Information'],
            'mother_tongue' => ['label' => 'Mother Tongue', 'section' => 'Student Information'],
            'class_id' => ['label' => 'Class', 'section' => 'Academic Information'],
            'section_id' => ['label' => 'Section', 'section' => 'Academic Information'],
            'admission_date' => ['label' => 'Admission Date', 'section' => 'Academic Information'],
            'roll_no' => ['label' => 'Roll No', 'section' => 'Academic Information'],
            'previous_school' => ['label' => 'Previous School', 'section' => 'Academic Information'],
            'email' => ['label' => 'Email', 'section' => 'Contact Information'],
            'phone' => ['label' => 'Phone', 'section' => 'Contact Information'],
            'current_address' => ['label' => 'Current Address', 'section' => 'Contact Information'],
            'permanent_address' => ['label' => 'Permanent Address', 'section' => 'Contact Information'],
            'father_name' => ['label' => "Father's Name", 'section' => 'Parent Information'],
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
            'first_name' => ['label' => 'First Name', 'section' => 'Basic Information'],
            'gender' => ['label' => 'Gender', 'section' => 'Basic Information'],
            'date_of_birth' => ['label' => 'Date of Birth', 'section' => 'Basic Information'],
            'last_name' => ['label' => 'Last Name', 'section' => 'Basic Information'],
            'blood_group' => ['label' => 'Blood Group', 'section' => 'Basic Information'],
            'religion' => ['label' => 'Religion', 'section' => 'Basic Information'],
            'marital_status' => ['label' => 'Marital Status', 'section' => 'Basic Information'],
            'nationality' => ['label' => 'Nationality', 'section' => 'Basic Information'],
            'email' => ['label' => 'Email', 'section' => 'Contact Information'],
            'phone' => ['label' => 'Phone', 'section' => 'Contact Information'],
            'emergency_contact' => ['label' => 'Emergency Contact', 'section' => 'Contact Information'],
            'current_address' => ['label' => 'Current Address', 'section' => 'Contact Information'],
            'permanent_address' => ['label' => 'Permanent Address', 'section' => 'Contact Information'],
            'subject_id' => ['label' => 'Subject', 'section' => 'Employment Information'],
            'designation_id' => ['label' => 'Designation', 'section' => 'Employment Information'],
            'joining_date' => ['label' => 'Joining Date', 'section' => 'Employment Information'],
            'contract_type' => ['label' => 'Contract Type', 'section' => 'Employment Information'],
            'basic_salary' => ['label' => 'Basic Salary', 'section' => 'Employment Information'],
            'aadhaar_number' => ['label' => 'Aadhaar Card Number', 'section' => 'Aadhaar & PAN Card'],
            'aadhaar_front' => ['label' => 'Aadhaar Card Front', 'section' => 'Aadhaar & PAN Card'],
            'aadhaar_back' => ['label' => 'Aadhaar Card Back', 'section' => 'Aadhaar & PAN Card'],
            'pan_number' => ['label' => 'PAN Card Number', 'section' => 'Aadhaar & PAN Card'],
            'pan_front' => ['label' => 'PAN Card Upload', 'section' => 'Aadhaar & PAN Card'],
            'qualification' => ['label' => 'Qualification', 'section' => 'Qualifications'],
            'experience' => ['label' => 'Experience', 'section' => 'Qualifications'],
            'photo' => ['label' => 'Photo Upload', 'section' => 'Sidebar'],
        ];
    }

    private function getDefaultStaffFields(): array
    {
        return [
            'first_name' => ['label' => 'First Name', 'section' => 'Basic Information'],
            'last_name' => ['label' => 'Last Name', 'section' => 'Basic Information'],
            'gender' => ['label' => 'Gender', 'section' => 'Basic Information'],
            'date_of_birth' => ['label' => 'Date of Birth', 'section' => 'Basic Information'],
            'phone' => ['label' => 'Phone', 'section' => 'Contact Information'],
            'email' => ['label' => 'Email', 'section' => 'Contact Information'],
            'current_address' => ['label' => 'Address', 'section' => 'Contact Information'],
            'emergency_contact' => ['label' => 'Emergency Contact', 'section' => 'Contact Information'],
            'designation_id' => ['label' => 'Role', 'section' => 'Job Details'],
            'joining_date' => ['label' => 'Joining Date', 'section' => 'Job Details'],
            'contract_type' => ['label' => 'Contract Type', 'section' => 'Job Details'],
            'basic_salary' => ['label' => 'Basic Salary', 'section' => 'Job Details'],
            'aadhaar_number' => ['label' => 'Aadhaar Number', 'section' => 'Documents'],
            'aadhaar_front' => ['label' => 'Aadhaar Front', 'section' => 'Documents'],
            'aadhaar_back' => ['label' => 'Aadhaar Back', 'section' => 'Documents'],
            'photo' => ['label' => 'Photo', 'section' => 'Sidebar'],
        ];
    }
}
