<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    protected $services = [
        // Register service bindings
        // Example:
        // UserService::class,
        // PostService::class,
        // NotificationService::class,
        // CacheService::class,
    ];

    protected $singletons = [
        // Register singleton service bindings
        // Example:
        // LoggerService::class => LoggerService::class,
        // AnalyticsService::class => AnalyticsService::class,
    ];

    public function register() : void
    {
        $this->registerServices();
        $this->registerSingletons();
    }

    public function boot() : void
    {
        $this->registerServicePattern();
    }

    private function registerServices() : void
    {
        foreach ($this->services as $service) {
            $this->app->bind($service, $service);
        }
    }

    private function registerSingletons() : void
    {
        foreach ($this->singletons as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }
    }

    private function registerServicePattern() : void
    {
        // Register service layer conventions
        // Example:
        // $this->app->bind(
        //     UserService::class,
        //     function ($app) {
        //         return new UserService(
        //             $app->make(UserRepositoryInterface::class),
        //             $app->make(CacheService::class),
        //             $app->make(NotificationService::class)
        //         );
        //     }
        // );
        
        // Register service macros or extensions
        // Example:
        // BaseService::macro('withCache', function ($key, $ttl = 3600) {
        //     return $this->cacheService->remember($key, $ttl, function () {
        //         return $this->execute();
        //     });
        // });
        
        // BaseService::macro('withTransaction', function (Closure $callback) {
        //     return DB::transaction($callback);
        // });
    }
}
