<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class SearchButton
{
    public static function make(string $icon = 'bs.search', string $method = 'search') : Button
    {
        return Button::make(__('Search'))
            ->icon($icon)
            ->type(Color::INFO)
            ->method($method);
    }
}
