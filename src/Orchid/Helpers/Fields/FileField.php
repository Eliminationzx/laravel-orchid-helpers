<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Upload;

class FileField
{
    public static function make(string $name): Upload
    {
        return Upload::make("model.$name")
            ->title(attrName($name))
            ->maxFiles(1);
    }
}