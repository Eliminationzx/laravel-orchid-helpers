<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class CopyButton
{
    public static function make(string $icon = 'bs.clipboard', string $method = 'copy') : Button
    {
        return Button::make(__('Copy'))
            ->icon($icon)
            ->type(Color::INFO)
            ->method($method);
    }
}
