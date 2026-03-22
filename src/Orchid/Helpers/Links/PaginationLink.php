<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class PaginationLink
{
    public static function previous(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Previous'))
            ->icon('bs.chevron-left')
            ->href($url)
            ->class('page-link');
    }

    public static function next(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Next'))
            ->icon('bs.chevron-right')
            ->href($url)
            ->class('page-link');
    }

    public static function first(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('First'))
            ->icon('bs.chevron-double-left')
            ->href($url)
            ->class('page-link');
    }

    public static function last(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Last'))
            ->icon('bs.chevron-double-right')
            ->href($url)
            ->class('page-link');
    }

    public static function page(int $page, string $url, bool $active = false) : Link
    {
        $link = Link::make((string) $page)
            ->href($url)
            ->class('page-link');

        if ($active) {
            $link->class('page-link active');
        }

        return $link;
    }
}
