<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class ErrorLayout
{
    public static function make(string $title, string $description = '', array $actions = []): array
    {
        return [
            Layout::view('orchid-helpers::error', [
                'title' => $title,
                'description' => $description,
                'actions' => $actions,
            ]),
        ];
    }

    public static function notFound(string $resource = ''): array
    {
        $title = __('Not Found');
        $description = $resource 
            ? __('The requested :resource could not be found.', ['resource' => $resource])
            : __('The requested resource could not be found.');
        
        return self::make($title, $description);
    }

    public static function unauthorized(): array
    {
        $title = __('Unauthorized');
        $description = __('You do not have permission to access this resource.');
        
        return self::make($title, $description);
    }

    public static function serverError(string $message = ''): array
    {
        $title = __('Server Error');
        $description = $message ?: __('An unexpected error occurred. Please try again later.');
        
        return self::make($title, $description);
    }

    public static function validationErrors(array $errors = []): array
    {
        $title = __('Validation Error');
        $description = __('Please check the form for errors.');
        
        return [
            Layout::view('orchid-helpers::error-validation', [
                'title' => $title,
                'description' => $description,
                'errors' => $errors,
            ]),
        ];
    }

    public static function withCode(string $title, string $code, string $description = ''): array
    {
        return [
            Layout::view('orchid-helpers::error-code', [
                'title' => $title,
                'code' => $code,
                'description' => $description,
            ]),
        ];
    }
}
