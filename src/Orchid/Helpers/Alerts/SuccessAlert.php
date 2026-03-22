<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class SuccessAlert
{
    public static function make(string $message = 'Operation completed successfully!') : void
    {
        Alert::success(__($message));
    }
}
