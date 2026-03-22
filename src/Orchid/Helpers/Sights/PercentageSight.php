<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class PercentageSight
{
    public static function make(
        string $name, 
        string $title = null, 
        int $decimals = 2,
        bool $showSymbol = true,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $decimals, $showSymbol, $decimalSeparator, $thousandsSeparator) : string {
                    $value = $repository->get($name);

                    if($value === null) {
                        return '';
                    }

                    // Format the number as percentage
                    $formatted = number_format(
                        (float) $value, 
                        $decimals, 
                        $decimalSeparator, 
                        $thousandsSeparator
                    );

                    return $showSymbol ? "{$formatted}%" : $formatted;
                }
            );
    }
}