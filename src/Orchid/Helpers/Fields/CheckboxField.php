<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\CheckBox;

class CheckboxField
{
    public static function make(string $name): CheckBox
    {
        return CheckBox::make("model.$name")
            ->title(attrName($name));
    }
}
