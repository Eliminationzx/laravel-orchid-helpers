<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class DestroyAlert
{
    public static function make(string $message = 'Data deleted!') : void
    {
        Alert::success(__($message));
    }
}
