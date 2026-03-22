<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Input;

class TextField
{
    public static function make(string $name): Input
    {
        return Input::make("model.$name")
            ->type('text')
            ->title(attrName($name));
    }
}
