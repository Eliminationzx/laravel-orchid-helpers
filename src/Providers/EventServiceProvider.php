<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    protected $listen = [
        // Register package event listeners here
        // Example:
        // UserRegistered::class => [
        //     SendWelcomeEmail::class,
        //     LogUserRegistration::class,
        // ],
        // UserLoggedIn::class => [
        //     UpdateLastLoginTimestamp::class,
        // ],
    ];

    protected $subscribe = [
        // Register event subscribers here
        // Example:
        // UserEventSubscriber::class,
    ];

    public function boot() : void
    {
        parent::boot();
        
        $this->registerObservers();
    }

    private function registerObservers() : void
    {
        // Register model observers
        // Example:
        // User::observe(UserObserver::class);
        // Post::observe(PostObserver::class);
    }

    public function shouldDiscoverEvents() : bool
    {
        return false;
    }
}