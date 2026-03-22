<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Link;

class DownloadLink
{
    public static function make(string $url, string $label = null) : Link
    {
        return Link::make($label ?? __('Download'))
            ->icon('bs.download')
            ->href($url)
            ->download();
    }

    public static function withFilename(string $url, string $filename, string $label = null) : Link
    {
        return self::make($url, $label)
            ->download($filename);
    }
}
