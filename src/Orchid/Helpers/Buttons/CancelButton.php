<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class CancelButton
{
    public static function make(string $icon = 'bs.x-circle', string $method = 'cancel') : Button
    {
        return Button::make(__('Cancel'))
            ->icon($icon)
            ->type(Color::SECONDARY)
            ->method($method);
    }
}
