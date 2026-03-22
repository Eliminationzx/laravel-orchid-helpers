<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class ErrorAlert
{
    public static function make(string $message = 'Произошла ошибка!') : void
    {
        Alert::error(__($message));
    }
}