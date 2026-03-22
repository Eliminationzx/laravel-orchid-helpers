<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerConfigurations();
        $this->mergeConfigurations();
    }

    private function registerConfigurations() : void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/orchid-helpers.php' => config_path('orchid-helpers.php'),
            ], 'orchid-helpers-config');
        }
    }

    private function mergeConfigurations() : void
    {
        // Merge package configuration with application configuration
        // Example:
        // $this->mergeConfigFrom(__DIR__ . '/../../config/orchid-helpers.php', 'orchid-helpers');
        // $this->mergeConfigFrom(__DIR__ . '/../../config/permissions.php', 'permissions');
        // $this->mergeConfigFrom(__DIR__ . '/../../config/settings.php', 'settings');
        
        // You can also merge multiple config files
        $configFiles = [
            'orchid-helpers' => __DIR__ . '/../../config/orchid-helpers.php',
            // Add more config files as needed
        ];
        
        foreach ($configFiles as $key => $path) {
            if (file_exists($path)) {
                $this->mergeConfigFrom($path, $key);
            }
        }
    }
}