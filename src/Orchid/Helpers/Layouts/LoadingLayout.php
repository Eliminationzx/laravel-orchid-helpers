<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class LoadingLayout
{
    public static function make(string $message = ''): array
    {
        $defaultMessage = $message ?: __('Loading...');
        
        return [
            Layout::view('orchid-helpers::loading', [
                'message' => $defaultMessage,
            ]),
        ];
    }

    public static function spinner(string $message = ''): array
    {
        $defaultMessage = $message ?: __('Please wait...');
        
        return [
            Layout::view('orchid-helpers::loading-spinner', [
                'message' => $defaultMessage,
            ]),
        ];
    }

    public static function skeleton(int $lines = 3): array
    {
        return [
            Layout::view('orchid-helpers::loading-skeleton', [
                'lines' => $lines,
            ]),
        ];
    }

    public static function forTable(int $rows = 5, int $columns = 4): array
    {
        return [
            Layout::view('orchid-helpers::loading-table', [
                'rows' => $rows,
                'columns' => $columns,
            ]),
        ];
    }

    public static function withProgress(string $message = '', int $progress = 0): array
    {
        $defaultMessage = $message ?: __('Processing...');
        
        return [
            Layout::view('orchid-helpers::loading-progress', [
                'message' => $defaultMessage,
                'progress' => $progress,
            ]),
        ];
    }
}