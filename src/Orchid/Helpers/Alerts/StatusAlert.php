<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class StatusAlert
{
    public static function make(string $message = 'Status changed', string $oldStatus = null, string $newStatus = null) : void
    {
        if ($oldStatus && $newStatus) {
            $statusMessage = __($message) . ': ' . __(':old → :new', [
                'old' => $oldStatus,
                'new' => $newStatus
            ]);
        } else {
            $statusMessage = __($message);
        }
        
        Alert::info($statusMessage);
    }
}
