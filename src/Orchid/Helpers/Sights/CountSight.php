<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class CountSight
{
    public static function make(
        string $name, 
        string $title = null,
        bool $showBadge = false,
        string $badgeColor = 'primary',
        string $emptyText = '0',
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $showBadge, $badgeColor, $emptyText, $decimalSeparator, $thousandsSeparator) : string {
                    $count = $repository->get($name);

                    if($count === null) {
                        return $emptyText;
                    }

                    $count = (int) $count;
                    
                    // Format the number
                    $formatted = number_format($count, 0, $decimalSeparator, $thousandsSeparator);
                    
                    if($showBadge) {
                        return <<<HTML
<span class="badge bg-{$badgeColor} rounded-pill">
    {$formatted}
</span>
HTML;
                    }

                    return $formatted;
                }
            );
    }
}
