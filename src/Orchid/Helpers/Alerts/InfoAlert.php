<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class InfoAlert
{
    public static function make(string $message = 'Информация') : void
    {
        Alert::info(__($message));
    }
}