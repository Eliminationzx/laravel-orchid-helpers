<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class DismissibleAlert
{
    public static function make(string $message = 'Notification', string $type = 'info') : void
    {
        // Add dismissible indicator to the message
        $dismissibleMessage = __($message) . ' ' . __('(can be closed)');
        
        switch ($type) {
            case 'success':
                Alert::success($dismissibleMessage);
                break;
            case 'error':
                Alert::error($dismissibleMessage);
                break;
            case 'warning':
                Alert::warning($dismissibleMessage);
                break;
            case 'info':
            default:
                Alert::info($dismissibleMessage);
                break;
        }
    }
}
