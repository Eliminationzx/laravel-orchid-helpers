<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class CurrencySight
{
    public static function make(
        string $name, 
        string $title = null, 
        string $currency = 'USD', 
        int $decimals = 2,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $currency, $decimals, $decimalSeparator, $thousandsSeparator) : string {
                    $value = $repository->get($name);

                    if($value === null) {
                        return '';
                    }

                    // Format the number as currency
                    $formatted = number_format(
                        (float) $value, 
                        $decimals, 
                        $decimalSeparator, 
                        $thousandsSeparator
                    );

                    // Add currency symbol based on currency code
                    $symbol = self::getCurrencySymbol($currency);

                    return "{$symbol}{$formatted}";
                }
            );
    }

    private static function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'RUB' => '₽',
            'INR' => '₹',
            'BRL' => 'R$',
            'CAD' => 'C$',
            'AUD' => 'A$',
        ];

        return $symbols[strtoupper($currency)] ?? strtoupper($currency) . ' ';
    }
}
