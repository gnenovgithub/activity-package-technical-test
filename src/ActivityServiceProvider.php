<?php

declare(strict_types=1);

namespace Activity;

use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
    }
}