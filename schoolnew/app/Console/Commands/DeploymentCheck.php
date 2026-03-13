<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DeploymentCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deployment-check
                            {--fix : Attempt to fix common issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the application is ready for production deployment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('School Management System - Deployment Readiness Check');
        $this->info('=====================================================');
        $this->newLine();

        $allPassed = true;

        // 1. Environment Check
        $this->info('1. Environment Configuration');
        $allPassed = $this->checkEnvironment() && $allPassed;
        $this->newLine();

        // 2. Database Check
        $this->info('2. Database Connection');
        $allPassed = $this->checkDatabase() && $allPassed;
        $this->newLine();

        // 3. File Permissions
        $this->info('3. File Permissions');
        $allPassed = $this->checkPermissions() && $allPassed;
        $this->newLine();

        // 4. Required Extensions
        $this->info('4. PHP Extensions');
        $allPassed = $this->checkExtensions() && $allPassed;
        $this->newLine();

        // 5. Security Check
        $this->info('5. Security Configuration');
        $allPassed = $this->checkSecurity() && $allPassed;
        $this->newLine();

        // 6. Storage Links
        $this->info('6. Storage Links');
        $allPassed = $this->checkStorageLinks() && $allPassed;
        $this->newLine();

        // 7. Cache Status
        $this->info('7. Cache Status');
        $allPassed = $this->checkCacheStatus() && $allPassed;
        $this->newLine();

        // Summary
        $this->info('=====================================================');
        if ($allPassed) {
            $this->info('All checks passed! Application is ready for deployment.');
        } else {
            $this->error('Some checks failed. Please fix the issues before deploying.');
        }

        return $allPassed ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Check environment configuration
     */
    protected function checkEnvironment(): bool
    {
        $passed = true;

        // APP_ENV
        if (config('app.env') === 'production') {
            $this->line('   [OK] APP_ENV is set to production');
        } else {
            $this->warn('   [WARN] APP_ENV is not set to production (current: ' . config('app.env') . ')');
        }

        // APP_DEBUG
        if (config('app.debug') === false) {
            $this->line('   [OK] APP_DEBUG is disabled');
        } else {
            $this->error('   [FAIL] APP_DEBUG should be disabled in production');
            $passed = false;
        }

        // APP_URL
        if (!str_contains(config('app.url'), 'localhost')) {
            $this->line('   [OK] APP_URL is set: ' . config('app.url'));
        } else {
            $this->warn('   [WARN] APP_URL still contains localhost');
        }

        // APP_KEY
        if (config('app.key')) {
            $this->line('   [OK] APP_KEY is set');
        } else {
            $this->error('   [FAIL] APP_KEY is not set');
            $passed = false;
        }

        return $passed;
    }

    /**
     * Check database connection
     */
    protected function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            $this->line('   [OK] Database connection successful');

            // Check pending migrations
            $pendingMigrations = count(DB::select("SELECT * FROM migrations WHERE batch = 0"));
            if ($pendingMigrations === 0) {
                $this->line('   [OK] No pending migrations');
            } else {
                $this->warn("   [WARN] There may be pending migrations");
            }

            return true;
        } catch (\Exception $e) {
            $this->error('   [FAIL] Database connection failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check file permissions
     */
    protected function checkPermissions(): bool
    {
        $passed = true;
        $directories = [
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($directories as $name => $path) {
            if (File::isWritable($path)) {
                $this->line("   [OK] {$name} is writable");
            } else {
                $this->error("   [FAIL] {$name} is not writable");
                $passed = false;
            }
        }

        return $passed;
    }

    /**
     * Check required PHP extensions
     */
    protected function checkExtensions(): bool
    {
        $passed = true;
        $required = [
            'openssl',
            'pdo',
            'pdo_mysql',
            'mbstring',
            'tokenizer',
            'xml',
            'ctype',
            'json',
            'bcmath',
            'fileinfo',
            'gd',
        ];

        foreach ($required as $ext) {
            if (extension_loaded($ext)) {
                $this->line("   [OK] {$ext} extension loaded");
            } else {
                $this->error("   [FAIL] {$ext} extension not loaded");
                $passed = false;
            }
        }

        // Check OPcache (recommended)
        if (extension_loaded('Zend OPcache')) {
            $this->line('   [OK] OPcache extension loaded (recommended for performance)');
        } else {
            $this->warn('   [WARN] OPcache extension not loaded (recommended for performance)');
        }

        return $passed;
    }

    /**
     * Check security configuration
     */
    protected function checkSecurity(): bool
    {
        $passed = true;

        // Session security
        if (config('session.secure') === true || config('session.secure') === null) {
            $this->line('   [OK] Session secure cookie configured');
        } else {
            $this->warn('   [WARN] SESSION_SECURE_COOKIE should be true in production');
        }

        if (config('session.http_only') === true) {
            $this->line('   [OK] HTTP-only session cookies enabled');
        } else {
            $this->error('   [FAIL] HTTP-only session cookies should be enabled');
            $passed = false;
        }

        // HTTPS check
        if (str_starts_with(config('app.url'), 'https://')) {
            $this->line('   [OK] APP_URL uses HTTPS');
        } else {
            $this->warn('   [WARN] APP_URL should use HTTPS in production');
        }

        // .env file not accessible
        if (!File::exists(public_path('.env'))) {
            $this->line('   [OK] .env file not in public directory');
        } else {
            $this->error('   [FAIL] .env file found in public directory!');
            $passed = false;
        }

        return $passed;
    }

    /**
     * Check storage links
     */
    protected function checkStorageLinks(): bool
    {
        $publicStorage = public_path('storage');

        if (File::exists($publicStorage) && is_link($publicStorage)) {
            $this->line('   [OK] Storage link exists');
            return true;
        } else {
            $this->error('   [FAIL] Storage link not found');
            $this->line('   Run: php artisan storage:link');

            if ($this->option('fix')) {
                $this->call('storage:link');
                return true;
            }

            return false;
        }
    }

    /**
     * Check cache status
     */
    protected function checkCacheStatus(): bool
    {
        // Check if config is cached
        $configCached = File::exists(base_path('bootstrap/cache/config.php'));
        if ($configCached) {
            $this->line('   [OK] Configuration is cached');
        } else {
            $this->warn('   [WARN] Configuration is not cached');
            $this->line('   Run: php artisan config:cache');
        }

        // Check if routes are cached
        $routesCached = File::exists(base_path('bootstrap/cache/routes-v7.php'));
        if ($routesCached) {
            $this->line('   [OK] Routes are cached');
        } else {
            $this->warn('   [WARN] Routes are not cached');
            $this->line('   Run: php artisan route:cache');
        }

        // Check if views are cached
        $viewsCached = count(File::files(storage_path('framework/views'))) > 0;
        if ($viewsCached) {
            $this->line('   [OK] Views are compiled');
        } else {
            $this->warn('   [WARN] Views are not compiled');
            $this->line('   Run: php artisan view:cache');
        }

        return true;
    }
}
