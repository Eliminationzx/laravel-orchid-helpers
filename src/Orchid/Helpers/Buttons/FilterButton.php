<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class FilterButton
{
    public static function make(string $icon = 'bs.funnel', string $method = 'filter') : Button
    {
        return Button::make(__('Filter'))
            ->icon($icon)
            ->type(Color::SECONDARY)
            ->method($method);
    }
}
