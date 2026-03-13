#!/bin/bash

# School Management System - Production Deployment Script
# ========================================================
# This script handles the deployment process for the application.
# Run this script after pulling the latest code from the repository.

echo "=================================================="
echo "School Management System - Deployment Script"
echo "=================================================="
echo ""

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from the project root directory."
    exit 1
fi

# Function to check if a command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check for required commands
if ! command_exists php; then
    echo "Error: PHP is not installed or not in PATH"
    exit 1
fi

if ! command_exists composer; then
    echo "Error: Composer is not installed or not in PATH"
    exit 1
fi

echo "Step 1: Enabling maintenance mode..."
php artisan down --message="System is being updated. Please check back in a few minutes." --retry=60

echo ""
echo "Step 2: Installing/updating dependencies..."
composer install --no-dev --optimize-autoloader

echo ""
echo "Step 3: Running database migrations..."
php artisan migrate --force

echo ""
echo "Step 4: Clearing and rebuilding caches..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

echo ""
echo "Step 5: Creating storage link..."
php artisan storage:link 2>/dev/null || true

echo ""
echo "Step 6: Running application optimization..."
php artisan app:optimize

echo ""
echo "Step 7: Running deployment checks..."
php artisan app:deployment-check

echo ""
echo "Step 8: Disabling maintenance mode..."
php artisan up

echo ""
echo "=================================================="
echo "Deployment completed successfully!"
echo "=================================================="
echo ""
echo "Post-deployment checklist:"
echo "  - Test login functionality"
echo "  - Verify file uploads work correctly"
echo "  - Check email sending (if configured)"
echo "  - Monitor error logs for any issues"
echo ""
