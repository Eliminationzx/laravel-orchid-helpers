<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class PrintButton
{
    public static function make(string $icon = 'bs.printer', string $method = 'print') : Button
    {
        return Button::make(__('Print'))
            ->icon($icon)
            ->type(Color::DEFAULT)
            ->method($method);
    }
}
