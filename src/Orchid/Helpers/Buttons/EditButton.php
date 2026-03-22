<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class EditButton
{
    public static function make(string $icon = 'bs.wrench', string $method = 'edit') : Button
    {
        return Button::make(__('Edit'))
            ->icon($icon)
            ->type(Color::PRIMARY)
            ->method($method);
    }
}