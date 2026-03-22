<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\TD;

use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class TruncatedTextTD
{
    public static function make(
        string $name, 
        string $title = null,
        int $maxLength = 100,
        string $suffix = '...',
        bool $showTooltip = true
    ) : TD {
        return TD::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $maxLength, $suffix, $showTooltip) : string {
                    $text = $repository->get($name);

                    if($text === null) {
                        return '';
                    }

                    $text = (string) $text;
                    
                    if(mb_strlen($text) <= $maxLength) {
                        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                    }

                    $truncated = mb_substr($text, 0, $maxLength) . $suffix;
                    $escapedText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                    $escapedTruncated = htmlspecialchars($truncated, ENT_QUOTES, 'UTF-8');

                    if($showTooltip) {
                        return <<<HTML
<span title="{$escapedText}" data-bs-toggle="tooltip">
    {$escapedTruncated}
</span>
HTML;
                    }

                    return $escapedTruncated;
                }
            );
    }
}