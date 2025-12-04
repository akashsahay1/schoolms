<?php

namespace App\Http\Controllers\Admin\Academics;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\SmStaff;
use App\SmBaseSetup;
use App\SmDesignation;
use App\SmHumanDepartment;
use App\SmStudentDocument;
use App\SmStudentTimeline;
use App\SmHrPayrollGenerate;
use App\SmLeaveRequest;
use App\Models\SmCustomField;
use App\Scopes\ActiveStatusSchoolScope;
use Modules\RolePermission\Entities\InfixRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;

class TeacherController extends Controller
{
    const TEACHER_ROLE_ID = 4;

    public function __construct()
    {
        $this->middleware('PM');
    }

    /**
     * Display list of all teachers
     */
    public function index()
    {
        try {
            $teachers = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('role_id', self::TEACHER_ROLE_ID)
                ->where('school_id', Auth::user()->school_id)
                ->with('departments', 'designations', 'roles')
                ->orderBy('id', 'desc')
                ->get();

            return view('backEnd.academics.teachers.index', compact('teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new teacher
     */
    public function create()
    {
        try {
            $max_staff_no = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('is_saas', 0)
                ->where('school_id', Auth::user()->school_id)
                ->max('staff_no');

            $departments = SmHumanDepartment::where('is_saas', 0)
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            $designations = SmDesignation::where('is_saas', 0)
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('title', 'asc')
                ->get(['id', 'title']);

            $marital_status = SmBaseSetup::where('base_group_id', '=', '4')
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('base_setup_name', 'asc')
                ->get(['id', 'base_setup_name']);

            $genders = SmBaseSetup::where('base_group_id', '=', '1')
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('base_setup_name', 'asc')
                ->get(['id', 'base_setup_name']);

            $custom_fields = SmCustomField::where('form_name', 'staff_registration')
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.academics.teachers.create', compact(
                'departments', 'designations', 'marital_status',
                'max_staff_no', 'genders', 'custom_fields'
            ));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Store a newly created teacher
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email',
            'staff_no' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:sm_human_departments,id',
            'designation_id' => 'nullable|exists:sm_designations,id',
            'gender_id' => 'nullable|exists:sm_base_setups,id',
            'date_of_birth' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'mobile' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = new User();
            $user->role_id = self::TEACHER_ROLE_ID;
            $user->username = $request->email;
            $user->email = $request->email;
            $user->full_name = $request->first_name . ' ' . ($request->last_name ?? '');
            $user->password = Hash::make($request->password ?? '123456');
            $user->school_id = Auth::user()->school_id;
            $user->active_status = 1;
            $user->save();

            // Create staff record
            $staff = new SmStaff();
            $staff->staff_no = $request->staff_no ?? (SmStaff::max('staff_no') + 1);
            $staff->role_id = self::TEACHER_ROLE_ID;
            $staff->department_id = $request->department_id;
            $staff->designation_id = $request->designation_id;
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->full_name = $request->first_name . ' ' . ($request->last_name ?? '');
            $staff->fathers_name = $request->fathers_name;
            $staff->mothers_name = $request->mothers_name;
            $staff->email = $request->email;
            $staff->school_id = Auth::user()->school_id;
            $staff->gender_id = $request->gender_id;
            $staff->marital_status = $request->marital_status;
            $staff->date_of_birth = $request->date_of_birth ? date('Y-m-d', strtotime($request->date_of_birth)) : null;
            $staff->date_of_joining = $request->date_of_joining ? date('Y-m-d', strtotime($request->date_of_joining)) : null;
            $staff->mobile = $request->mobile;
            $staff->emergency_mobile = $request->emergency_mobile;
            $staff->current_address = $request->current_address;
            $staff->permanent_address = $request->permanent_address;
            $staff->qualification = $request->qualification;
            $staff->experience = $request->experience;
            $staff->basic_salary = $request->basic_salary ?? 0;
            $staff->contract_type = $request->contract_type;
            $staff->user_id = $user->id;

            // Handle photo upload
            if ($request->hasFile('staff_photo')) {
                $file = $request->file('staff_photo');
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/staff/';
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $name);
                $staff->staff_photo = $destinationPath . $name;
            }

            $staff->save();

            DB::commit();

            // Send email notification if enabled
            try {
                if ($request->email) {
                    $compact['user_email'] = $request->email;
                    $compact['id'] = $staff->id;
                    $compact['slug'] = 'staff';
                    @send_mail($request->email, $staff->full_name, "staff_login_credentials", $compact);
                }
            } catch (\Exception $e) {
                // Email sending failed but teacher was created
            }

            Toastr::success('Teacher added successfully', 'Success');
            return redirect()->route('academic.teachers.index');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed: ' . $e->getMessage(), 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified teacher
     */
    public function show($id)
    {
        try {
            $teacher = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('id', $id)
                ->where('role_id', self::TEACHER_ROLE_ID)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            $payrollDetails = SmHrPayrollGenerate::where('staff_id', $id)
                ->where('payroll_status', '!=', 'NG')
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $leaveDetails = SmLeaveRequest::where('staff_id', $teacher->user_id)
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $documents = SmStudentDocument::where('student_staff_id', $id)
                ->where('type', 'stf')
                ->where('school_id', Auth::user()->school_id)
                ->get();

            $timelines = SmStudentTimeline::where('staff_student_id', $id)
                ->where('type', 'stf')
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.academics.teachers.show', compact(
                'teacher', 'payrollDetails', 'leaveDetails', 'documents', 'timelines'
            ));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing a teacher
     */
    public function edit($id)
    {
        try {
            $teacher = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('id', $id)
                ->where('role_id', self::TEACHER_ROLE_ID)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            $departments = SmHumanDepartment::where('is_saas', 0)
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);

            $designations = SmDesignation::where('is_saas', 0)
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('title', 'asc')
                ->get(['id', 'title']);

            $marital_status = SmBaseSetup::where('base_group_id', '=', '4')
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('base_setup_name', 'asc')
                ->get(['id', 'base_setup_name']);

            $genders = SmBaseSetup::where('base_group_id', '=', '1')
                ->where('school_id', Auth::user()->school_id)
                ->orderBy('base_setup_name', 'asc')
                ->get(['id', 'base_setup_name']);

            $custom_fields = SmCustomField::where('form_name', 'staff_registration')
                ->where('school_id', Auth::user()->school_id)
                ->get();

            return view('backEnd.academics.teachers.edit', compact(
                'teacher', 'departments', 'designations', 'marital_status',
                'genders', 'custom_fields'
            ));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Update the specified teacher
     */
    public function update(Request $request, $id)
    {
        $teacher = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
            ->where('id', $id)
            ->where('role_id', self::TEACHER_ROLE_ID)
            ->where('school_id', Auth::user()->school_id)
            ->firstOrFail();

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
        ]);

        try {
            DB::beginTransaction();

            // Update staff record
            $teacher->staff_no = $request->staff_no ?? $teacher->staff_no;
            $teacher->department_id = $request->department_id;
            $teacher->designation_id = $request->designation_id;
            $teacher->first_name = $request->first_name;
            $teacher->last_name = $request->last_name;
            $teacher->full_name = $request->first_name . ' ' . ($request->last_name ?? '');
            $teacher->fathers_name = $request->fathers_name;
            $teacher->mothers_name = $request->mothers_name;
            $teacher->email = $request->email;
            $teacher->gender_id = $request->gender_id;
            $teacher->marital_status = $request->marital_status;
            $teacher->date_of_birth = $request->date_of_birth ? date('Y-m-d', strtotime($request->date_of_birth)) : null;
            $teacher->date_of_joining = $request->date_of_joining ? date('Y-m-d', strtotime($request->date_of_joining)) : null;
            $teacher->mobile = $request->mobile;
            $teacher->emergency_mobile = $request->emergency_mobile;
            $teacher->current_address = $request->current_address;
            $teacher->permanent_address = $request->permanent_address;
            $teacher->qualification = $request->qualification;
            $teacher->experience = $request->experience;
            $teacher->basic_salary = $request->basic_salary ?? $teacher->basic_salary;
            $teacher->contract_type = $request->contract_type;

            // Handle photo upload
            if ($request->hasFile('staff_photo')) {
                // Delete old photo
                if ($teacher->staff_photo && file_exists($teacher->staff_photo)) {
                    File::delete($teacher->staff_photo);
                }

                $file = $request->file('staff_photo');
                $name = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $destinationPath = 'uploads/staff/';
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $name);
                $teacher->staff_photo = $destinationPath . $name;
            }

            $teacher->save();

            // Update user account
            $user = User::find($teacher->user_id);
            if ($user) {
                $user->username = $request->email;
                $user->email = $request->email;
                $user->full_name = $request->first_name . ' ' . ($request->last_name ?? '');

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();
            }

            DB::commit();

            Toastr::success('Teacher updated successfully', 'Success');
            return redirect()->route('academic.teachers.index');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Toggle teacher status
     */
    public function toggleStatus(Request $request)
    {
        try {
            $teacher = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('id', $request->id)
                ->where('role_id', self::TEACHER_ROLE_ID)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            $status = $request->status == 'on' ? 1 : 0;
            $teacher->active_status = $status;
            $teacher->save();

            // Update user status
            $user = User::find($teacher->user_id);
            if ($user) {
                $user->active_status = $status;
                $user->save();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed'], 500);
        }
    }

    /**
     * Delete a teacher
     */
    public function destroy($id)
    {
        try {
            $teacher = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('id', $id)
                ->where('role_id', self::TEACHER_ROLE_ID)
                ->where('school_id', Auth::user()->school_id)
                ->firstOrFail();

            // Check for dependencies
            $tables = \App\tableList::getTableList('staff_id', $id);
            if ($tables != null) {
                Toastr::error('This teacher has related data: ' . $tables, 'Failed');
                return redirect()->back();
            }

            $user_id = $teacher->user_id;
            $teacher->delete();

            // Delete user account
            User::destroy($user_id);

            Toastr::success('Teacher deleted successfully', 'Success');
            return redirect()->route('academic.teachers.index');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Search teachers
     */
    public function search(Request $request)
    {
        try {
            $query = SmStaff::withOutGlobalScope(ActiveStatusSchoolScope::class)
                ->where('role_id', self::TEACHER_ROLE_ID)
                ->where('school_id', Auth::user()->school_id);

            if ($request->filled('staff_no')) {
                $query->where('staff_no', $request->staff_no);
            }

            if ($request->filled('search_name')) {
                $query->where('full_name', 'like', '%' . $request->search_name . '%');
            }

            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }

            $teachers = $query->with('departments', 'designations', 'roles')
                ->orderBy('id', 'desc')
                ->get();

            return view('backEnd.academics.teachers.index', compact('teachers'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
