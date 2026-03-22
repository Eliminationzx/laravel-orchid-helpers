<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class ProgressSight
{
    public static function make(
        string $name, 
        string $title = null,
        int $max = 100,
        string $color = 'primary',
        bool $showLabel = true,
        string $labelFormat = '{value}%',
        int $height = 20
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $max, $color, $showLabel, $labelFormat, $height) : string {
                    $value = $repository->get($name);

                    if($value === null) {
                        return '';
                    }

                    $value = (float) $value;
                    $percentage = min(100, max(0, ($value / $max) * 100));
                    
                    $progressBar = self::renderProgressBar($percentage, $color, $height);
                    
                    if($showLabel) {
                        $label = str_replace('{value}', (string) round($percentage, 1), $labelFormat);
                        $label = str_replace('{raw}', (string) $value, $label);
                        $label = str_replace('{max}', (string) $max, $label);
                        
                        return <<<HTML
<div class="d-flex align-items-center">
    <div class="flex-grow-1 me-3">
        {$progressBar}
    </div>
    <div class="text-nowrap" style="min-width: 60px;">
        {$label}
    </div>
</div>
HTML;
                    }

                    return $progressBar;
                }
            );
    }

    private static function renderProgressBar(float $percentage, string $color, int $height): string
    {
        $colorClass = "bg-{$color}";
        $style = "height: {$height}px;";
        
        return <<<HTML
<div class="progress" style="{$style}">
    <div class="progress-bar {$colorClass}" role="progressbar" style="width: {$percentage}%;" aria-valuenow="{$percentage}" aria-valuemin="0" aria-valuemax="100"></div>
</div>
HTML;
    }
}