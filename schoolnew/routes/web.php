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
use App\Http\Controllers\Admin\FeeDiscountController;
use App\Http\Controllers\Admin\FeeReportController;
use App\Http\Controllers\Admin\ReconciliationController;
use App\Http\Controllers\Admin\HomeworkController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookIssueController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\TransportRouteController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LeaveApplicationController;

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
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Payment Gateway Settings
    Route::get('/settings/payment', [App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('settings.payment');
    Route::put('/settings/payment', [App\Http\Controllers\Admin\PaymentSettingController::class, 'update'])->name('settings.payment.update');
    Route::post('/settings/payment/test', [App\Http\Controllers\Admin\PaymentSettingController::class, 'test'])->name('settings.payment.test');

    // School Settings
    Route::get('/settings/school', [App\Http\Controllers\Admin\SettingController::class, 'school'])->name('settings.school');
    Route::post('/settings/school', [App\Http\Controllers\Admin\SettingController::class, 'updateSchool'])->name('settings.school.update');

    // Library Settings
    Route::get('/settings/library', [App\Http\Controllers\Admin\SettingController::class, 'library'])->name('settings.library');
    Route::post('/settings/library', [App\Http\Controllers\Admin\SettingController::class, 'updateLibrary'])->name('settings.library.update');

    // Academic Years
    Route::resource('academic-years', AcademicYearController::class)->except('show');
    Route::post('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('/students/sections/{classId}', [StudentController::class, 'getSections'])->name('students.sections');
    Route::get('students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');
    Route::post('students/bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');
    // Student Trash
    Route::get('students-trash', [StudentController::class, 'trash'])->name('students.trash');
    Route::post('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
    Route::post('students/bulk-restore', [StudentController::class, 'bulkRestore'])->name('students.bulk-restore');
    Route::post('students/bulk-force-delete', [StudentController::class, 'bulkForceDelete'])->name('students.bulk-force-delete');
    Route::delete('students-trash/empty', [StudentController::class, 'emptyTrash'])->name('students.empty-trash');

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
        Route::get('/outstanding', [FeeCollectionController::class, 'outstanding'])->name('outstanding');

        // Fee Discounts
        Route::get('/discounts', [FeeDiscountController::class, 'index'])->name('discounts.index');
        Route::get('/discounts/create', [FeeDiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discounts', [FeeDiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{discount}/edit', [FeeDiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discounts/{discount}', [FeeDiscountController::class, 'update'])->name('discounts.update');
        Route::delete('/discounts/{discount}', [FeeDiscountController::class, 'destroy'])->name('discounts.destroy');

        // Fee Reports & Analytics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [FeeReportController::class, 'index'])->name('index');
            Route::get('/collection', [FeeReportController::class, 'collection'])->name('collection');
            Route::get('/outstanding', [FeeReportController::class, 'outstanding'])->name('outstanding');
            Route::get('/fee-type-wise', [FeeReportController::class, 'feeTypeWise'])->name('fee-type-wise');
            Route::get('/class-wise', [FeeReportController::class, 'classWise'])->name('class-wise');
            Route::get('/daily', [FeeReportController::class, 'daily'])->name('daily');
            Route::get('/export', [FeeReportController::class, 'export'])->name('export');
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

    // Departments & Designations
    Route::resource('departments', DepartmentController::class);
    Route::post('departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('departments.bulk-delete');
    Route::get('departments-trash', [DepartmentController::class, 'trash'])->name('departments.trash');
    Route::post('departments/{id}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
    Route::delete('departments/{id}/force-delete', [DepartmentController::class, 'forceDelete'])->name('departments.force-delete');
    Route::post('departments/bulk-restore', [DepartmentController::class, 'bulkRestore'])->name('departments.bulk-restore');
    Route::post('departments/bulk-force-delete', [DepartmentController::class, 'bulkForceDelete'])->name('departments.bulk-force-delete');
    Route::delete('departments-trash/empty', [DepartmentController::class, 'emptyTrash'])->name('departments.empty-trash');

    Route::resource('designations', DesignationController::class);
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

        Route::get('/members', function () { return view('admin.coming-soon'); })->name('members');
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

        Route::get('/drivers', function () { return view('admin.coming-soon'); })->name('drivers');
    });

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

    // Messages
    Route::get('/messages', function () { return view('admin.coming-soon'); })->name('messages.index');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/students', function () { return view('admin.coming-soon'); })->name('students');
        Route::get('/fees', function () { return view('admin.coming-soon'); })->name('fees');
        Route::get('/attendance', function () { return view('admin.coming-soon'); })->name('attendance');
        Route::get('/exams', function () { return view('admin.coming-soon'); })->name('exams');
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
    Route::prefix('exams')->name('exams')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\ExamController::class, 'index']);
        Route::get('/results', [App\Http\Controllers\Portal\ExamController::class, 'results'])->name('.results');
        Route::get('/report-card', [App\Http\Controllers\Portal\ExamController::class, 'reportCard'])->name('.report-card');
    });

    // Homework
    Route::prefix('homework')->name('homework')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\HomeworkController::class, 'index']);
        Route::get('/pending', [App\Http\Controllers\Portal\HomeworkController::class, 'pending'])->name('.pending');
        Route::get('/submitted', [App\Http\Controllers\Portal\HomeworkController::class, 'submitted'])->name('.submitted');
        Route::get('/{homework}', [App\Http\Controllers\Portal\HomeworkController::class, 'show'])->name('.show');
        Route::post('/{homework}/submit', [App\Http\Controllers\Portal\HomeworkController::class, 'submit'])->name('.submit');
    });

    // Library
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\LibraryController::class, 'index'])->name('index');
        Route::get('/history', [App\Http\Controllers\Portal\LibraryController::class, 'history'])->name('history');
        Route::get('/search', [App\Http\Controllers\Portal\LibraryController::class, 'search'])->name('search');
        Route::get('/book/{book}', [App\Http\Controllers\Portal\LibraryController::class, 'show'])->name('show');
    });
});
