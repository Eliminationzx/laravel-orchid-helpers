<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class RefreshLink
{
    public static function make(string $label = null) : Link
    {
        return Link::make($label ?? __('Refresh'))
            ->icon('bs.arrow-clockwise')
            ->href('javascript:location.reload()');
    }

    public static function withUrl(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Refresh'))
            ->icon('bs.arrow-clockwise')
            ->href($url);
    }
}
