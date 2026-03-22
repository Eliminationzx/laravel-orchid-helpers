<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class WarningAlert
{
    public static function make(string $message = 'Warning!') : void
    {
        Alert::warning(__($message));
    }
}
