<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\TaskRead\Interfaces\Services\TaskReadServiceInterface;
use App\Domains\TaskRead\Services\TaskReadService;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskReadServiceInterface::class, TaskReadService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            // $schedule->command('backup:database')->dailyAt('00:00');
            $schedule->command('backup:database')->everyMinute();
        });
    }
}
