<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class ExportButton
{
    public static function make(string $icon = 'bs.download', string $method = 'export') : Button
    {
        return Button::make(__('Export'))
            ->icon($icon)
            ->type(Color::INFO)
            ->method($method);
    }
}