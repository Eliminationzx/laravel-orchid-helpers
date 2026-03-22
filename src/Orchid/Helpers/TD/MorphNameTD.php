<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\TD;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\TD;

class MorphNameTD
{
    public static function make(string $name) : TD
    {
        return TD::make($name, __('Object Type'))
            ->render(
                static fn(Model $model) => __(class_basename($model->{$name}))
            );
    }
}
