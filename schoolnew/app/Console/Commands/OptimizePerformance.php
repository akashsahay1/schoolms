<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize
                            {--clear : Clear all caches instead of building them}
                            {--production : Run production-level optimizations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the application for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('clear')) {
            $this->clearOptimizations();
        } else {
            $this->buildOptimizations();
        }

        return Command::SUCCESS;
    }

    /**
     * Build all optimizations
     */
    protected function buildOptimizations()
    {
        $this->info('Starting performance optimization...');
        $this->newLine();

        // 1. Clear existing caches first
        $this->info('Clearing existing caches...');
        Artisan::call('cache:clear');
        $this->line('  - Application cache cleared');

        // 2. Cache configuration
        $this->info('Caching configuration...');
        Artisan::call('config:cache');
        $this->line('  - Configuration cached');

        // 3. Cache routes
        $this->info('Caching routes...');
        Artisan::call('route:cache');
        $this->line('  - Routes cached');

        // 4. Cache views
        $this->info('Caching views...');
        Artisan::call('view:cache');
        $this->line('  - Views cached');

        // 5. Cache events (Laravel 11+)
        $this->info('Caching events...');
        try {
            Artisan::call('event:cache');
            $this->line('  - Events cached');
        } catch (\Exception $e) {
            $this->line('  - Event caching skipped (not available)');
        }

        // 6. Warm up application cache
        $this->info('Warming up application cache...');
        $this->warmUpCache();
        $this->line('  - Application data cached');

        // 7. Production optimizations
        if ($this->option('production')) {
            $this->info('Running production optimizations...');

            // Optimize composer autoloader
            $this->line('  - Optimizing composer autoloader...');
            exec('composer dump-autoload --optimize --no-dev 2>&1', $output, $returnCode);
            if ($returnCode === 0) {
                $this->line('  - Composer autoloader optimized');
            }
        }

        $this->newLine();
        $this->info('Performance optimization completed successfully!');
        $this->newLine();

        // Display optimization tips
        $this->displayOptimizationTips();
    }

    /**
     * Clear all optimizations
     */
    protected function clearOptimizations()
    {
        $this->info('Clearing all optimizations...');
        $this->newLine();

        Artisan::call('cache:clear');
        $this->line('  - Application cache cleared');

        Artisan::call('config:clear');
        $this->line('  - Configuration cache cleared');

        Artisan::call('route:clear');
        $this->line('  - Route cache cleared');

        Artisan::call('view:clear');
        $this->line('  - View cache cleared');

        try {
            Artisan::call('event:clear');
            $this->line('  - Event cache cleared');
        } catch (\Exception $e) {
            // Event cache not available
        }

        // Clear application-level caches
        CacheService::clearAll();
        $this->line('  - Application data cache cleared');

        $this->newLine();
        $this->info('All caches cleared successfully!');
    }

    /**
     * Warm up the application cache
     */
    protected function warmUpCache()
    {
        // Pre-cache frequently accessed data
        CacheService::getClasses();
        CacheService::getSections();
        CacheService::getAcademicYears();
        CacheService::getCurrentAcademicYear();
        CacheService::getDepartments();
        CacheService::getDesignations();
        CacheService::getFeeTypes();
        CacheService::getSubjects();
        CacheService::getSettings();
        CacheService::getDashboardStats();
    }

    /**
     * Display optimization tips
     */
    protected function displayOptimizationTips()
    {
        $this->info('Optimization Tips:');
        $this->line('  1. Run "php artisan app:optimize --production" before deploying');
        $this->line('  2. Enable OPcache in PHP for better performance');
        $this->line('  3. Use Redis or Memcached for session and cache storage');
        $this->line('  4. Enable database query logging in development only');
        $this->line('  5. Use a CDN for static assets in production');
        $this->line('  6. Enable gzip compression in your web server');
    }
}
