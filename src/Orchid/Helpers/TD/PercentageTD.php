<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\TD;

use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class PercentageTD
{
    public static function make(
        string $name, 
        string $title = null, 
        int $decimals = 2,
        bool $showSymbol = true,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ) : TD {
        return TD::make($name, $title ?? attrName($name))
            ->alignRight()
            ->sort()
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
