<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class TextSight
{
    public static function make(
        string $name, 
        string $title = null,
        int $maxLength = null,
        string $suffix = '...',
        bool $showTooltip = true
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $maxLength, $suffix, $showTooltip) : string {
                    $text = $repository->get($name);

                    if($text === null) {
                        return '';
                    }

                    $text = (string) $text;
                    
                    // If no maxLength specified or text is shorter than maxLength, return full text
                    if($maxLength === null || mb_strlen($text) <= $maxLength) {
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
