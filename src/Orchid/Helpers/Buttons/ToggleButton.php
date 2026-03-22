<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class ToggleButton
{
    public static function make(string $icon = 'bs.toggle-on', string $method = 'toggle', array $attributes = []) : Button
    {
        return Button::make(__('Toggle'))
            ->icon($icon)
            ->type(Color::DEFAULT)
            ->method($method, $attributes);
    }
}
