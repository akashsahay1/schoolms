<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ParentController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\StaffAttendanceController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeCollectionController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\FeeGroupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/student-stats', [DashboardController::class, 'studentStats'])->name('dashboard.student-stats');

    // Profile and Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Academic Years
    Route::resource('academic-years', AcademicYearController::class)->except('show');
    Route::post('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('/students/sections/{classId}', [StudentController::class, 'getSections'])->name('students.sections');
    Route::get('students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');

    // Classes
    Route::resource('classes', ClassController::class);

    // Sections
    Route::resource('sections', SectionController::class);

    // Subjects
    Route::resource('subjects', SubjectController::class);

    // Timetable
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::get('/', [TimetableController::class, 'index'])->name('index');
        Route::get('/create', [TimetableController::class, 'create'])->name('create');
        Route::post('/', [TimetableController::class, 'store'])->name('store');
        Route::delete('/{timetable}', [TimetableController::class, 'destroy'])->name('destroy');
        Route::get('/periods', [TimetableController::class, 'periods'])->name('periods');
        Route::get('/periods/create', [TimetableController::class, 'createPeriod'])->name('periods.create');
        Route::post('/periods', [TimetableController::class, 'storePeriod'])->name('periods.store');
        Route::get('/periods/{period}/edit', [TimetableController::class, 'editPeriod'])->name('periods.edit');
        Route::put('/periods/{period}', [TimetableController::class, 'updatePeriod'])->name('periods.update');
        Route::delete('/periods/{period}', [TimetableController::class, 'destroyPeriod'])->name('periods.destroy');
        Route::get('/sections/{classId}', [TimetableController::class, 'getSections'])->name('sections');
        Route::get('/subjects/{classId}', [TimetableController::class, 'getSubjects'])->name('subjects');
    });

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Parents (view-only)
    Route::get('parents', [ParentController::class, 'index'])->name('parents.index');
    Route::get('parents/{parent}', [ParentController::class, 'show'])->name('parents.show');

    // Student Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/mark', [AttendanceController::class, 'mark'])->name('mark');
        Route::post('/mark', [AttendanceController::class, 'store'])->name('store');
        Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
        Route::get('/calendar', [AttendanceController::class, 'calendar'])->name('calendar');
        Route::get('/sections/{classId}', [AttendanceController::class, 'getSections'])->name('sections');
    });

    // Staff Attendance
    Route::prefix('staff-attendance')->name('staff-attendance.')->group(function () {
        Route::get('/mark', [StaffAttendanceController::class, 'mark'])->name('mark');
        Route::post('/mark', [StaffAttendanceController::class, 'store'])->name('store');
        Route::get('/reports', [StaffAttendanceController::class, 'reports'])->name('reports');
    });

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/create', [ExamController::class, 'create'])->name('create');
        Route::post('/', [ExamController::class, 'store'])->name('store');
        Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
        Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
        Route::get('/marks', [ExamController::class, 'marks'])->name('marks');
        Route::post('/marks', [ExamController::class, 'storeMarks'])->name('marks.store');
        Route::get('/results', [ExamController::class, 'results'])->name('results');
        Route::get('/report-cards', [ExamController::class, 'reportCards'])->name('report-cards');
        Route::get('/sections/{classId}', [ExamController::class, 'getSections'])->name('sections');
        Route::get('/students/{classId}/{sectionId}', [ExamController::class, 'getStudents'])->name('students');
    });

    // Homework
    Route::prefix('homework')->name('homework.')->group(function () {
        Route::get('/', function () { return view('admin.coming-soon'); })->name('index');
        Route::get('/create', function () { return view('admin.coming-soon'); })->name('create');
        Route::get('/submissions', function () { return view('admin.coming-soon'); })->name('submissions');
    });

    // Fees
    Route::prefix('fees')->name('fees.')->group(function () {
        // Fee Types
        Route::get('/types', [FeeTypeController::class, 'index'])->name('types.index');
        Route::get('/types/create', [FeeTypeController::class, 'create'])->name('types.create');
        Route::post('/types', [FeeTypeController::class, 'store'])->name('types.store');
        Route::get('/types/{feeType}/edit', [FeeTypeController::class, 'edit'])->name('types.edit');
        Route::put('/types/{feeType}', [FeeTypeController::class, 'update'])->name('types.update');
        Route::delete('/types/{feeType}', [FeeTypeController::class, 'destroy'])->name('types.destroy');

        // Fee Groups
        Route::get('/groups', [FeeGroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/create', [FeeGroupController::class, 'create'])->name('groups.create');
        Route::post('/groups', [FeeGroupController::class, 'store'])->name('groups.store');
        Route::get('/groups/{feeGroup}/edit', [FeeGroupController::class, 'edit'])->name('groups.edit');
        Route::put('/groups/{feeGroup}', [FeeGroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{feeGroup}', [FeeGroupController::class, 'destroy'])->name('groups.destroy');

        // Fee Structure
        Route::get('/structure', [FeeStructureController::class, 'index'])->name('structure');
        Route::get('/structure/create', [FeeStructureController::class, 'create'])->name('structure.create');
        Route::post('/structure', [FeeStructureController::class, 'store'])->name('structure.store');
        Route::get('/structure/{feeStructure}/edit', [FeeStructureController::class, 'edit'])->name('structure.edit');
        Route::put('/structure/{feeStructure}', [FeeStructureController::class, 'update'])->name('structure.update');
        Route::delete('/structure/{feeStructure}', [FeeStructureController::class, 'destroy'])->name('structure.destroy');
        Route::get('/structure/{feeStructure}/duplicate', [FeeStructureController::class, 'duplicate'])->name('structure.duplicate');
        Route::post('/structure/{feeStructure}/duplicate', [FeeStructureController::class, 'duplicate'])->name('structure.duplicate.store');

        // Fee Collection
        Route::get('/collection', [FeeCollectionController::class, 'index'])->name('collection');
        Route::get('/collection/{student}/collect', [FeeCollectionController::class, 'collectFee'])->name('collect');
        Route::post('/collection', [FeeCollectionController::class, 'store'])->name('collection.store');
        Route::get('/receipts/{feeCollection}', [FeeCollectionController::class, 'receipt'])->name('receipt');
        Route::get('/outstanding', [FeeCollectionController::class, 'outstanding'])->name('outstanding');
        Route::get('/discounts', function () {
            return view('admin.coming-soon', ['module' => 'Fee Discounts', 'description' => 'Manage fee discounts and waivers']);
        })->name('discounts');
    });

    // Staff
    Route::resource('staff', StaffController::class);
    Route::get('staff/{staff}/id-card', [StaffController::class, 'idCard'])->name('staff.id-card');
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/attendance', function () { return view('admin.coming-soon'); })->name('attendance');
        Route::get('/leaves', function () { return view('admin.coming-soon'); })->name('leaves');
    });

    // Departments & Designations
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', DesignationController::class);

    // Library
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/books', function () { return view('admin.coming-soon'); })->name('books');
        Route::get('/issue', function () { return view('admin.coming-soon'); })->name('issue');
        Route::get('/members', function () { return view('admin.coming-soon'); })->name('members');
    });

    // Transport
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::get('/vehicles', function () { return view('admin.coming-soon'); })->name('vehicles');
        Route::get('/routes', function () { return view('admin.coming-soon'); })->name('routes');
        Route::get('/drivers', function () { return view('admin.coming-soon'); })->name('drivers');
    });

    // Communication - Notices
    Route::resource('notices', App\Http\Controllers\Admin\NoticeController::class);

    // Communication - Events
    Route::resource('events', App\Http\Controllers\Admin\EventController::class);
    Route::delete('events/photos/{photo}', [App\Http\Controllers\Admin\EventController::class, 'deletePhoto'])->name('events.photos.destroy');

    // Messages
    Route::get('/messages', function () { return view('admin.coming-soon'); })->name('messages.index');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/students', function () { return view('admin.coming-soon'); })->name('students');
        Route::get('/fees', function () { return view('admin.coming-soon'); })->name('fees');
        Route::get('/attendance', function () { return view('admin.coming-soon'); })->name('attendance');
        Route::get('/exams', function () { return view('admin.coming-soon'); })->name('exams');
    });

    // Users & Roles
    Route::resource('users', UserController::class);
    Route::get('/roles', function () { return view('admin.coming-soon'); })->name('roles.index');
});

// Student/Parent Portal Routes
Route::prefix('portal')->name('portal.')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Portal\ProfileController::class, 'index'])->name('profile');

    // Attendance
    Route::get('/attendance', [App\Http\Controllers\Portal\AttendanceController::class, 'index'])->name('attendance');

    // Timetable
    Route::get('/timetable', [App\Http\Controllers\Portal\TimetableController::class, 'index'])->name('timetable');

    // Fees
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/overview', [App\Http\Controllers\Portal\FeeController::class, 'overview'])->name('overview');
        Route::get('/history', [App\Http\Controllers\Portal\FeeController::class, 'history'])->name('history');
        Route::get('/receipts/{feeCollection}', [App\Http\Controllers\Portal\FeeController::class, 'receipt'])->name('receipt');
    });

    // Notices
    Route::get('/notices', [App\Http\Controllers\Portal\NoticeController::class, 'index'])->name('notices');
    Route::get('/notices/{notice}', [App\Http\Controllers\Portal\NoticeController::class, 'show'])->name('notices.show');

    // Events
    Route::get('/events', [App\Http\Controllers\Portal\EventController::class, 'index'])->name('events');
    Route::get('/events/calendar-data', [App\Http\Controllers\Portal\EventController::class, 'calendarEvents'])->name('events.calendar');
    Route::get('/events/{event}', [App\Http\Controllers\Portal\EventController::class, 'show'])->name('events.show');

    // Leave Applications
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\LeaveController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Portal\LeaveController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Portal\LeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [App\Http\Controllers\Portal\LeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/cancel', [App\Http\Controllers\Portal\LeaveController::class, 'cancel'])->name('cancel');
    });

    // Contact School
    Route::get('/contact', [App\Http\Controllers\Portal\ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [App\Http\Controllers\Portal\ContactController::class, 'store'])->name('contact.store');
    Route::get('/contact/{message}', [App\Http\Controllers\Portal\ContactController::class, 'show'])->name('contact.show');

    // Placeholder routes (to be implemented later)
    Route::get('/homework', function () { return view('admin.coming-soon'); })->name('homework');
    Route::get('/exams', function () { return view('admin.coming-soon'); })->name('exams');
    Route::get('/results', function () { return view('admin.coming-soon'); })->name('results');
    Route::get('/report-cards', function () { return view('admin.coming-soon'); })->name('report-cards');
});
