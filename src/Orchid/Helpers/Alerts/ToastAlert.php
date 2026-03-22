<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class ToastAlert
{
    public static function make(string $message = 'Notification', string $type = 'success') : void
    {
        Alert::toast(__($message), $type);
    }
}
