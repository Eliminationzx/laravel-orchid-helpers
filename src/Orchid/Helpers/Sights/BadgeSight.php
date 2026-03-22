<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;
use Orchid\Support\Blade;

class BadgeSight
{
    public static function make(
        string $name, 
        string $title = null,
        array $colorMap = [],
        string $defaultColor = 'secondary'
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $colorMap, $defaultColor) : string {
                    $value = $repository->get($name);

                    if($value === null) {
                        return '';
                    }

                    $color = self::determineColor((string) $value, $colorMap, $defaultColor);
                    
                    return Blade::renderComponent('orchid::components.badge', [
                        'value' => $value,
                        'color' => $color,
                    ]);
                }
            );
    }

    private static function determineColor(string $value, array $colorMap, string $defaultColor): string
    {
        // Check exact match
        if (isset($colorMap[$value])) {
            return $colorMap[$value];
        }

        // Check case-insensitive match
        $lowerValue = strtolower($value);
        foreach ($colorMap as $key => $color) {
            if (strtolower($key) === $lowerValue) {
                return $color;
            }
        }

        // Check for common status patterns
        $commonColors = [
            'active' => 'success',
            'enabled' => 'success',
            'true' => 'success',
            'yes' => 'success',
            'success' => 'success',
            'completed' => 'success',
            'approved' => 'success',
            
            'inactive' => 'secondary',
            'disabled' => 'secondary',
            'false' => 'secondary',
            'no' => 'secondary',
            'pending' => 'secondary',
            'draft' => 'secondary',
            
            'error' => 'danger',
            'failed' => 'danger',
            'rejected' => 'danger',
            'cancelled' => 'danger',
            'deleted' => 'danger',
            
            'warning' => 'warning',
            'processing' => 'warning',
            'in_progress' => 'warning',
            'waiting' => 'warning',
        ];

        $lowerValue = strtolower($value);
        if (isset($commonColors[$lowerValue])) {
            return $commonColors[$lowerValue];
        }

        return $defaultColor;
    }
}
