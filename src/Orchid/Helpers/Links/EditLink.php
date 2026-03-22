<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Actions\Link;

class EditLink
{
    public static function make() : Link
    {
        return Link::make(__('Edit'))
            ->icon('bs.wrench');
    }

    /**
     * @param  array<string, mixed>|Model  $parameters
     */
    public static function route(string $name, array|Model $parameters = []) : Link
    {
        return self::make()
            ->route($name, $parameters)
            ->can('edit', $parameters);
    }
}
