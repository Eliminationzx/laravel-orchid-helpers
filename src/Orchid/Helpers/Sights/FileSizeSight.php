<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class FileSizeSight
{
    public static function make(
        string $name, 
        string $title = null,
        int $precision = 2,
        bool $showBytes = false,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ','
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $precision, $showBytes, $decimalSeparator, $thousandsSeparator) : string {
                    $size = $repository->get($name);

                    if($size === null) {
                        return '';
                    }

                    $size = (float) $size;
                    
                    // Format the file size
                    $formatted = self::formatFileSize($size, $precision, $decimalSeparator, $thousandsSeparator);
                    
                    if($showBytes) {
                        $bytes = number_format($size, 0, $decimalSeparator, $thousandsSeparator);
                        return <<<HTML
<div class="file-size-container">
    <div class="formatted-size">{$formatted}</div>
    <div class="byte-size text-muted small">{$bytes} bytes</div>
</div>
HTML;
                    }

                    return $formatted;
                }
            );
    }

    private static function formatFileSize(float $bytes, int $precision = 2, string $decimalSeparator = '.', string $thousandsSeparator = ','): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        
        if ($bytes == 0) {
            return '0 B';
        }
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        $formatted = number_format($bytes, $precision, $decimalSeparator, $thousandsSeparator);
        
        return $formatted . ' ' . $units[$pow];
    }
}
