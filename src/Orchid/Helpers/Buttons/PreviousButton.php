<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class PreviousButton
{
    public static function make(string $icon = 'bs.arrow-left', string $method = 'previous') : Button
    {
        return Button::make(__('Previous'))
            ->icon($icon)
            ->type(Color::SECONDARY)
            ->method($method);
    }
}
