<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class EmptyStateLayout
{
    public static function make(string $title, string $description = '', array $actions = []): array
    {
        return [
            Layout::view('orchid-helpers::empty-state', [
                'title' => $title,
                'description' => $description,
                'actions' => $actions,
            ]),
        ];
    }

    public static function withIcon(string $title, string $icon, string $description = ''): array
    {
        return [
            Layout::view('orchid-helpers::empty-state-icon', [
                'title' => $title,
                'icon' => $icon,
                'description' => $description,
            ]),
        ];
    }

    public static function forTable(string $resourceName, array $actions = []): array
    {
        $title = __('No :resource found', ['resource' => $resourceName]);
        $description = __('Create your first :resource to get started.', ['resource' => $resourceName]);
        
        return self::make($title, $description, $actions);
    }

    public static function forSearch(string $query = ''): array
    {
        $title = __('No results found');
        $description = $query 
            ? __('No results found for ":query". Try a different search term.', ['query' => $query])
            : __('Try adjusting your search or filter to find what you\'re looking for.');
        
        return self::make($title, $description);
    }
}
