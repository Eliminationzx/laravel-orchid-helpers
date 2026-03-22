<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class ViewButton
{
    public static function make(string $icon = 'bs.eye', string $method = 'show') : Button
    {
        return Button::make(__('View'))
            ->icon($icon)
            ->type(Color::INFO)
            ->method($method);
    }
}