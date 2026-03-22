<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\TextArea;

class TextareaField
{
    public static function make(string $name): TextArea
    {
        return TextArea::make("model.$name")
            ->title(attrName($name));
    }
}