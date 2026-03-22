<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class TimedAlert
{
    public static function make(string $message = 'Notification', string $type = 'info', int $seconds = 5) : void
    {
        // Add timing information to the message
        $timedMessage = __($message) . ' ' . __('(will automatically close in :seconds seconds)', ['seconds' => $seconds]);
        
        switch ($type) {
            case 'success':
                Alert::success($timedMessage);
                break;
            case 'error':
                Alert::error($timedMessage);
                break;
            case 'warning':
                Alert::warning($timedMessage);
                break;
            case 'info':
            default:
                Alert::info($timedMessage);
                break;
        }
    }
}
