<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Layouts;

use OrchidHelpers\\Orchid\Helpers\Sights\CreatedAtSight;
use OrchidHelpers\\Orchid\Helpers\Sights\UpdatedAtSight;
use Orchid\Screen\Layouts\Legend;
use Orchid\Support\Facades\Layout;

class ModelTimestampsLayout
{
    public static function make() : Legend
    {
        return Layout::legend('model', [
            UpdatedAtSight::make(),
            CreatedAtSight::make(),
        ])->title(__('Dates'));
    }
}
