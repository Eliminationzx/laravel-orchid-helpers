<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Select;

class SelectField
{
    public static function make(string $name): Select
    {
        return Select::make("model.$name")
            ->title(attrName($name));
    }
}