<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\ModalToggle;

class ModalLink
{
    public static function make(string $modalKey, string $label) : ModalToggle
    {
        return ModalToggle::make($label)
            ->modal($modalKey)
            ->icon('bs.window');
    }

    public static function withIcon(string $modalKey, string $label, string $icon) : ModalToggle
    {
        return self::make($modalKey, $label)
            ->icon($icon);
    }

    public static function large(string $modalKey, string $label) : ModalToggle
    {
        return self::make($modalKey, $label)
            ->size('lg');
    }

    public static function small(string $modalKey, string $label) : ModalToggle
    {
        return self::make($modalKey, $label)
            ->size('sm');
    }

    public static function async(string $modalKey, string $label, string $method) : ModalToggle
    {
        return self::make($modalKey, $label)
            ->method($method);
    }

    public static function confirm(string $modalKey, string $label, string $method, array $parameters = []) : ModalToggle
    {
        return self::make($modalKey, $label)
            ->method($method, $parameters)
            ->icon('bs.question-circle');
    }
}
