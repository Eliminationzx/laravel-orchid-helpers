<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Input;

class PasswordField
{
    public static function make(string $name): Input
    {
        return Input::make("model.$name")
            ->type('password')
            ->title(attrName($name));
    }
}