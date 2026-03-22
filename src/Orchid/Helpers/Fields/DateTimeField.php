<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\DateTimer;

class DateTimeField
{
    public static function make(string $name): DateTimer
    {
        return DateTimer::make("model.$name")
            ->title(attrName($name))
            ->format('Y-m-d H:i:s')
            ->enableTime();
    }
}
