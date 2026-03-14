<?php

use Illuminate\Support\Facades\Schedule;

// Retry failed queue jobs every 10 minutes
Schedule::command('queue:retry all')->everyTenMinutes()->withoutOverlapping();

// Prune failed jobs older than 7 days
Schedule::command('queue:prune-failed --hours=168')->daily();

// Prune stale batches older than 2 days
Schedule::command('queue:prune-batches --hours=48')->daily();

// Clear expired password reset tokens daily
Schedule::command('auth:clear-resets')->daily();
