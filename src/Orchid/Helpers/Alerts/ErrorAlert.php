<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class ErrorAlert
{
    public static function make(string $message = 'An error occurred!') : void
    {
        Alert::error(__($message));
    }
}
