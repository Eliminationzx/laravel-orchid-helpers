<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    protected $commands = [
        // Register package commands here
        // Example:
        // Commands\InstallCommand::class,
        // Commands\PublishCommand::class,
        // Commands\MakeHelperCommand::class,
    ];

    public function boot() : void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerSchedules();
        }
    }

    private function registerCommands() : void
    {
        $this->commands($this->commands);
    }

    private function registerSchedules() : void
    {
        // Register scheduled commands
        // Example:
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('orchid-helpers:cleanup')->daily();
        //     $schedule->command('orchid-helpers:backup')->weekly();
        // });
    }
}