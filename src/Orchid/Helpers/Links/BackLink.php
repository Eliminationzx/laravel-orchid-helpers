<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class BackLink
{
    public static function make(string $label = null) : Link
    {
        return Link::make($label ?? __('Back'))
            ->icon('bs.arrow-left')
            ->href('javascript:history.back()');
    }

    public static function route(string $name, $parameters = []) : Link
    {
        return Link::make(__('Back'))
            ->icon('bs.arrow-left')
            ->route($name, $parameters);
    }
}
