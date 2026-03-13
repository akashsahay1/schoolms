<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Students table indexes
        $this->addIndexSafely('students', 'admission_no', 'students_admission_no_idx');
        $this->addIndexSafely('students', 'status', 'students_status_idx');

        // Fee collections table indexes
        $this->addCompositeIndexSafely('fee_collections', ['student_id', 'payment_date'], 'fee_collections_student_payment_idx');

        // Exam results table indexes
        $this->addCompositeIndexSafely('exam_results', ['exam_id', 'student_id'], 'exam_results_exam_student_idx');

        // Homeworks table indexes
        $this->addCompositeIndexSafely('homeworks', ['class_id', 'section_id'], 'homeworks_class_section_idx');
    }

    /**
     * Add a single-column index safely
     */
    private function addIndexSafely(string $table, string $column, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if (!$this->columnExists($table, $column)) {
            return;
        }

        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column, $indexName) {
            $blueprint->index($column, $indexName);
        });
    }

    /**
     * Add a composite index safely
     */
    private function addCompositeIndexSafely(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        // Check all columns exist
        foreach ($columns as $column) {
            if (!$this->columnExists($table, $column)) {
                return;
            }
        }

        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
            $blueprint->index($columns, $indexName);
        });
    }

    /**
     * Check if a column exists
     */
    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = Schema::getIndexes($table);
            foreach ($indexes as $index) {
                if ($index['name'] === $indexName) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // Table might not exist or error getting indexes
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexSafely('students', 'students_admission_no_idx');
        $this->dropIndexSafely('students', 'students_status_idx');
        $this->dropIndexSafely('fee_collections', 'fee_collections_student_payment_idx');
        $this->dropIndexSafely('exam_results', 'exam_results_exam_student_idx');
        $this->dropIndexSafely('homeworks', 'homeworks_class_section_idx');
    }

    /**
     * Drop index safely
     */
    private function dropIndexSafely(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
            $blueprint->dropIndex($indexName);
        });
    }
};
