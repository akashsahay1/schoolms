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
use App\Http\Controllers\Auth\LoginController;

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

    // Placeholder routes for sidebar links (to be implemented in Phase 2)
    Route::get('/profile', function () { return view('admin.coming-soon'); })->name('profile');
    Route::get('/settings', function () { return view('admin.coming-soon'); })->name('settings');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('/students/sections/{classId}', [StudentController::class, 'getSections'])->name('students.sections');

    // Classes
    Route::resource('classes', ClassController::class);

    // Sections
    Route::resource('sections', SectionController::class);

    // Subjects
    Route::resource('subjects', SubjectController::class);
    Route::get('/timetable', function () { return view('admin.coming-soon'); })->name('timetable.index');

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Parents (view-only)
    Route::get('parents', [ParentController::class, 'index'])->name('parents.index');
    Route::get('parents/{parent}', [ParentController::class, 'show'])->name('parents.show');

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/mark', function () { return view('admin.coming-soon'); })->name('mark');
        Route::get('/reports', function () { return view('admin.coming-soon'); })->name('reports');
    });

    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', function () { return view('admin.coming-soon'); })->name('index');
        Route::get('/marks', function () { return view('admin.coming-soon'); })->name('marks');
        Route::get('/results', function () { return view('admin.coming-soon'); })->name('results');
        Route::get('/report-cards', function () { return view('admin.coming-soon'); })->name('report-cards');
    });

    // Homework
    Route::prefix('homework')->name('homework.')->group(function () {
        Route::get('/', function () { return view('admin.coming-soon'); })->name('index');
        Route::get('/create', function () { return view('admin.coming-soon'); })->name('create');
        Route::get('/submissions', function () { return view('admin.coming-soon'); })->name('submissions');
    });

    // Fees
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/structure', function () { return view('admin.coming-soon'); })->name('structure');
        Route::get('/collection', function () { return view('admin.coming-soon'); })->name('collection');
        Route::get('/receipts', function () { return view('admin.coming-soon'); })->name('receipts');
        Route::get('/outstanding', function () { return view('admin.coming-soon'); })->name('outstanding');
        Route::get('/discounts', function () { return view('admin.coming-soon'); })->name('discounts');
    });

    // Staff
    Route::resource('staff', StaffController::class);
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/attendance', function () { return view('admin.coming-soon'); })->name('attendance');
        Route::get('/leaves', function () { return view('admin.coming-soon'); })->name('leaves');
    });

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

    // Communication
    Route::get('/notices', function () { return view('admin.coming-soon'); })->name('notices.index');
    Route::get('/events', function () { return view('admin.coming-soon'); })->name('events.index');
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

// Student Portal Routes (to be fully implemented in Phase 2)
Route::prefix('portal')->name('portal.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('admin.coming-soon'); })->name('dashboard');
    Route::get('/profile', function () { return view('admin.coming-soon'); })->name('profile');
    Route::get('/attendance', function () { return view('admin.coming-soon'); })->name('attendance');
    Route::get('/timetable', function () { return view('admin.coming-soon'); })->name('timetable');
    Route::get('/homework', function () { return view('admin.coming-soon'); })->name('homework');
    Route::get('/exams', function () { return view('admin.coming-soon'); })->name('exams');
    Route::get('/results', function () { return view('admin.coming-soon'); })->name('results');
    Route::get('/report-cards', function () { return view('admin.coming-soon'); })->name('report-cards');

    // Fees
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/overview', function () { return view('admin.coming-soon'); })->name('overview');
        Route::get('/pay', function () { return view('admin.coming-soon'); })->name('pay');
        Route::get('/history', function () { return view('admin.coming-soon'); })->name('history');
        Route::get('/receipts', function () { return view('admin.coming-soon'); })->name('receipts');
    });

    Route::get('/notices', function () { return view('admin.coming-soon'); })->name('notices');
    Route::get('/events', function () { return view('admin.coming-soon'); })->name('events');
    Route::get('/contact', function () { return view('admin.coming-soon'); })->name('contact');
});
