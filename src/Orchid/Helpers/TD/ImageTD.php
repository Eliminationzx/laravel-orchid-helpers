<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\TD;

use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class ImageTD
{
    public static function make(
        string $name, 
        string $title = null,
        int $width = 50,
        int $height = 50,
        string $alt = '',
        string $defaultImage = '',
        bool $circular = false
    ) : TD {
        return TD::make($name, $title ?? attrName($name))
            ->alignCenter()
            ->width(($width + 20) . 'px')
            ->render(
                static function(Repository $repository) use ($name, $width, $height, $alt, $defaultImage, $circular) : string {
                    $imageUrl = $repository->get($name);

                    if(empty($imageUrl) && empty($defaultImage)) {
                        return '';
                    }

                    $url = $imageUrl ?: $defaultImage;
                    $altText = $alt ?: 'Image';
                    
                    $class = $circular ? 'rounded-circle' : 'rounded';
                    $style = "width: {$width}px; height: {$height}px; object-fit: cover;";

                    return <<<HTML
<img src="{$url}" alt="{$altText}" class="{$class}" style="{$style}">
HTML;
                }
            );
    }
}
