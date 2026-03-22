<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class BreadcrumbLink
{
    public static function make(string $label, string $url = null) : Link
    {
        $link = Link::make($label)
            ->class('breadcrumb-item');

        if ($url !== null) {
            $link->href($url);
        }

        return $link;
    }

    public static function home(string $label = null) : Link
    {
        return self::make($label ?? __('Home'))
            ->icon('bs.house');
    }

    public static function active(string $label) : Link
    {
        return Link::make($label)
            ->class('breadcrumb-item active')
            ->href('javascript:void(0)');
    }

    public static function route(string $name, string $label, $parameters = []) : Link
    {
        return self::make($label)
            ->route($name, $parameters);
    }
}
