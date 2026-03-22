<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PolicyServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Register model policies
        // Example:
        // User::class => UserPolicy::class,
        // Post::class => PostPolicy::class,
        // Comment::class => CommentPolicy::class,
    ];

    public function boot() : void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    private function registerPolicies() : void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    private function registerGates() : void
    {
        // Register custom authorization gates
        // Example:
        // Gate::define('update-post', function ($user, $post) {
        //     return $user->id === $post->user_id;
        // });
        
        // Gate::define('delete-comment', function ($user, $comment) {
        //     return $user->id === $comment->user_id || $user->isAdmin();
        // });
        
        // Gate::define('view-admin-dashboard', function ($user) {
        //     return $user->hasRole('admin');
        // });
        
        // Gate::define('manage-users', function ($user) {
        //     return $user->hasPermission('manage_users');
        // });
    }
}