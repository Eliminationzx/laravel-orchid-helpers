<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class SystemAlert
{
    public static function make(string $message = 'Системное уведомление', string $code = null, string $type = 'warning') : void
    {
        $systemMessage = $code 
            ? __('[Система] :message (код: :code)', ['message' => $message, 'code' => $code])
            : __('[Система] :message', ['message' => $message]);
        
        switch ($type) {
            case 'success':
                Alert::success($systemMessage);
                break;
            case 'error':
                Alert::error($systemMessage);
                break;
            case 'warning':
                Alert::warning($systemMessage);
                break;
            case 'info':
            default:
                Alert::info($systemMessage);
                break;
        }
    }
}