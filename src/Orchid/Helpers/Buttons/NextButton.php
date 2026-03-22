<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class NextButton
{
    public static function make(string $icon = 'bs.arrow-right', string $method = 'next') : Button
    {
        return Button::make(__('Next'))
            ->icon($icon)
            ->type(Color::PRIMARY)
            ->method($method);
    }
}
