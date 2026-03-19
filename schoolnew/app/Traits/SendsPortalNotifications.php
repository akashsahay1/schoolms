<?php

namespace App\Traits;

use App\Models\Student;
use App\Models\User;
use App\Notifications\PortalUpdate;
use Illuminate\Support\Facades\Notification;

trait SendsPortalNotifications
{
    /**
     * Notify all students in a class about a module update.
     */
    protected function notifyClassStudents(int $classId, string $module, string $title, string $message = ''): void
    {
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'Student'))
            ->whereIn('id', Student::where('class_id', $classId)->where('status', 'active')->pluck('user_id'))
            ->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new PortalUpdate($module, $title, $message));
        }
    }

    /**
     * Notify all active students about a module update.
     */
    protected function notifyAllStudents(string $module, string $title, string $message = ''): void
    {
        $users = User::whereHas('roles', fn($q) => $q->where('name', 'Student'))
            ->whereIn('id', Student::where('status', 'active')->pluck('user_id'))
            ->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new PortalUpdate($module, $title, $message));
        }
    }

    /**
     * Notify a specific student.
     */
    protected function notifyStudent(int $studentId, string $module, string $title, string $message = ''): void
    {
        $student = Student::find($studentId);
        if ($student && $student->user) {
            $student->user->notify(new PortalUpdate($module, $title, $message));
        }
    }
}
