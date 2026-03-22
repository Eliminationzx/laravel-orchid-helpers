<?php

declare(strict_types=1);

namespace Orchid\Helpers\Providers;

use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    protected $middleware = [
        // Global middleware
        // Example:
        // \OrchidHelpers\Http\Middleware\TrustHosts::class,
        // \OrchidHelpers\Http\Middleware\PreventRequestsDuringMaintenance::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // Web middleware group
            // Example:
            // \OrchidHelpers\Http\Middleware\EncryptCookies::class,
            // \OrchidHelpers\Http\Middleware\VerifyCsrfToken::class,
        ],
        'api' => [
            // API middleware group
            // Example:
            // \OrchidHelpers\Http\Middleware\ThrottleRequests::class . ':api',
            // \OrchidHelpers\Http\Middleware\ForceJsonResponse::class,
        ],
    ];

    protected $routeMiddleware = [
        // Route middleware (aliases)
        // Example:
        // 'auth' => \OrchidHelpers\Http\Middleware\Authenticate::class,
        // 'can' => \OrchidHelpers\Http\Middleware\Authorize::class,
        // 'guest' => \OrchidHelpers\Http\Middleware\RedirectIfAuthenticated::class,
        // 'throttle' => \OrchidHelpers\Http\Middleware\ThrottleRequests::class,
    ];

    protected $middlewarePriority = [
        // Middleware execution priority
        // Example:
        // \Illuminate\Session\Middleware\StartSession::class,
        // \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        // \OrchidHelpers\Http\Middleware\Authenticate::class,
        // \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];

    public function boot() : void
    {
        $this->registerMiddleware();
        $this->registerMiddlewareGroups();
        $this->registerRouteMiddleware();
    }

    private function registerMiddleware() : void
    {
        // Global middleware is typically registered in the HTTP kernel
        // This would need to be done in the application's bootstrap
        // For package development, we document how to add middleware
    }

    private function registerMiddlewareGroups() : void
    {
        $router = $this->app['router'];
        
        foreach ($this->middlewareGroups as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $router->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }

    private function registerRouteMiddleware() : void
    {
        $router = $this->app['router'];
        
        foreach ($this->routeMiddleware as $alias => $middleware) {
            $router->aliasMiddleware($alias, $middleware);
        }
    }
}
