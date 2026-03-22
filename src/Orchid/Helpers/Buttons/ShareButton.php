<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class ShareButton
{
    public static function make(string $icon = 'bs.share', string $method = 'share') : Button
    {
        return Button::make(__('Share'))
            ->icon($icon)
            ->type(Color::INFO)
            ->method($method);
    }
}