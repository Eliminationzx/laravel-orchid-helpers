<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class PrintLink
{
    public static function make(string $label = null) : Link
    {
        return Link::make($label ?? __('Print'))
            ->icon('bs.printer')
            ->href('javascript:window.print()');
    }

    public static function forUrl(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Print'))
            ->icon('bs.printer')
            ->href($url)
            ->target('_blank');
    }
}
