<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class SaveAlert
{
    public static function make(string $message = 'Data saved!') : void
    {
        Alert::success(__($message));
    }
}
