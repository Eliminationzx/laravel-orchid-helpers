<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Alerts;

use Orchid\Support\Facades\Alert;

class ProgressAlert
{
    public static function make(string $message = 'Processing...', int $progress = 0) : void
    {
        // Show an info alert with progress indication
        $progressMessage = $progress > 0 
            ? __($message) . ' ' . __('(:progress%)', ['progress' => $progress])
            : __($message);
        
        Alert::info($progressMessage);
    }
    
    public static function complete(string $message = 'Completed!') : void
    {
        Alert::success(__($message));
    }
}