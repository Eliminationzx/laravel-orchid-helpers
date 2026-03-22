<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;

class FilterLink
{
    public static function make(string $label, array $filterParams) : Link
    {
        return Link::make($label)
            ->icon('bs.funnel')
            ->href('?' . http_build_query($filterParams))
            ->class('filter-link');
    }

    public static function active(string $label, array $filterParams) : Link
    {
        return self::make($label, $filterParams)
            ->type(Color::PRIMARY())
            ->icon('bs.funnel-fill');
    }

    public static function clear(string $label = null) : Link
    {
        return Link::make($label ?? __('Clear Filters'))
            ->icon('bs.funnel-x')
            ->href('?')
            ->type(Color::SECONDARY())
            ->class('filter-clear-link');
    }

    public static function toggle(string $label, string $filterKey, $filterValue, bool $isActive = false) : Link
    {
        $params = [$filterKey => $filterValue];
        $link = self::make($label, $params);

        if ($isActive) {
            $link->type(Color::PRIMARY())->icon('bs.funnel-fill');
        }

        return $link;
    }
}
