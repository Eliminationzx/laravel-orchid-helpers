<?php

declare(strict_types=1);

namespace OrchidHelpers\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->registerHelpers();
        $this->registerMacros();
    }

    private function registerHelpers() : void
    {
        // Load package helper files
        $helperFiles = [
            __DIR__ . '/../Support/helpers.php',
            // Add more helper files here as needed
            // __DIR__ . '/../Support/string_helpers.php',
            // __DIR__ . '/../Support/array_helpers.php',
            // __DIR__ . '/../Support/file_helpers.php',
        ];

        foreach ($helperFiles as $helperFile) {
            if (file_exists($helperFile)) {
                require_once $helperFile;
            }
        }
    }

    private function registerMacros() : void
    {
        // Register collection macros
        // Example:
        // Collection::macro('toUpper', function () {
        //     return $this->map(function ($value) {
        //         return is_string($value) ? strtoupper($value) : $value;
        //     });
        // });
        
        // Register string macros
        // Example:
        // Str::macro('reverse', function ($value) {
        //     return strrev($value);
        // });
        
        // Register response macros
        // Example:
        // Response::macro('success', function ($data = null, $message = 'Success', $status = 200) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => $message,
        //         'data' => $data,
        //     ], $status);
        // });
    }
}