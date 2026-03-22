<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class BackButton
{
    public static function make(string $icon = 'bs.arrow-left', string $method = 'back') : Button
    {
        return Button::make(__('Back'))
            ->icon($icon)
            ->type(Color::SECONDARY)
            ->method($method);
    }
}
