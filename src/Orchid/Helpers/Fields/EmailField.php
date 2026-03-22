<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Input;

class EmailField
{
    public static function make(string $name): Input
    {
        return Input::make("model.$name")
            ->type('email')
            ->title(attrName($name));
    }
}
