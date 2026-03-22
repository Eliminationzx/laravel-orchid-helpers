<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Quill;

class RichTextField
{
    public static function make(string $name): Quill
    {
        return Quill::make("model.$name")
            ->title(attrName($name));
    }
}
