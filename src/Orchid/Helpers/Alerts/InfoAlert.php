<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class InfoAlert
{
    public static function make(string $message = 'Information') : void
    {
        Alert::info(__($message));
    }
}
