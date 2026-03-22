<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Actions\Link;

class ShowLink
{
    public static function make() : Link
    {
        return Link::make(__('View'))
            ->icon('bs.eye');
    }

    public static function route(string $name, $parameters = []) : Link
    {
        return self::make()
            ->route($name, $parameters)
            ->can('show', $parameters);
    }
}
