<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Radio;

class RadioField
{
    public static function make(string $name): Radio
    {
        return Radio::make("model.$name")
            ->title(attrName($name));
    }
}
