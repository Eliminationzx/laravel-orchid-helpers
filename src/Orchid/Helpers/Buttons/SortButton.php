<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class SortButton
{
    public static function make(string $icon = 'bs.sort-down', string $method = 'sort') : Button
    {
        return Button::make(__('Sort'))
            ->icon($icon)
            ->type(Color::DEFAULT)
            ->method($method);
    }
}