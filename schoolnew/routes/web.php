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
use App\Http\Controllers\Admin\CustomFieldController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeCollectionController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\FeeGroupController;
use App\Http\Controllers\Admin\FeeDiscountController;
use App\Http\Controllers\Admin\FeeReportController;
use App\Http\Controllers\Admin\ReconciliationController;
use App\Http\Controllers\Admin\HomeworkController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookCategoryController;
use App\Http\Controllers\Admin\BookIssueController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\TransportRouteController;
use App\Http\Controllers\Admin\RouteAssignmentController;
use App\Http\Controllers\Admin\TransportReportController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\TransportFeeController;
use App\Http\Controllers\Admin\SmsSettingController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LeaveApplicationController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\StaffLeaveController;
use App\Http\Controllers\Admin\BulkMessagingController;
use App\Http\Controllers\Admin\MessagingController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\LibraryMemberController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\WebsiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Website Routes
Route::name('website.')->group(function () {
    Route::get('/', [WebsiteController::class, 'index'])->name('home');
    Route::get('/about', [WebsiteController::class, 'about'])->name('about');
    Route::get('/academics', [WebsiteController::class, 'academics'])->name('academics');
    Route::get('/facilities', [WebsiteController::class, 'facilities'])->name('facilities');
    Route::get('/gallery', [WebsiteController::class, 'gallery'])->name('gallery');
    Route::get('/news', [WebsiteController::class, 'news'])->name('news');
    Route::get('/news/{notice}', [WebsiteController::class, 'newsShow'])->name('news.show');
    Route::get('/events', [WebsiteController::class, 'events'])->name('events');
    Route::get('/events/{event}', [WebsiteController::class, 'eventShow'])->name('events.show');
    Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
    Route::post('/contact', [WebsiteController::class, 'contactStore'])->name('contact.store');
    Route::get('/page/{slug}', [WebsiteController::class, 'page'])->name('page');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Notification Routes (shared by all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Super Admin,Admin,Accountant,Librarian,Receptionist'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/student-stats', [DashboardController::class, 'studentStats'])->name('dashboard.student-stats');

    // Profile and Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete-avatar');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // School Settings
    Route::get('/settings/school', [App\Http\Controllers\Admin\SettingController::class, 'school'])->name('settings.school');
    Route::post('/settings/school', [App\Http\Controllers\Admin\SettingController::class, 'updateSchool'])->name('settings.school.update');

    // Library Settings
    Route::get('/settings/library', [App\Http\Controllers\Admin\SettingController::class, 'library'])->name('settings.library');
    Route::post('/settings/library', [App\Http\Controllers\Admin\SettingController::class, 'updateLibrary'])->name('settings.library.update');

    // SMS Settings
    Route::prefix('settings/sms')->name('settings.sms.')->group(function () {
        Route::get('/', [SmsSettingController::class, 'index'])->name('index');
        Route::put('/', [SmsSettingController::class, 'update'])->name('update');
        Route::post('/test', [SmsSettingController::class, 'test'])->name('test');
        Route::get('/templates', [SmsSettingController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [SmsSettingController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [SmsSettingController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{template}/edit', [SmsSettingController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [SmsSettingController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [SmsSettingController::class, 'destroyTemplate'])->name('templates.destroy');
        Route::get('/logs', [SmsSettingController::class, 'logs'])->name('logs');
    });

    // Custom Fields
    Route::get('custom-fields/form-settings', [CustomFieldController::class, 'formSettings'])->name('custom-fields.form-settings');
    Route::put('custom-fields/form-settings', [CustomFieldController::class, 'updateFormSettings'])->name('custom-fields.update-form-settings');
    Route::post('custom-fields/bulk-restore', [CustomFieldController::class, 'bulkRestore'])->name('custom-fields.bulk-restore');
    Route::post('custom-fields/bulk-force-delete', [CustomFieldController::class, 'bulkForceDelete'])->name('custom-fields.bulk-force-delete');
    Route::resource('custom-fields', CustomFieldController::class)->except('show');
    Route::get('custom-fields-trash', [CustomFieldController::class, 'trash'])->name('custom-fields.trash');
    Route::post('custom-fields/{id}/restore', [CustomFieldController::class, 'restore'])->name('custom-fields.restore');
    Route::delete('custom-fields/{id}/force-delete', [CustomFieldController::class, 'forceDelete'])->name('custom-fields.force-delete');

    // Academic Years
    Route::resource('academic-years', AcademicYearController::class)->except('show');
    Route::post('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('/students/sections/{classId}', [StudentController::class, 'getSections'])->name('students.sections');
    Route::get('students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');
    Route::post('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
    Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');
    Route::post('students/{student}/update-email', [StudentController::class, 'updateEmail'])->name('students.update-email');
    // Student Trash
    Route::get('students-trash', [StudentController::class, 'trash'])->name('students.trash');
    Route::post('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
    Route::post('students/bulk-restore', [StudentController::class, 'bulkRestore'])->name('students.bulk-restore');
    Route::post('students/bulk-force-delete', [StudentController::class, 'bulkForceDelete'])->name('students.bulk-force-delete');
    Route::delete('students-trash/empty', [StudentController::class, 'emptyTrash'])->name('students.empty-trash');

    // Classes
    Route::resource('classes', ClassController::class);
    Route::post('classes/{class}/add-subject', [ClassController::class, 'addSubject'])->name('classes.add-subject');
    Route::delete('classes/{class}/remove-subject/{subject}', [ClassController::class, 'removeSubject'])->name('classes.remove-subject');
    Route::post('classes/{class}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('classes.assign-teacher');

    // Sections
    Route::resource('sections', SectionController::class);

    // Subjects
    Route::resource('subjects', SubjectController::class);

    // Timetable
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::get('/', [TimetableController::class, 'index'])->name('index');
        Route::get('/create', [TimetableController::class, 'create'])->name('create');
        Route::post('/', [TimetableController::class, 'store'])->name('store');
        Route::put('/{timetable}', [TimetableController::class, 'update'])->name('update');
        Route::delete('/{timetable}', [TimetableController::class, 'destroy'])->name('destroy');
        Route::get('/print', [TimetableController::class, 'print'])->name('print');
        Route::get('/teacher', [TimetableController::class, 'teacherTimetable'])->name('teacher');
        Route::get('/teacher/print', [TimetableController::class, 'printTeacherTimetable'])->name('teacher.print');
        Route::get('/conflicts', [TimetableController::class, 'conflicts'])->name('conflicts');
        Route::get('/room-availability', [TimetableController::class, 'getRoomAvailability'])->name('room-availability');
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
    Route::post('teachers/bulk-delete', [TeacherController::class, 'bulkDelete'])->name('teachers.bulk-delete');
    Route::post('teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');
    // Teacher Trash
    Route::get('teachers-trash', [TeacherController::class, 'trash'])->name('teachers.trash');
    Route::post('teachers/{id}/restore', [TeacherController::class, 'restore'])->name('teachers.restore');
    Route::delete('teachers/{id}/force-delete', [TeacherController::class, 'forceDelete'])->name('teachers.force-delete');
    Route::post('teachers/bulk-restore', [TeacherController::class, 'bulkRestore'])->name('teachers.bulk-restore');
    Route::post('teachers/bulk-force-delete', [TeacherController::class, 'bulkForceDelete'])->name('teachers.bulk-force-delete');
    Route::delete('teachers-trash/empty', [TeacherController::class, 'emptyTrash'])->name('teachers.empty-trash');

    // Parents
    Route::get('parents', [ParentController::class, 'index'])->name('parents.index');
    Route::get('parents/{parent}', [ParentController::class, 'show'])->name('parents.show');
    Route::delete('parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy');
    Route::post('parents/bulk-delete', [ParentController::class, 'bulkDelete'])->name('parents.bulk-delete');
    Route::post('parents/{parent}/reset-password', [ParentController::class, 'resetPassword'])->name('parents.reset-password');
    Route::get('parents-trash', [ParentController::class, 'trash'])->name('parents.trash');
    Route::post('parents/{id}/restore', [ParentController::class, 'restore'])->name('parents.restore');
    Route::delete('parents/{id}/force-delete', [ParentController::class, 'forceDelete'])->name('parents.force-delete');
    Route::post('parents/bulk-restore', [ParentController::class, 'bulkRestore'])->name('parents.bulk-restore');
    Route::post('parents/bulk-force-delete', [ParentController::class, 'bulkForceDelete'])->name('parents.bulk-force-delete');
    Route::delete('parents-trash/empty', [ParentController::class, 'emptyTrash'])->name('parents.empty-trash');

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
        Route::get('/', [HomeworkController::class, 'index'])->name('index');
        Route::get('/create', [HomeworkController::class, 'create'])->name('create');
        Route::post('/', [HomeworkController::class, 'store'])->name('store');
        Route::get('/{homework}/edit', [HomeworkController::class, 'edit'])->name('edit');
        Route::put('/{homework}', [HomeworkController::class, 'update'])->name('update');
        Route::delete('/{homework}', [HomeworkController::class, 'destroy'])->name('destroy');
        Route::get('/{homework}/submissions', [HomeworkController::class, 'submissions'])->name('submissions');
        Route::post('/submissions/{submission}/evaluate', [HomeworkController::class, 'evaluateSubmission'])->name('evaluate-submission');
        Route::get('/sections/{classId}', [HomeworkController::class, 'getSections'])->name('sections');
        Route::post('/bulk-delete', [HomeworkController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('-trash', [HomeworkController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [HomeworkController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [HomeworkController::class, 'forceDelete'])->name('force-delete');
        Route::post('/bulk-restore', [HomeworkController::class, 'bulkRestore'])->name('bulk-restore');
        Route::post('/bulk-force-delete', [HomeworkController::class, 'bulkForceDelete'])->name('bulk-force-delete');
        Route::delete('-trash/empty', [HomeworkController::class, 'emptyTrash'])->name('empty-trash');
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
        Route::post('/types/bulk-delete', [FeeTypeController::class, 'bulkDelete'])->name('types.bulk-delete');
        Route::get('/types-trash', [FeeTypeController::class, 'trash'])->name('types.trash');
        Route::post('/types/{id}/restore', [FeeTypeController::class, 'restore'])->name('types.restore');
        Route::delete('/types/{id}/force-delete', [FeeTypeController::class, 'forceDelete'])->name('types.force-delete');
        Route::post('/types/bulk-restore', [FeeTypeController::class, 'bulkRestore'])->name('types.bulk-restore');
        Route::post('/types/bulk-force-delete', [FeeTypeController::class, 'bulkForceDelete'])->name('types.bulk-force-delete');
        Route::delete('/types-trash/empty', [FeeTypeController::class, 'emptyTrash'])->name('types.empty-trash');

        // Fee Groups
        Route::get('/groups', [FeeGroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/create', [FeeGroupController::class, 'create'])->name('groups.create');
        Route::post('/groups', [FeeGroupController::class, 'store'])->name('groups.store');
        Route::get('/groups/{feeGroup}/edit', [FeeGroupController::class, 'edit'])->name('groups.edit');
        Route::put('/groups/{feeGroup}', [FeeGroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{feeGroup}', [FeeGroupController::class, 'destroy'])->name('groups.destroy');
        Route::post('/groups/bulk-delete', [FeeGroupController::class, 'bulkDelete'])->name('groups.bulk-delete');
        Route::get('/groups-trash', [FeeGroupController::class, 'trash'])->name('groups.trash');
        Route::post('/groups/{id}/restore', [FeeGroupController::class, 'restore'])->name('groups.restore');
        Route::delete('/groups/{id}/force-delete', [FeeGroupController::class, 'forceDelete'])->name('groups.force-delete');
        Route::post('/groups/bulk-restore', [FeeGroupController::class, 'bulkRestore'])->name('groups.bulk-restore');
        Route::post('/groups/bulk-force-delete', [FeeGroupController::class, 'bulkForceDelete'])->name('groups.bulk-force-delete');
        Route::delete('/groups-trash/empty', [FeeGroupController::class, 'emptyTrash'])->name('groups.empty-trash');

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
        Route::delete('/collection/{feeCollection}', [FeeCollectionController::class, 'destroy'])->name('collection.destroy');
        Route::post('/collection/{feeCollection}/refund', [FeeCollectionController::class, 'refund'])->name('collection.refund');
        Route::get('/outstanding', [FeeCollectionController::class, 'outstanding'])->name('outstanding');

        // Fee Discounts
        Route::get('/discounts', [FeeDiscountController::class, 'index'])->name('discounts.index');
        Route::get('/discounts/create', [FeeDiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discounts', [FeeDiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{discount}/edit', [FeeDiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discounts/{discount}', [FeeDiscountController::class, 'update'])->name('discounts.update');
        Route::delete('/discounts/{discount}', [FeeDiscountController::class, 'destroy'])->name('discounts.destroy');
        Route::post('/discounts/bulk-delete', [FeeDiscountController::class, 'bulkDelete'])->name('discounts.bulk-delete');

        // Fee Reports & Analytics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [FeeReportController::class, 'index'])->name('index');
            Route::get('/collection', [FeeReportController::class, 'collection'])->name('collection');
            Route::get('/outstanding', [FeeReportController::class, 'outstanding'])->name('outstanding');
            Route::get('/fee-type-wise', [FeeReportController::class, 'feeTypeWise'])->name('fee-type-wise');
            Route::get('/class-wise', [FeeReportController::class, 'classWise'])->name('class-wise');
            Route::get('/daily', [FeeReportController::class, 'daily'])->name('daily');
            Route::get('/export-excel', [FeeReportController::class, 'exportExcel'])->name('export-excel');
            Route::get('/export-pdf', [FeeReportController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/chart-data', [FeeReportController::class, 'chartData'])->name('chart-data');
        });

        // Transaction Reconciliation
        Route::prefix('reconciliation')->name('reconciliation.')->group(function () {
            Route::get('/', [ReconciliationController::class, 'index'])->name('index');
            Route::get('/import', [ReconciliationController::class, 'import'])->name('import');
            Route::post('/import', [ReconciliationController::class, 'processImport'])->name('process-import');
            Route::get('/match', [ReconciliationController::class, 'match'])->name('match');
            Route::post('/auto-match', [ReconciliationController::class, 'autoMatch'])->name('auto-match');
            Route::post('/manual-match', [ReconciliationController::class, 'manualMatch'])->name('manual-match');
            Route::post('/unmatch', [ReconciliationController::class, 'unmatch'])->name('unmatch');
            Route::post('/mark-unmatched', [ReconciliationController::class, 'markUnmatched'])->name('mark-unmatched');
            Route::post('/ignore', [ReconciliationController::class, 'ignore'])->name('ignore');
            Route::post('/dispute', [ReconciliationController::class, 'dispute'])->name('dispute');
            Route::get('/report', [ReconciliationController::class, 'report'])->name('report');
            Route::get('/search-collections', [ReconciliationController::class, 'searchCollections'])->name('search-collections');
        });
    });

    // Staff
    Route::resource('staff', StaffController::class);
    Route::get('staff/{staff}/id-card', [StaffController::class, 'idCard'])->name('staff.id-card');
    Route::post('staff/bulk-delete', [StaffController::class, 'bulkDelete'])->name('staff.bulk-delete');
    Route::post('staff/{staff}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.reset-password');
    // Staff Trash
    Route::get('staff-trash', [StaffController::class, 'trash'])->name('staff.trash');
    Route::post('staff/{id}/restore', [StaffController::class, 'restore'])->name('staff.restore');
    Route::delete('staff/{id}/force-delete', [StaffController::class, 'forceDelete'])->name('staff.force-delete');
    Route::post('staff/bulk-restore', [StaffController::class, 'bulkRestore'])->name('staff.bulk-restore');
    Route::post('staff/bulk-force-delete', [StaffController::class, 'bulkForceDelete'])->name('staff.bulk-force-delete');
    Route::delete('staff-trash/empty', [StaffController::class, 'emptyTrash'])->name('staff.empty-trash');

    // Leave Applications (Student)
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [LeaveApplicationController::class, 'index'])->name('index');
        Route::get('/{leave}', [LeaveApplicationController::class, 'show'])->name('show');
        Route::post('/{leave}/approve', [LeaveApplicationController::class, 'approve'])->name('approve');
        Route::post('/{leave}/reject', [LeaveApplicationController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [LeaveApplicationController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [LeaveApplicationController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Staff Leave Management
    Route::prefix('staff-leaves')->name('staff-leaves.')->group(function () {
        // Leave Types
        Route::get('/types', [LeaveTypeController::class, 'index'])->name('types.index');
        Route::get('/types/create', [LeaveTypeController::class, 'create'])->name('types.create');
        Route::post('/types', [LeaveTypeController::class, 'store'])->name('types.store');
        Route::get('/types/{type}/edit', [LeaveTypeController::class, 'edit'])->name('types.edit');
        Route::put('/types/{type}', [LeaveTypeController::class, 'update'])->name('types.update');
        Route::delete('/types/{type}', [LeaveTypeController::class, 'destroy'])->name('types.destroy');
        Route::post('/types/bulk-delete', [LeaveTypeController::class, 'bulkDelete'])->name('types.bulk-delete');
        Route::get('/types-trash', [LeaveTypeController::class, 'trash'])->name('types.trash');
        Route::post('/types/{id}/restore', [LeaveTypeController::class, 'restore'])->name('types.restore');
        Route::delete('/types/{id}/force-delete', [LeaveTypeController::class, 'forceDelete'])->name('types.force-delete');
        Route::post('/types/bulk-restore', [LeaveTypeController::class, 'bulkRestore'])->name('types.bulk-restore');
        Route::post('/types/bulk-force-delete', [LeaveTypeController::class, 'bulkForceDelete'])->name('types.bulk-force-delete');
        Route::delete('/types-trash/empty', [LeaveTypeController::class, 'emptyTrash'])->name('types.empty-trash');

        // Staff Leave Applications
        Route::get('/', [StaffLeaveController::class, 'index'])->name('index');
        Route::get('/create', [StaffLeaveController::class, 'create'])->name('create');
        Route::post('/', [StaffLeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [StaffLeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/approve', [StaffLeaveController::class, 'approve'])->name('approve');
        Route::post('/{leave}/reject', [StaffLeaveController::class, 'reject'])->name('reject');
        Route::post('/{leave}/cancel', [StaffLeaveController::class, 'cancel'])->name('cancel');
        Route::post('/bulk-approve', [StaffLeaveController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [StaffLeaveController::class, 'bulkReject'])->name('bulk-reject');

        // Leave Balances
        Route::get('/balances', [StaffLeaveController::class, 'balances'])->name('balances');
        Route::get('/balances/allocate', [StaffLeaveController::class, 'allocate'])->name('balances.allocate');
        Route::post('/balances/allocate', [StaffLeaveController::class, 'storeAllocation'])->name('balances.store');
        Route::get('/reports', [StaffLeaveController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [StaffLeaveController::class, 'exportReport'])->name('reports.export');
    });

    // Departments & Designations
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::post('departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('departments.bulk-delete');
    Route::get('departments-trash', [DepartmentController::class, 'trash'])->name('departments.trash');
    Route::post('departments/{id}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
    Route::delete('departments/{id}/force-delete', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
    Route::post('departments/bulk-restore', [DepartmentController::class, 'bulkRestore'])->name('departments.bulk-restore');
    Route::post('departments/bulk-force-delete', [DepartmentController::class, 'bulkForceDelete'])->name('departments.bulk-force-delete');
    Route::delete('departments-trash/empty', [DepartmentController::class, 'emptyTrash'])->name('departments.empty-trash');

    Route::resource('designations', DesignationController::class)->except(['show']);
    Route::post('designations/bulk-delete', [DesignationController::class, 'bulkDelete'])->name('designations.bulk-delete');
    Route::get('designations-trash', [DesignationController::class, 'trash'])->name('designations.trash');
    Route::post('designations/{id}/restore', [DesignationController::class, 'restore'])->name('designations.restore');
    Route::delete('designations/{id}/force-delete', [DesignationController::class, 'forceDelete'])->name('designations.force-delete');
    Route::post('designations/bulk-restore', [DesignationController::class, 'bulkRestore'])->name('designations.bulk-restore');
    Route::post('designations/bulk-force-delete', [DesignationController::class, 'bulkForceDelete'])->name('designations.bulk-force-delete');
    Route::delete('designations-trash/empty', [DesignationController::class, 'emptyTrash'])->name('designations.empty-trash');

    // Library
    Route::prefix('library')->name('library.')->group(function () {
        // Books
        Route::get('/books', [BookController::class, 'index'])->name('books.index');
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
        Route::post('/books/bulk-delete', [BookController::class, 'bulkDelete'])->name('books.bulk-delete');
        Route::get('/books-trash', [BookController::class, 'trash'])->name('books.trash');
        Route::post('/books/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
        Route::delete('/books/{id}/force-delete', [BookController::class, 'forceDelete'])->name('books.force-delete');
        Route::post('/books/bulk-restore', [BookController::class, 'bulkRestore'])->name('books.bulk-restore');
        Route::post('/books/bulk-force-delete', [BookController::class, 'bulkForceDelete'])->name('books.bulk-force-delete');
        Route::delete('/books-trash/empty', [BookController::class, 'emptyTrash'])->name('books.empty-trash');

        // Book Categories
        Route::get('/categories', [BookCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [BookCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [BookCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [BookCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [BookCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [BookCategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/bulk-delete', [BookCategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
        Route::get('/categories-trash', [BookCategoryController::class, 'trash'])->name('categories.trash');
        Route::post('/categories/{id}/restore', [BookCategoryController::class, 'restore'])->name('categories.restore');
        Route::delete('/categories/{id}/force-delete', [BookCategoryController::class, 'forceDelete'])->name('categories.force-delete');
        Route::post('/categories/bulk-restore', [BookCategoryController::class, 'bulkRestore'])->name('categories.bulk-restore');
        Route::post('/categories/bulk-force-delete', [BookCategoryController::class, 'bulkForceDelete'])->name('categories.bulk-force-delete');
        Route::delete('/categories-trash/empty', [BookCategoryController::class, 'emptyTrash'])->name('categories.empty-trash');

        // Book Issue
        Route::get('/issue', [BookIssueController::class, 'index'])->name('issue.index');
        Route::get('/issue/create', [BookIssueController::class, 'create'])->name('issue.create');
        Route::post('/issue', [BookIssueController::class, 'store'])->name('issue.store');
        Route::post('/issue/{issue}/return', [BookIssueController::class, 'returnBook'])->name('issue.return');
        Route::get('/issue/{issue}/calculate-fine', [BookIssueController::class, 'calculateFine'])->name('issue.calculate-fine');

        // Library Reports
        Route::get('/reports', [App\Http\Controllers\Admin\LibraryReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/issues', [App\Http\Controllers\Admin\LibraryReportController::class, 'issues'])->name('reports.issues');
        Route::get('/reports/overdue', [App\Http\Controllers\Admin\LibraryReportController::class, 'overdue'])->name('reports.overdue');
        Route::get('/reports/inventory', [App\Http\Controllers\Admin\LibraryReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/fines', [App\Http\Controllers\Admin\LibraryReportController::class, 'fines'])->name('reports.fines');
        Route::get('/reports/student-wise', [App\Http\Controllers\Admin\LibraryReportController::class, 'studentWise'])->name('reports.student-wise');
        Route::get('/reports/export', [App\Http\Controllers\Admin\LibraryReportController::class, 'export'])->name('reports.export');

        // Library Members
        Route::get('/members', [LibraryMemberController::class, 'index'])->name('members.index');
        Route::get('/members/create', [LibraryMemberController::class, 'create'])->name('members.create');
        Route::post('/members', [LibraryMemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}', [LibraryMemberController::class, 'show'])->name('members.show');
        Route::get('/members/{member}/edit', [LibraryMemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [LibraryMemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [LibraryMemberController::class, 'destroy'])->name('members.destroy');
        Route::post('/members/bulk-delete', [LibraryMemberController::class, 'bulkDelete'])->name('members.bulk-delete');
        Route::get('/members-trash', [LibraryMemberController::class, 'trash'])->name('members.trash');
        Route::post('/members/{id}/restore', [LibraryMemberController::class, 'restore'])->name('members.restore');
        Route::delete('/members/{id}/force-delete', [LibraryMemberController::class, 'forceDelete'])->name('members.force-delete');
        Route::get('/members/{member}/card', [LibraryMemberController::class, 'card'])->name('members.card');
        Route::post('/members/{member}/renew', [LibraryMemberController::class, 'renew'])->name('members.renew');
    });

    // Transport
    Route::prefix('transport')->name('transport.')->group(function () {
        // Vehicles
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::post('/vehicles/bulk-delete', [VehicleController::class, 'bulkDelete'])->name('vehicles.bulk-delete');
        Route::get('/vehicles-trash', [VehicleController::class, 'trash'])->name('vehicles.trash');
        Route::post('/vehicles/{id}/restore', [VehicleController::class, 'restore'])->name('vehicles.restore');
        Route::delete('/vehicles/{id}/force-delete', [VehicleController::class, 'forceDelete'])->name('vehicles.force-delete');
        Route::post('/vehicles/bulk-restore', [VehicleController::class, 'bulkRestore'])->name('vehicles.bulk-restore');
        Route::post('/vehicles/bulk-force-delete', [VehicleController::class, 'bulkForceDelete'])->name('vehicles.bulk-force-delete');
        Route::delete('/vehicles-trash/empty', [VehicleController::class, 'emptyTrash'])->name('vehicles.empty-trash');

        // Routes
        Route::get('/routes', [TransportRouteController::class, 'index'])->name('routes.index');
        Route::get('/routes/create', [TransportRouteController::class, 'create'])->name('routes.create');
        Route::post('/routes', [TransportRouteController::class, 'store'])->name('routes.store');
        Route::get('/routes/{route}/edit', [TransportRouteController::class, 'edit'])->name('routes.edit');
        Route::put('/routes/{route}', [TransportRouteController::class, 'update'])->name('routes.update');
        Route::delete('/routes/{route}', [TransportRouteController::class, 'destroy'])->name('routes.destroy');
        Route::post('/routes/bulk-delete', [TransportRouteController::class, 'bulkDelete'])->name('routes.bulk-delete');
        Route::get('/routes-trash', [TransportRouteController::class, 'trash'])->name('routes.trash');
        Route::post('/routes/{id}/restore', [TransportRouteController::class, 'restore'])->name('routes.restore');
        Route::delete('/routes/{id}/force-delete', [TransportRouteController::class, 'forceDelete'])->name('routes.force-delete');
        Route::post('/routes/bulk-restore', [TransportRouteController::class, 'bulkRestore'])->name('routes.bulk-restore');
        Route::post('/routes/bulk-force-delete', [TransportRouteController::class, 'bulkForceDelete'])->name('routes.bulk-force-delete');
        Route::delete('/routes-trash/empty', [TransportRouteController::class, 'emptyTrash'])->name('routes.empty-trash');
        Route::get('/routes/{route}/stops', [TransportRouteController::class, 'getStops'])->name('routes.stops');

        // Route Assignments
        Route::get('/assignments/students', [RouteAssignmentController::class, 'getStudents'])->name('assignments.students');
        Route::get('/assignments/sections', [RouteAssignmentController::class, 'getSections'])->name('assignments.sections');
        Route::get('/assignments', [RouteAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [RouteAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [RouteAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}/edit', [RouteAssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{assignment}', [RouteAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [RouteAssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::post('/assignments/bulk-delete', [RouteAssignmentController::class, 'bulkDelete'])->name('assignments.bulk-delete');
        Route::get('/assignments-trash', [RouteAssignmentController::class, 'trash'])->name('assignments.trash');
        Route::post('/assignments/{id}/restore', [RouteAssignmentController::class, 'restore'])->name('assignments.restore');
        Route::delete('/assignments/{id}/force-delete', [RouteAssignmentController::class, 'forceDelete'])->name('assignments.force-delete');
        Route::post('/assignments/bulk-restore', [RouteAssignmentController::class, 'bulkRestore'])->name('assignments.bulk-restore');
        Route::post('/assignments/bulk-force-delete', [RouteAssignmentController::class, 'bulkForceDelete'])->name('assignments.bulk-force-delete');
        Route::delete('/assignments-trash/empty', [RouteAssignmentController::class, 'emptyTrash'])->name('assignments.empty-trash');

        // Transport Reports
        Route::get('/reports', [TransportReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/route-wise', [TransportReportController::class, 'routeWise'])->name('reports.route-wise');
        Route::get('/reports/class-wise', [TransportReportController::class, 'classWise'])->name('reports.class-wise');
        Route::get('/reports/vehicle-wise', [TransportReportController::class, 'vehicleWise'])->name('reports.vehicle-wise');
        Route::get('/reports/export-route', [TransportReportController::class, 'exportRouteWise'])->name('reports.export-route');
        Route::get('/reports/export-class', [TransportReportController::class, 'exportClassWise'])->name('reports.export-class');
        Route::get('/reports/export-vehicle', [TransportReportController::class, 'exportVehicleWise'])->name('reports.export-vehicle');

        // Transport Fees
        Route::get('/fees', [TransportFeeController::class, 'index'])->name('fees.index');
        Route::get('/fees/create', [TransportFeeController::class, 'create'])->name('fees.create');
        Route::post('/fees', [TransportFeeController::class, 'store'])->name('fees.store');
        Route::get('/fees/{fee}/edit', [TransportFeeController::class, 'edit'])->name('fees.edit');
        Route::put('/fees/{fee}', [TransportFeeController::class, 'update'])->name('fees.update');
        Route::delete('/fees/{fee}', [TransportFeeController::class, 'destroy'])->name('fees.destroy');
        Route::get('/fees/collections', [TransportFeeController::class, 'collections'])->name('fees.collections');
        Route::get('/fees/collect/{student}', [TransportFeeController::class, 'collectForm'])->name('fees.collect-form');
        Route::post('/fees/collect/{student}', [TransportFeeController::class, 'collect'])->name('fees.collect');
        Route::post('/fees/generate', [TransportFeeController::class, 'generateMonthlyFees'])->name('fees.generate');
        Route::get('/fees/reports', [TransportFeeController::class, 'reports'])->name('fees.reports');
        Route::get('/fees/export-collections', [TransportFeeController::class, 'exportCollections'])->name('fees.export-collections');
    });

    // Drivers
    Route::resource('drivers', DriverController::class);
    Route::post('drivers/bulk-delete', [DriverController::class, 'bulkDelete'])->name('drivers.bulk-delete');
    Route::post('drivers/bulk-restore', [DriverController::class, 'bulkRestore'])->name('drivers.bulk-restore');
    Route::post('drivers/bulk-force-delete', [DriverController::class, 'bulkForceDelete'])->name('drivers.bulk-force-delete');
    Route::get('drivers-trash', [DriverController::class, 'trash'])->name('drivers.trash');
    Route::post('drivers/{id}/restore', [DriverController::class, 'restore'])->name('drivers.restore');
    Route::delete('drivers/{id}/force-delete', [DriverController::class, 'forceDelete'])->name('drivers.force-delete');
    Route::delete('drivers-trash/empty', [DriverController::class, 'emptyTrash'])->name('drivers.empty-trash');
    Route::get('drivers/export', [DriverController::class, 'export'])->name('drivers.export');
    Route::post('drivers/assign-vehicle', [DriverController::class, 'assignVehicle'])->name('drivers.assign-vehicle');
    Route::post('drivers/unassign-vehicle', [DriverController::class, 'unassignVehicle'])->name('drivers.unassign-vehicle');

    // Communication - Notices
    Route::resource('notices', App\Http\Controllers\Admin\NoticeController::class);
    Route::post('notices/bulk-delete', [NoticeController::class, 'bulkDelete'])->name('notices.bulk-delete');
    Route::get('notices-trash', [NoticeController::class, 'trash'])->name('notices.trash');
    Route::post('notices/{id}/restore', [NoticeController::class, 'restore'])->name('notices.restore');
    Route::delete('notices/{id}/force-delete', [NoticeController::class, 'forceDelete'])->name('notices.force-delete');
    Route::post('notices/bulk-restore', [NoticeController::class, 'bulkRestore'])->name('notices.bulk-restore');
    Route::post('notices/bulk-force-delete', [NoticeController::class, 'bulkForceDelete'])->name('notices.bulk-force-delete');
    Route::delete('notices-trash/empty', [NoticeController::class, 'emptyTrash'])->name('notices.empty-trash');

    // Communication - Events
    Route::resource('events', App\Http\Controllers\Admin\EventController::class);
    Route::delete('events/photos/{photo}', [App\Http\Controllers\Admin\EventController::class, 'deletePhoto'])->name('events.photos.destroy');
    Route::post('events/bulk-delete', [EventController::class, 'bulkDelete'])->name('events.bulk-delete');
    Route::get('events-trash', [EventController::class, 'trash'])->name('events.trash');
    Route::post('events/{id}/restore', [EventController::class, 'restore'])->name('events.restore');
    Route::delete('events/{id}/force-delete', [EventController::class, 'forceDelete'])->name('events.force-delete');
    Route::post('events/bulk-restore', [EventController::class, 'bulkRestore'])->name('events.bulk-restore');
    Route::post('events/bulk-force-delete', [EventController::class, 'bulkForceDelete'])->name('events.bulk-force-delete');
    Route::delete('events-trash/empty', [EventController::class, 'emptyTrash'])->name('events.empty-trash');

    // Messaging System
    Route::prefix('messaging')->name('messaging.')->group(function () {
        // Bulk Messages
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::get('/', [BulkMessagingController::class, 'index'])->name('index');
            Route::get('/create', [BulkMessagingController::class, 'create'])->name('create');
            Route::post('/', [BulkMessagingController::class, 'store'])->name('store');
            Route::get('/{bulkMessage}', [BulkMessagingController::class, 'show'])->name('show');
            Route::get('/{bulkMessage}/edit', [BulkMessagingController::class, 'edit'])->name('edit');
            Route::put('/{bulkMessage}', [BulkMessagingController::class, 'update'])->name('update');
            Route::delete('/{bulkMessage}', [BulkMessagingController::class, 'destroy'])->name('destroy');
            Route::post('/{bulkMessage}/send', [BulkMessagingController::class, 'send'])->name('send');
            Route::get('/{bulkMessage}/logs', [BulkMessagingController::class, 'logs'])->name('logs');
        });

        // Inbox Messages (Parent-Teacher Communication)
        Route::prefix('inbox')->name('inbox.')->group(function () {
            Route::get('/', [MessagingController::class, 'index'])->name('index');
            Route::get('/create', [MessagingController::class, 'create'])->name('create');
            Route::post('/', [MessagingController::class, 'store'])->name('store');
            Route::get('/{message}', [MessagingController::class, 'show'])->name('show');
            Route::post('/{message}/reply', [MessagingController::class, 'reply'])->name('reply');
            Route::delete('/{message}', [MessagingController::class, 'destroy'])->name('destroy');
            Route::post('/{message}/mark-read', [MessagingController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [MessagingController::class, 'markAllAsRead'])->name('mark-all-read');
        });

        // Contact Messages (from Student/Parent Portal)
        Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
            Route::get('/', [ContactMessageController::class, 'index'])->name('index');
            Route::get('/{contactMessage}', [ContactMessageController::class, 'show'])->name('show');
            Route::post('/{contactMessage}/respond', [ContactMessageController::class, 'respond'])->name('respond');
            Route::patch('/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('destroy');
        });
    });

    // Alumni
    Route::get('/alumni', [App\Http\Controllers\Admin\AlumniController::class, 'index'])->name('alumni.index');
    Route::post('/alumni/auto-graduate', [App\Http\Controllers\Admin\AlumniController::class, 'autoGraduate'])->name('alumni.auto-graduate');

    // Student Quick Actions
    Route::post('/students/{student}/mark-graduated', [App\Http\Controllers\Admin\AlumniController::class, 'markGraduated'])->name('students.mark-graduated');
    Route::post('/students/{student}/mark-transferred', [App\Http\Controllers\Admin\AlumniController::class, 'markTransferred'])->name('students.mark-transferred');

    // Certificates
    Route::get('/certificates/tc/{student}', [App\Http\Controllers\Admin\CertificateController::class, 'transferCertificate'])->name('certificates.tc');
    Route::get('/certificates/marksheet/{student}', [App\Http\Controllers\Admin\CertificateController::class, 'marksheet'])->name('certificates.marksheet');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/students', [ReportController::class, 'students'])->name('students');
        Route::get('/students/export', [ReportController::class, 'exportStudents'])->name('students.export');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');
        Route::get('/exams', [ReportController::class, 'exams'])->name('exams');
        Route::get('/exams/export', [ReportController::class, 'exportExams'])->name('exams.export');
        Route::get('/fees', [ReportController::class, 'fees'])->name('fees');

        // Custom Report Builder
        Route::prefix('builder')->name('builder.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CustomReportController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\CustomReportController::class, 'create'])->name('create');
            Route::post('/preview', [App\Http\Controllers\Admin\CustomReportController::class, 'preview'])->name('preview');
            Route::post('/export-csv', [App\Http\Controllers\Admin\CustomReportController::class, 'exportCsv'])->name('export-csv');
            Route::post('/export-pdf', [App\Http\Controllers\Admin\CustomReportController::class, 'exportPdf'])->name('export-pdf');
            Route::get('/source-config', [App\Http\Controllers\Admin\CustomReportController::class, 'getSourceConfig'])->name('source-config');
            Route::post('/templates', [App\Http\Controllers\Admin\CustomReportController::class, 'saveTemplate'])->name('save-template');
            Route::get('/templates/{template}', [App\Http\Controllers\Admin\CustomReportController::class, 'loadTemplate'])->name('load-template');
            Route::delete('/templates/{template}', [App\Http\Controllers\Admin\CustomReportController::class, 'deleteTemplate'])->name('delete-template');
        });
    });

    // Student Promotions
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PromotionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\PromotionController::class, 'create'])->name('create');
        Route::post('/process', [App\Http\Controllers\Admin\PromotionController::class, 'process'])->name('process');
        Route::get('/rules', [App\Http\Controllers\Admin\PromotionController::class, 'rules'])->name('rules');
        Route::post('/rules', [App\Http\Controllers\Admin\PromotionController::class, 'storeRule'])->name('rules.store');
        Route::delete('/rules/{rule}', [App\Http\Controllers\Admin\PromotionController::class, 'deleteRule'])->name('rules.delete');
        Route::get('/history', [App\Http\Controllers\Admin\PromotionController::class, 'history'])->name('history');
        Route::delete('/rollback/{promotion}', [App\Http\Controllers\Admin\PromotionController::class, 'rollback'])->name('rollback');
        Route::post('/batches/{batch}/finalize', [App\Http\Controllers\Admin\PromotionController::class, 'finalizeBatch'])->name('batches.finalize');
        Route::get('/sections/{classId}', [App\Http\Controllers\Admin\PromotionController::class, 'getSections'])->name('sections');
        Route::get('/students', [App\Http\Controllers\Admin\PromotionController::class, 'getStudents'])->name('students');
    });

    // Users & Roles
    Route::resource('users', UserController::class);
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    // User Trash
    Route::get('users-trash', [UserController::class, 'trash'])->name('users.trash');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::post('users/bulk-restore', [UserController::class, 'bulkRestore'])->name('users.bulk-restore');
    Route::post('users/bulk-force-delete', [UserController::class, 'bulkForceDelete'])->name('users.bulk-force-delete');
    Route::delete('users-trash/empty', [UserController::class, 'emptyTrash'])->name('users.empty-trash');

    // Roles Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [RoleController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Website Settings
    Route::prefix('website')->name('website.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'index'])->name('index');

        // Sliders
        Route::get('/sliders', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'sliders'])->name('sliders');
        Route::get('/sliders/create', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'createSlider'])->name('sliders.create');
        Route::post('/sliders', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeSlider'])->name('sliders.store');
        Route::get('/sliders/{slider}/edit', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'editSlider'])->name('sliders.edit');
        Route::put('/sliders/{slider}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateSlider'])->name('sliders.update');
        Route::delete('/sliders/{slider}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroySlider'])->name('sliders.destroy');

        // Facilities
        Route::get('/facilities', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'facilities'])->name('facilities');
        Route::get('/facilities/create', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'createFacility'])->name('facilities.create');
        Route::post('/facilities', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeFacility'])->name('facilities.store');
        Route::get('/facilities/{facility}/edit', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'editFacility'])->name('facilities.edit');
        Route::put('/facilities/{facility}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateFacility'])->name('facilities.update');
        Route::delete('/facilities/{facility}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyFacility'])->name('facilities.destroy');

        // Testimonials
        Route::get('/testimonials', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'testimonials'])->name('testimonials');
        Route::get('/testimonials/create', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'createTestimonial'])->name('testimonials.create');
        Route::post('/testimonials', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::get('/testimonials/{testimonial}/edit', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'editTestimonial'])->name('testimonials.edit');
        Route::put('/testimonials/{testimonial}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyTestimonial'])->name('testimonials.destroy');

        // Gallery
        Route::get('/gallery', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'gallery'])->name('gallery');
        Route::get('/gallery/create', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'createGallery'])->name('gallery.create');
        Route::post('/gallery', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeGallery'])->name('gallery.store');
        Route::get('/gallery/{gallery}/edit', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'editGallery'])->name('gallery.edit');
        Route::put('/gallery/{gallery}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateGallery'])->name('gallery.update');
        Route::delete('/gallery/{gallery}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyGallery'])->name('gallery.destroy');

        // Pages
        Route::get('/pages', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'pages'])->name('pages');
        Route::get('/pages/{page}/edit', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'editPage'])->name('pages.edit');
        Route::put('/pages/{page}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'updatePage'])->name('pages.update');

        // Contact Messages
        Route::get('/contacts', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'contacts'])->name('contacts');
        Route::get('/contacts/{contact}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'showContact'])->name('contacts.show');
        Route::post('/contacts/{contact}/reply', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'replyContact'])->name('contacts.reply');
        Route::delete('/contacts/{contact}', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyContact'])->name('contacts.destroy');

        // Website Images
        Route::get('/images', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'images'])->name('images');
        Route::post('/images/upload', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'uploadImage'])->name('images.upload');
        Route::post('/images/delete', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'deleteImage'])->name('images.delete');

        // Homepage Sections
        Route::get('/homepage-sections', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'homepageSections'])->name('homepage-sections');
        Route::put('/homepage-sections', [App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateHomepageSections'])->name('update-homepage-sections');
    });
});

// Student/Parent Portal Routes
Route::prefix('portal')->name('portal.')->middleware(['auth', 'role:Student,Parent'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('dashboard');

    // Switch Child (for parents with multiple children)
    Route::post('/switch-child', [App\Http\Controllers\Portal\DashboardController::class, 'switchChild'])->name('switch-child');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Portal\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [App\Http\Controllers\Portal\ProfileController::class, 'updatePassword'])->name('profile.update-password');

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

    // Online Payment
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/checkout', [App\Http\Controllers\Portal\PaymentController::class, 'checkout'])->name('checkout');
        Route::post('/create-order', [App\Http\Controllers\Portal\PaymentController::class, 'createOrder'])->name('create-order');
        Route::post('/success', [App\Http\Controllers\Portal\PaymentController::class, 'success'])->name('success');
        Route::post('/demo-success', [App\Http\Controllers\Portal\PaymentController::class, 'demoSuccess'])->name('demo-success');
        Route::post('/failure', [App\Http\Controllers\Portal\PaymentController::class, 'failure'])->name('failure');
        Route::get('/receipt/{payment}', [App\Http\Controllers\Portal\PaymentController::class, 'receipt'])->name('receipt');
    });

    // Notices
    Route::get('/notices', [App\Http\Controllers\Portal\NoticeController::class, 'index'])->name('notices');
    Route::get('/notices/{notice}', [App\Http\Controllers\Portal\NoticeController::class, 'show'])->name('notices.show');

    // Events
    Route::get('/events', [App\Http\Controllers\Portal\EventController::class, 'index'])->name('events');
    Route::get('/events/calendar-data', [App\Http\Controllers\Portal\EventController::class, 'calendarEvents'])->name('events.calendar');
    Route::get('/events/{event}', [App\Http\Controllers\Portal\EventController::class, 'show'])->name('events.show');

    // Portal Notifications
    Route::post('/notifications/mark-read', function (\Illuminate\Http\Request $request) {
        $module = $request->input('module');
        auth()->user()->unreadNotifications()
            ->where('type', 'App\\Notifications\\PortalUpdate')
            ->get()
            ->filter(fn($n) => ($n->data['module'] ?? '') === $module)
            ->each(fn($n) => $n->markAsRead());
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');

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

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\ExamController::class, 'index'])->name('index');
        Route::get('/results', [App\Http\Controllers\Portal\ExamController::class, 'results'])->name('results');
        Route::get('/report-card', [App\Http\Controllers\Portal\ExamController::class, 'reportCard'])->name('report-card');
    });

    // Homework
    Route::prefix('homework')->name('homework.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\HomeworkController::class, 'index'])->name('index');
        Route::get('/pending', [App\Http\Controllers\Portal\HomeworkController::class, 'pending'])->name('pending');
        Route::get('/submitted', [App\Http\Controllers\Portal\HomeworkController::class, 'submitted'])->name('submitted');
        Route::get('/{homework}', [App\Http\Controllers\Portal\HomeworkController::class, 'show'])->name('show');
        Route::post('/{homework}/submit', [App\Http\Controllers\Portal\HomeworkController::class, 'submit'])->name('submit');
    });

    // Library
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\LibraryController::class, 'index'])->name('index');
        Route::get('/history', [App\Http\Controllers\Portal\LibraryController::class, 'history'])->name('history');
        Route::get('/search', [App\Http\Controllers\Portal\LibraryController::class, 'search'])->name('search');
        Route::get('/book/{book}', [App\Http\Controllers\Portal\LibraryController::class, 'show'])->name('show');
    });
});

// Teacher/Staff Portal Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:Teacher,Super Admin,Admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Teacher\DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile/password', [App\Http\Controllers\Teacher\DashboardController::class, 'updatePassword'])->name('profile.update-password');

    // Timetable
    Route::get('/timetable', [App\Http\Controllers\Teacher\DashboardController::class, 'timetable'])->name('timetable');

    // My Classes
    Route::get('/my-classes', [App\Http\Controllers\Teacher\DashboardController::class, 'myClasses'])->name('my-classes');
    Route::get('/class-students/{classId}/{sectionId}', [App\Http\Controllers\Teacher\DashboardController::class, 'classStudents'])->name('class-students');

    // Homework
    Route::prefix('homework')->name('homework.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\HomeworkController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Teacher\HomeworkController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Teacher\HomeworkController::class, 'store'])->name('store');
        Route::get('/submissions', [App\Http\Controllers\Teacher\HomeworkController::class, 'submissions'])->name('submissions');
        Route::post('/submissions/{submission}/grade', [App\Http\Controllers\Teacher\HomeworkController::class, 'grade'])->name('grade');
        Route::get('/sections/{classId}', [App\Http\Controllers\Teacher\HomeworkController::class, 'getSections'])->name('sections');
    });

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/mark', [App\Http\Controllers\Teacher\AttendanceController::class, 'mark'])->name('mark');
        Route::post('/mark', [App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('store');
        Route::get('/reports', [App\Http\Controllers\Teacher\AttendanceController::class, 'reports'])->name('reports');
        Route::get('/sections/{classId}', [App\Http\Controllers\Teacher\AttendanceController::class, 'getSections'])->name('sections');
    });

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/schedule', [App\Http\Controllers\Teacher\ExamController::class, 'schedule'])->name('schedule');
        Route::get('/marks', [App\Http\Controllers\Teacher\ExamController::class, 'marks'])->name('marks');
        Route::post('/marks', [App\Http\Controllers\Teacher\ExamController::class, 'storeMarks'])->name('marks.store');
    });

    // Leave Applications
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\LeaveController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Teacher\LeaveController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Teacher\LeaveController::class, 'store'])->name('store');
        Route::get('/balance', [App\Http\Controllers\Teacher\LeaveController::class, 'balance'])->name('balance');
        Route::get('/{leave}', [App\Http\Controllers\Teacher\LeaveController::class, 'show'])->name('show');
        Route::post('/{leave}/cancel', [App\Http\Controllers\Teacher\LeaveController::class, 'cancel'])->name('cancel');
    });

    // Notices
    Route::get('/notices', [App\Http\Controllers\Teacher\NoticeController::class, 'index'])->name('notices');
    Route::get('/notices/{notice}', [App\Http\Controllers\Teacher\NoticeController::class, 'show'])->name('notices.show');

    // Events
    Route::get('/events', [App\Http\Controllers\Teacher\EventController::class, 'index'])->name('events');
    Route::get('/events/{event}', [App\Http\Controllers\Teacher\EventController::class, 'show'])->name('events.show');

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [App\Http\Controllers\Teacher\MessageController::class, 'index'])->name('index');
        Route::get('/compose', [App\Http\Controllers\Teacher\MessageController::class, 'compose'])->name('compose');
        Route::post('/', [App\Http\Controllers\Teacher\MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [App\Http\Controllers\Teacher\MessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [App\Http\Controllers\Teacher\MessageController::class, 'reply'])->name('reply');
    });
});

// Razorpay Webhook — outside auth middleware, CSRF excluded in bootstrap/app.php
Route::post('/razorpay/webhook', [App\Http\Controllers\Portal\PaymentController::class, 'webhook'])
    ->name('razorpay.webhook');
