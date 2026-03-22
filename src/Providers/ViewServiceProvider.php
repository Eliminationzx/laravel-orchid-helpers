<?php

declare(strict_types=1);

namespace Orchid\Helpers\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerViews();
        $this->registerViewComposers();
    }

    private function registerViews() : void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'orchid-helpers');
        
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/orchid-helpers'),
            ], 'orchid-helpers-views');
        }
    }

    private function registerViewComposers() : void
    {
        // Register view composers here
        // Example:
        // View::composer('orchid-helpers::components.platform.*', function ($view) {
        //     $view->with('version', '1.0.0');
        // });
        
        // View::composer('orchid-helpers::platform.print', PrintViewComposer::class);
    }
}
