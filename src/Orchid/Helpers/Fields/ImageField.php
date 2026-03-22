<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Fields;

use Orchid\Screen\Fields\Picture;

class ImageField
{
    public static function make(string $name): Picture
    {
        return Picture::make("model.$name")
            ->title(attrName($name));
    }
}
