<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class UrlSight
{
    public static function make(string $name, string $title = null) : Sight
    {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name) : ?Link {
                    $url = $repository->get($name);

                    if($url === null || $url === '') {
                        return null;
                    }

                    return Link::make($url)
                        ->target('_blank')
                        ->icon('link')
                        ->href($url);
                }
            );
    }
}
