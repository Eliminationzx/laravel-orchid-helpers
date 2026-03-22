<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class HomeLink
{
    public static function make(string $label = null) : Link
    {
        return Link::make($label ?? __('Home'))
            ->icon('bs.house');
    }

    public static function route(string $name, $parameters = []) : Link
    {
        return self::make()
            ->route($name, $parameters);
    }
}
