<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class DownloadButton
{
    public static function make(string $icon = 'bs.download', string $method = 'download') : Button
    {
        return Button::make(__('Download'))
            ->icon($icon)
            ->type(Color::SUCCESS)
            ->method($method);
    }
}
