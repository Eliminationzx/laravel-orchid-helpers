<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class ToggleLink
{
    public static function make(string $label, array $attributes, string $method = 'toggle') : Button
    {
        return Button::make($label)
            ->icon('bs.toggle-on')
            ->method($method, $attributes);
    }

    public static function active(string $label = null, array $attributes = [], string $method = 'toggle') : Button
    {
        return self::make($label ?? __('Activate'), $attributes, $method)
            ->type(Color::SUCCESS())
            ->icon('bs.toggle-on');
    }

    public static function inactive(string $label = null, array $attributes = [], string $method = 'toggle') : Button
    {
        return self::make($label ?? __('Deactivate'), $attributes, $method)
            ->type(Color::SECONDARY())
            ->icon('bs.toggle-off');
    }
}
