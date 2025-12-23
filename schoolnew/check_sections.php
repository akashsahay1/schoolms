<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Section;
use App\Models\SchoolClass;

echo "=== Checking Sections ===\n\n";

$totalSections = Section::count();
$activeSections = Section::where('is_active', true)->count();

echo "Total sections: {$totalSections}\n";
echo "Active sections: {$activeSections}\n\n";

if ($totalSections > 0) {
    echo "First 10 sections:\n";
    echo str_repeat('-', 80) . "\n";

    Section::with('schoolClass')
        ->take(10)
        ->get()
        ->each(function($section) {
            $className = $section->schoolClass->name ?? 'N/A';
            $active = $section->is_active ? 'Yes' : 'No';
            echo "ID: {$section->id} | Section: {$section->name} | Class: {$className} | Active: {$active}\n";
        });
} else {
    echo "No sections found in database.\n";
}

echo "\n=== Checking Classes ===\n\n";

$classes = SchoolClass::where('is_active', true)->get();
echo "Total active classes: {$classes->count()}\n\n";

if ($classes->count() > 0) {
    echo "Classes and their sections:\n";
    echo str_repeat('-', 80) . "\n";

    foreach ($classes as $class) {
        $sectionsCount = Section::where('class_id', $class->id)->where('is_active', true)->count();
        echo "Class: {$class->name} (ID: {$class->id}) - Active Sections: {$sectionsCount}\n";

        $sections = Section::where('class_id', $class->id)->where('is_active', true)->get();
        foreach ($sections as $section) {
            echo "  - {$section->name}\n";
        }
    }
}
