<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class DurationSight
{
    public static function make(
        string $name, 
        string $title = null,
        string $format = 'auto',
        bool $showSeconds = true,
        bool $showMilliseconds = false,
        string $separator = ' '
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $format, $showSeconds, $showMilliseconds, $separator) : string {
                    $duration = $repository->get($name);

                    if($duration === null) {
                        return '';
                    }

                    $duration = (float) $duration;
                    
                    // Format the duration
                    $formatted = self::formatDuration($duration, $format, $showSeconds, $showMilliseconds, $separator);
                    
                    return $formatted;
                }
            );
    }

    private static function formatDuration(float $seconds, string $format = 'auto', bool $showSeconds = true, bool $showMilliseconds = false, string $separator = ' '): string
    {
        if ($seconds == 0) {
            return '0s';
        }
        
        $milliseconds = $seconds * 1000;
        
        // Handle milliseconds display
        if ($showMilliseconds && $seconds < 1) {
            return round($milliseconds) . 'ms';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = floor($seconds % 60);
        $remainingMs = round(($seconds - floor($seconds)) * 1000);
        
        $parts = [];
        
        if ($format === 'auto') {
            // Auto format: show only significant parts
            if ($hours > 0) {
                $parts[] = $hours . 'h';
            }
            if ($minutes > 0 || $hours > 0) {
                $parts[] = $minutes . 'm';
            }
            if ($showSeconds && ($secs > 0 || ($hours == 0 && $minutes == 0))) {
                $parts[] = $secs . 's';
            }
            if ($showMilliseconds && $remainingMs > 0 && $seconds < 60) {
                $parts[] = $remainingMs . 'ms';
            }
        } elseif ($format === 'full') {
            // Full format: show all parts
            $parts[] = $hours . 'h';
            $parts[] = $minutes . 'm';
            if ($showSeconds) {
                $parts[] = $secs . 's';
            }
            if ($showMilliseconds && $remainingMs > 0) {
                $parts[] = $remainingMs . 'ms';
            }
        } elseif ($format === 'compact') {
            // Compact format: HH:MM:SS
            if ($hours > 0) {
                $parts[] = str_pad((string) $hours, 2, '0', STR_PAD_LEFT);
            }
            $parts[] = str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
            if ($showSeconds) {
                $parts[] = str_pad((string) $secs, 2, '0', STR_PAD_LEFT);
            }
            return implode(':', $parts);
        } elseif ($format === 'human') {
            // Human readable format
            if ($hours > 0) {
                $parts[] = $hours . ' hour' . ($hours != 1 ? 's' : '');
            }
            if ($minutes > 0) {
                $parts[] = $minutes . ' minute' . ($minutes != 1 ? 's' : '');
            }
            if ($showSeconds && $secs > 0) {
                $parts[] = $secs . ' second' . ($secs != 1 ? 's' : '');
            }
            if ($showMilliseconds && $remainingMs > 0 && $seconds < 1) {
                $parts[] = $remainingMs . ' millisecond' . ($remainingMs != 1 ? 's' : '');
            }
            
            if (empty($parts)) {
                return '0 seconds';
            }
            
            // Join with commas and "and"
            if (count($parts) > 1) {
                $last = array_pop($parts);
                return implode(', ', $parts) . ' and ' . $last;
            }
            
            return $parts[0];
        }
        
        if (empty($parts)) {
            return '0s';
        }
        
        return implode($separator, $parts);
    }
}
