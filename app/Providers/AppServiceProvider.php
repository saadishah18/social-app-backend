<?php

namespace App\Providers;

use App\Repositories\DashboardRepository;
use App\Repositories\Interfaces\DashboardInterface;
use App\Repositories\Interfaces\PostInterface;
use App\Repositories\Interfaces\SettingsInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\PostRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;

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
        //
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
        });
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(PostInterface::class, PostRepository::class);
        $this->app->bind(DashboardInterface::class, DashboardRepository::class);
        $this->app->bind(SettingsInterface::class, SettingsRepository::class);

    }
}
