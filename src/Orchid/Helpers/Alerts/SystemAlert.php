<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class SystemAlert
{
    public static function make(string $message = 'System notification', string $code = null, string $type = 'warning') : void
    {
        $systemMessage = $code 
            ? __('[System] :message (code: :code)', ['message' => $message, 'code' => $code])
            : __('[System] :message', ['message' => $message]);
        
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
