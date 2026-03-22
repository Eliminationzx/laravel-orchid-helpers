<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerMigrations();
        $this->registerSeeders();
    }

    private function registerMigrations() : void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
            
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations/vendor/orchid-helpers'),
            ], 'orchid-helpers-migrations');
        }
    }

    private function registerSeeders() : void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../database/seeders' => database_path('seeders/vendor/orchid-helpers'),
            ], 'orchid-helpers-seeders');
            
            // Register seeders for artisan db:seed command
            // Example:
            // $this->callAfterResolving('seed.handler', function ($handler) {
            //     $handler->register(\OrchidHelpers\Database\Seeders\PackageSeeder::class);
            // });
        }
    }
}