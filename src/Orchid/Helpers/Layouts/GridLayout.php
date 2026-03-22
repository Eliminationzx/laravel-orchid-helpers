<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class GridLayout
{
    public static function make(array $columns): array
    {
        return [
            Layout::columns($columns),
        ];
    }

    public static function responsive(array $columns, array $breakpoints = []): array
    {
        $defaultBreakpoints = [
            'sm' => 12,
            'md' => 6,
            'lg' => 4,
            'xl' => 3,
        ];

        $breakpoints = array_merge($defaultBreakpoints, $breakpoints);

        $gridColumns = [];
        foreach ($columns as $column) {
            $gridColumns[] = Layout::view('orchid-helpers::grid-column', [
                'content' => $column,
                'breakpoints' => $breakpoints,
            ]);
        }

        return [
            Layout::columns($gridColumns),
        ];
    }

    public static function auto(array $columns, int $maxColumns = 4): array
    {
        $chunked = array_chunk($columns, $maxColumns);
        
        $gridRows = [];
        foreach ($chunked as $rowColumns) {
            $gridRows[] = Layout::columns($rowColumns);
        }
        
        return $gridRows;
    }
}