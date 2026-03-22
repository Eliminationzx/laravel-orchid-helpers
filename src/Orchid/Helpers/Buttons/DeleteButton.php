<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Buttons;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class DeleteButton
{
    public static function make(string $icon = 'bs.trash2', string $method = 'destroy', array $attributes = []) : Button
    {
        return Button::make(__('Delete'))
            ->icon($icon)
            ->type(Color::DANGER())
            ->confirm(__('Are you sure you want to delete this item?'))
            ->method($method, $attributes);
    }
}