<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class TabLayout
{
    public static function make(array $tabs): array
    {
        return [
            Layout::tabs($tabs),
        ];
    }

    public static function withIcons(array $tabsWithIcons): array
    {
        $formattedTabs = [];
        
        foreach ($tabsWithIcons as $key => $tab) {
            $formattedTabs[$key] = [
                'icon' => $tab['icon'] ?? null,
                'layout' => $tab['layout'] ?? [],
            ];
        }
        
        return [
            Layout::tabs($formattedTabs),
        ];
    }
}
