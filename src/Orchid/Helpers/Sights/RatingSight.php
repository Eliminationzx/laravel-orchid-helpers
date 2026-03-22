<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class RatingSight
{
    public static function make(
        string $name, 
        string $title = null,
        int $maxStars = 5,
        string $fullStar = '★',
        string $emptyStar = '☆',
        string $color = '#ffc107',
        bool $showValue = true,
        string $valueFormat = '{rating}/{max}'
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $maxStars, $fullStar, $emptyStar, $color, $showValue, $valueFormat) : string {
                    $value = $repository->get($name);

                    if($value === null) {
                        return '';
                    }

                    $rating = (float) $value;
                    $stars = self::renderStars($rating, $maxStars, $fullStar, $emptyStar, $color);
                    
                    if($showValue) {
                        $valueText = str_replace('{rating}', (string) round($rating, 1), $valueFormat);
                        $valueText = str_replace('{max}', (string) $maxStars, $valueText);
                        
                        return <<<HTML
<div class="d-flex align-items-center">
    <div class="me-2">
        {$stars}
    </div>
    <div class="text-muted small">
        {$valueText}
    </div>
</div>
HTML;
                    }

                    return $stars;
                }
            );
    }

    private static function renderStars(float $rating, int $maxStars, string $fullStar, string $emptyStar, string $color): string
    {
        $stars = '';
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        
        for ($i = 1; $i <= $maxStars; $i++) {
            if ($i <= $fullStars) {
                // Full star
                $stars .= '<span style="color: ' . $color . '; font-size: 1.2em;">' . $fullStar . '</span>';
            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                // Half star
                $stars .= '<span style="color: ' . $color . '; font-size: 1.2em; position: relative;">';
                $stars .= '<span style="opacity: 0.5;">' . $emptyStar . '</span>';
                $stars .= '<span style="position: absolute; left: 0; width: 50%; overflow: hidden;">' . $fullStar . '</span>';
                $stars .= '</span>';
            } else {
                // Empty star
                $stars .= '<span style="color: ' . $color . '; opacity: 0.3; font-size: 1.2em;">' . $emptyStar . '</span>';
            }
        }
        
        return '<div class="d-inline-flex">' . $stars . '</div>';
    }
}
