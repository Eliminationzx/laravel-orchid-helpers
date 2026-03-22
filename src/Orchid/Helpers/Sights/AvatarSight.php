<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class AvatarSight
{
    public static function make(
        string $name, 
        string $title = null,
        int $size = 40,
        string $alt = '',
        string $defaultImage = '',
        string $initialsField = null,
        string $backgroundColor = '#6c757d',
        string $textColor = '#ffffff'
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $size, $alt, $defaultImage, $initialsField, $backgroundColor, $textColor) : string {
                    $imageUrl = $repository->get($name);
                    $initials = $initialsField ? $repository->get($initialsField) : '';

                    if(empty($imageUrl) && empty($defaultImage) && empty($initials)) {
                        return '';
                    }

                    // If we have an image URL, use it
                    if(!empty($imageUrl) || !empty($defaultImage)) {
                        $url = $imageUrl ?: $defaultImage;
                        $altText = $alt ?: 'Avatar';
                        $style = "width: {$size}px; height: {$size}px; object-fit: cover;";

                        return <<<HTML
<img src="{$url}" alt="{$altText}" class="rounded-circle" style="{$style}">
HTML;
                    }

                    // Otherwise, create initials avatar
                    $initialsText = self::getInitials($initials);
                    $style = "width: {$size}px; height: {$size}px; background-color: {$backgroundColor}; color: {$textColor}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: " . ($size * 0.4) . "px;";

                    return <<<HTML
<div class="rounded-circle d-inline-flex" style="{$style}">
    {$initialsText}
</div>
HTML;
                }
            );
    }

    private static function getInitials(string $text): string
    {
        if(empty($text)) {
            return '?';
        }

        $words = preg_split('/\s+/', trim($text));
        $initials = '';

        foreach($words as $word) {
            if(!empty($word)) {
                $initials .= mb_substr($word, 0, 1);
            }
            if(mb_strlen($initials) >= 2) {
                break;
            }
        }

        return mb_strtoupper($initials);
    }
}
