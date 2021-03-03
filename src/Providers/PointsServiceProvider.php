<?php

namespace Railroad\Points\Providers;

use Illuminate\Support\ServiceProvider;
use Railroad\Points\Events\UserPointsUpdated;
use Railroad\Points\Listeners\PointsEventListener;

class PointsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish config file
        $this->publishes(
            [
                __DIR__ . '/../../config/points.php' => config_path('points.php'),
            ]
        );

        // migrations
        if (config('points.data_mode') == 'host') {
            $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        }
        
        // events
        $this->listen = [
            UserPointsUpdated::class => [PointsEventListener::class . '@handleUserPointsUpdated'],
        ];
    }

    /**
     * Register the application services.migrations
     *
     * @return void
     */
    public function register()
    {

    }
}