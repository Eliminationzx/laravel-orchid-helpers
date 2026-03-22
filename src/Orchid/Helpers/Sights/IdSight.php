<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Sight;

class IdSight
{
    public static function make() : Sight
    {
        return Sight::make('id', 'ID');
    }
}
