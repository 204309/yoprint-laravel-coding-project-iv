<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\FileProcessStatusUpdated;
use App\Listeners\UpdateFileProcessStatus;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listener
        $this->app['events']->listen(
            FileProcessStatusUpdated::class,
            UpdateFileProcessStatus::class
        );
    }
}
