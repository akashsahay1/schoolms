<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Designation;
use App\Models\FeeType;
use App\Models\Subject;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    protected const TTL = 3600;

    /**
     * Long cache TTL (24 hours) for static data
     */
    protected const LONG_TTL = 86400;

    /**
     * Get all active classes (cached)
     */
    public static function getClasses()
    {
        return Cache::remember('classes.active', self::TTL, function () {
            return SchoolClass::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all active sections (cached)
     */
    public static function getSections()
    {
        return Cache::remember('sections.active', self::TTL, function () {
            return Section::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get sections for a specific class (cached)
     */
    public static function getSectionsByClass($classId)
    {
        return Cache::remember("sections.class.{$classId}", self::TTL, function () use ($classId) {
            return Section::where('is_active', true)
                ->whereHas('classes', function ($q) use ($classId) {
                    $q->where('school_class_id', $classId);
                })
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all academic years (cached)
     */
    public static function getAcademicYears()
    {
        return Cache::remember('academic_years.all', self::TTL, function () {
            return AcademicYear::orderBy('start_date', 'desc')->get();
        });
    }

    /**
     * Get current academic year (cached)
     */
    public static function getCurrentAcademicYear()
    {
        return Cache::remember('academic_year.current', self::TTL, function () {
            return AcademicYear::where('is_current', true)->first();
        });
    }

    /**
     * Get all active departments (cached)
     */
    public static function getDepartments()
    {
        return Cache::remember('departments.active', self::TTL, function () {
            return Department::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all active designations (cached)
     */
    public static function getDesignations()
    {
        return Cache::remember('designations.active', self::TTL, function () {
            return Designation::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all active fee types (cached)
     */
    public static function getFeeTypes()
    {
        return Cache::remember('fee_types.active', self::TTL, function () {
            return FeeType::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all active subjects (cached)
     */
    public static function getSubjects()
    {
        return Cache::remember('subjects.active', self::TTL, function () {
            return Subject::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get school settings (cached)
     */
    public static function getSettings()
    {
        return Cache::remember('settings.all', self::LONG_TTL, function () {
            $settings = Setting::pluck('value', 'key')->toArray();
            return $settings;
        });
    }

    /**
     * Get a specific setting (cached)
     */
    public static function getSetting($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Clear all cached data
     */
    public static function clearAll()
    {
        Cache::forget('classes.active');
        Cache::forget('sections.active');
        Cache::forget('academic_years.all');
        Cache::forget('academic_year.current');
        Cache::forget('departments.active');
        Cache::forget('designations.active');
        Cache::forget('fee_types.active');
        Cache::forget('subjects.active');
        Cache::forget('settings.all');

        // Clear section-class caches
        $classes = SchoolClass::pluck('id');
        foreach ($classes as $classId) {
            Cache::forget("sections.class.{$classId}");
        }
    }

    /**
     * Clear class-related caches
     */
    public static function clearClassCache()
    {
        Cache::forget('classes.active');
        $classes = SchoolClass::pluck('id');
        foreach ($classes as $classId) {
            Cache::forget("sections.class.{$classId}");
        }
    }

    /**
     * Clear section-related caches
     */
    public static function clearSectionCache()
    {
        Cache::forget('sections.active');
        $classes = SchoolClass::pluck('id');
        foreach ($classes as $classId) {
            Cache::forget("sections.class.{$classId}");
        }
    }

    /**
     * Clear settings cache
     */
    public static function clearSettingsCache()
    {
        Cache::forget('settings.all');
    }

    /**
     * Get dashboard statistics (cached for 5 minutes)
     */
    public static function getDashboardStats()
    {
        return Cache::remember('dashboard.stats', 300, function () {
            return [
                'total_students' => \App\Models\Student::where('status', 'active')->count(),
                'total_staff' => \App\Models\Staff::where('status', 'active')->count(),
                'total_classes' => SchoolClass::where('is_active', true)->count(),
                'total_subjects' => Subject::where('is_active', true)->count(),
            ];
        });
    }

    /**
     * Clear dashboard stats cache
     */
    public static function clearDashboardCache()
    {
        Cache::forget('dashboard.stats');
    }
}
