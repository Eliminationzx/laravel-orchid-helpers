<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class ActionAlert
{
    public static function make(string $message = 'Notification', string $actionText = 'Action', string $actionUrl = '#', string $type = 'info') : void
    {
        // Add action indicator to the message
        $actionMessage = __($message) . ' ' . __('[:action]', ['action' => $actionText]);
        
        switch ($type) {
            case 'success':
                Alert::success($actionMessage);
                break;
            case 'error':
                Alert::error($actionMessage);
                break;
            case 'warning':
                Alert::warning($actionMessage);
                break;
            case 'info':
            default:
                Alert::info($actionMessage);
                break;
        }
    }
}