<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class InlineAlert
{
    public static function make(string $message = 'Please check the entered data', string $field = null) : void
    {
        $fullMessage = $field ? __('Field :field: :message', ['field' => $field, 'message' => $message]) : __($message);
        Alert::error($fullMessage);
    }
}
