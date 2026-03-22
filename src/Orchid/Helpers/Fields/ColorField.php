<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Input;

class ColorField
{
    public static function make(string $name): Input
    {
        return Input::make("model.$name")
            ->type('color')
            ->title(attrName($name));
    }
}
