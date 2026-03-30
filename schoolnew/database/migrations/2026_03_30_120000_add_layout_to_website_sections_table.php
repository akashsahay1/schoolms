<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Columns already added - layout and bg_color exist
        if (!Schema::hasColumn('website_sections', 'layout')) {
            Schema::table('website_sections', function (Blueprint $table) {
                $table->string('layout')->default('image-left')->after('section_key');
            });
        }
        if (!Schema::hasColumn('website_sections', 'bg_color')) {
            Schema::table('website_sections', function (Blueprint $table) {
                $table->string('bg_color')->nullable()->after('link_text');
            });
        }
    }

    public function down(): void
    {
        Schema::table('website_sections', function (Blueprint $table) {
            $table->dropColumn(['layout', 'bg_color']);
        });
    }
};
