<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class SubmitButton
{
    public static function make(string $icon = 'bs.check-circle', string $method = 'submit') : Button
    {
        return Button::make(__('Submit'))
            ->icon($icon)
            ->type(Color::PRIMARY)
            ->method($method);
    }
}