<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class DateTimeSight
{
    public static function make(
        string $name, 
        string $title = null,
        string $format = 'Y-m-d H:i:s',
        string $timezone = null
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository|Model $target) use ($name, $format, $timezone) : string {
                    $item = data_get($target, $name);
                    
                    if($item === null) {
                        return '';
                    }

                    if($item instanceof Carbon) {
                        $carbon = $timezone ? $item->copy()->setTimezone($timezone) : $item;
                        return $carbon->format($format);
                    }

                    // Try to parse string as date
                    try {
                        $carbon = Carbon::parse($item);
                        if($timezone) {
                            $carbon->setTimezone($timezone);
                        }
                        return $carbon->format($format);
                    } catch (\Exception $e) {
                        return (string) $item;
                    }
                }
            );
    }
}
