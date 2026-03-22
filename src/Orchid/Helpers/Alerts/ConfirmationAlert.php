<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class ConfirmationAlert
{
    public static function make(string $message = 'Are you sure?', string $confirmText = 'Confirm', string $cancelText = 'Cancel') : void
    {
        // In a real implementation, this would trigger a JavaScript confirmation dialog
        // For now, we'll show a warning alert that indicates confirmation is needed
        Alert::warning(__($message) . ' ' . __(':confirm or :cancel', [
            'confirm' => $confirmText,
            'cancel' => $cancelText
        ]));
    }
}
