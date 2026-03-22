<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class SaveButton
{
    public static function make(string $icon = 'bs.check-circle', string $method = 'save') : Button
    {
        return Button::make(__('Save'))
            ->icon($icon)
            ->type(Color::DEFAULT)
            ->method($method);
    }
}
