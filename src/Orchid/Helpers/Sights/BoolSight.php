<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use OrchidHelpers\\View\Components\Platform\BoolComponent;
use Orchid\Screen\Sight;

class BoolSight
{
    public static function make(string $name, string $title = null) : Sight
    {
        return Sight::make($name, $title ?? attrName($name))
            ->component(BoolComponent::class, compact('name'));
    }
}
