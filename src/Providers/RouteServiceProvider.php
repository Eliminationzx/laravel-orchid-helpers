<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    public function boot() : void
    {
        $this->configureRateLimiting();
        $this->configureRouteModelBinding();
        $this->registerRoutes();
    }

    private function configureRateLimiting() : void
    {
        // Configure rate limiting for package routes if needed
        // Example:
        // RateLimiter::for('orchid-helpers-api', function (Request $request) {
        //     return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        // });
    }

    private function configureRouteModelBinding() : void
    {
        // Configure explicit route model binding
        // Example:
        // Route::model('user', User::class);
        // Route::bind('user', function ($value) {
        //     return User::where('uuid', $value)->firstOrFail();
        // });
    }

    private function registerRoutes() : void
    {
        // Register package routes
        // Example:
        // Route::middleware('web')
        //     ->prefix('orchid-helpers')
        //     ->name('orchid-helpers.')
        //     ->group(function () {
        //         Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        //     });
        
        // Register API routes if needed
        // Route::middleware('api')
        //     ->prefix('api/orchid-helpers')
        //     ->name('api.orchid-helpers.')
        //     ->group(function () {
        //         Route::apiResource('users', UserController::class);
        //     });
    }
}