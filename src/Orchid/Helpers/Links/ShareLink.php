<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class ShareLink
{
    public static function make(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Share'))
            ->icon('bs.share')
            ->href('javascript:void(0)')
            ->onClick("window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('" . addslashes($url) . "'), 'facebook-share-dialog', 'width=626,height=436');");
    }

    public static function twitter(string $url, string $text = '', string $label = null) : Link
    {
        $shareUrl = 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($text);
        
        return Link::make($label ?? __('Share on Twitter'))
            ->icon('bs.twitter')
            ->href($shareUrl)
            ->target('_blank')
            ->rel('noopener noreferrer');
    }

    public static function facebook(string $url, string $label = null) : Link
    {
        $shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url);
        
        return Link::make($label ?? __('Share on Facebook'))
            ->icon('bs.facebook')
            ->href($shareUrl)
            ->target('_blank')
            ->rel('noopener noreferrer');
    }

    public static function linkedin(string $url, string $title = '', string $summary = '', string $label = null) : Link
    {
        $shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url);
        
        return Link::make($label ?? __('Share on LinkedIn'))
            ->icon('bs.linkedin')
            ->href($shareUrl)
            ->target('_blank')
            ->rel('noopener noreferrer');
    }
}
