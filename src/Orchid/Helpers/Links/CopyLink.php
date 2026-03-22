<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class CopyLink
{
    public static function make(string $text, string $label = null) : Link
    {
        return Link::make($label ?? __('Copy'))
            ->icon('bs.clipboard')
            ->href('javascript:void(0)')
            ->onClick("navigator.clipboard.writeText('" . addslashes($text) . "');");
    }

    public static function withSuccessMessage(string $text, string $label = null, string $message = null) : Link
    {
        $message = $message ?? __('Copied to clipboard!');
        
        return self::make($text, $label)
            ->onClick("navigator.clipboard.writeText('" . addslashes($text) . "'); alert('" . addslashes($message) . "');");
    }
}
