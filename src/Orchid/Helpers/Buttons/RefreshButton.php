<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class RefreshButton
{
    public static function make(string $icon = 'bs.arrow-clockwise', string $method = 'refresh') : Button
    {
        return Button::make(__('Refresh'))
            ->icon($icon)
            ->type(Color::DEFAULT)
            ->method($method);
    }
}
