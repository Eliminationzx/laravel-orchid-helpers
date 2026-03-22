<?php

declare(strict_types=1);

namespace Orchid\Helpers\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $repositories = [
        // Register repository bindings
        // Example:
        // UserRepositoryInterface::class => UserRepository::class,
        // PostRepositoryInterface::class => PostRepository::class,
        // CommentRepositoryInterface::class => CommentRepository::class,
    ];

    public function register() : void
    {
        $this->registerRepositories();
    }

    public function boot() : void
    {
        $this->registerRepositoryPattern();
    }

    private function registerRepositories() : void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    private function registerRepositoryPattern() : void
    {
        // Register repository pattern conventions
        // Example:
        // $this->app->bind(
        //     UserRepositoryInterface::class,
        //     function ($app) {
        //         return new UserRepository(
        //             $app->make(User::class),
        //             $app->make(Cache::class)
        //         );
        //     }
        // );
        
        // Register repository macros or extensions
        // Example:
        // BaseRepository::macro('withTrashed', function () {
        //     $this->model = $this->model->withTrashed();
        //     return $this;
        // });
        
        // BaseRepository::macro('onlyTrashed', function () {
        //     $this->model = $this->model->onlyTrashed();
        //     return $this;
        // });
    }
}
