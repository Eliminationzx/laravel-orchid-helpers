<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class AddButton
{
    public static function make(string $icon = 'bs.plus', string $method = 'create') : Button
    {
        return Button::make(__('Add'))
            ->icon($icon)
            ->type(Color::SUCCESS)
            ->method($method);
    }
}
