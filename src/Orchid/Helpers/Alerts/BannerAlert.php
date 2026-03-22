<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class BannerAlert
{
    public static function make(string $message = 'Важное уведомление', string $type = 'info') : void
    {
        // Banner notifications are typically more prominent
        // Using the standard alert method with the specified type
        switch ($type) {
            case 'success':
                Alert::success(__($message));
                break;
            case 'error':
                Alert::error(__($message));
                break;
            case 'warning':
                Alert::warning(__($message));
                break;
            case 'info':
            default:
                Alert::info(__($message));
                break;
        }
    }
}