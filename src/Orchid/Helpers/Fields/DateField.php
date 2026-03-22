<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\DateTimer;

class DateField
{
    public static function make(string $name): DateTimer
    {
        return DateTimer::make("model.$name")
            ->title(attrName($name))
            ->format('Y-m-d');
    }
}