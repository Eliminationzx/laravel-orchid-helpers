<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class SortLink
{
    public static function make(string $field, string $label, string $currentSort = null, string $currentDirection = 'asc') : Link
    {
        $isCurrent = $currentSort === $field;
        $direction = $isCurrent && $currentDirection === 'asc' ? 'desc' : 'asc';
        $icon = $isCurrent ? ($currentDirection === 'asc' ? 'bs.sort-up' : 'bs.sort-down') : 'bs.sort';

        return Link::make($label)
            ->icon($icon)
            ->href('?' . http_build_query(['sort' => $field, 'direction' => $direction]))
            ->class('sort-link');
    }

    public static function ascending(string $field, string $label) : Link
    {
        return Link::make($label)
            ->icon('bs.sort-up')
            ->href('?' . http_build_query(['sort' => $field, 'direction' => 'asc']))
            ->class('sort-link');
    }

    public static function descending(string $field, string $label) : Link
    {
        return Link::make($label)
            ->icon('bs.sort-down')
            ->href('?' . http_build_query(['sort' => $field, 'direction' => 'desc']))
            ->class('sort-link');
    }

    public static function withParams(string $field, string $label, array $params = []) : Link
    {
        $queryParams = array_merge($params, ['sort' => $field, 'direction' => 'asc']);
        
        return Link::make($label)
            ->icon('bs.sort')
            ->href('?' . http_build_query($queryParams))
            ->class('sort-link');
    }
}
