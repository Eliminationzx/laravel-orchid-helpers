<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class ExternalLink
{
    public static function make(string $url, string $label = null) : Link
    {
        return Link::make($label ?? $url)
            ->icon('bs.box-arrow-up-right')
            ->href($url)
            ->target('_blank')
            ->rel('noopener noreferrer');
    }

    public static function withIcon(string $url, string $label = null, string $icon = 'bs.box-arrow-up-right') : Link
    {
        return Link::make($label ?? $url)
            ->icon($icon)
            ->href($url)
            ->target('_blank')
            ->rel('noopener noreferrer');
    }
}
