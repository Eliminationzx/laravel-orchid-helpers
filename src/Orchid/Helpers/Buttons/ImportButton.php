<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class ImportButton
{
    public static function make(string $icon = 'bs.upload', string $method = 'import') : Button
    {
        return Button::make(__('Import'))
            ->icon($icon)
            ->type(Color::WARNING)
            ->method($method);
    }
}
