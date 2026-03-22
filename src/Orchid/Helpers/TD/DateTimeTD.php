<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\TD;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class DateTimeTD
{
    public static function make(
        string $name, 
        string $title = null,
        string $format = 'Y-m-d H:i:s',
        string $timezone = null
    ) : TD {
        return TD::make($name, $title ?? attrName($name))
            ->sort()
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
