<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class PhoneSight
{
    public static function make(string $name, string $title = null) : Sight
    {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name) : ?Link {
                    $phone = $repository->get($name);

                    if($phone === null || $phone === '') {
                        return null;
                    }

                    // Clean phone number for tel: link (remove non-numeric characters except +)
                    $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

                    return Link::make($phone)
                        ->icon('phone')
                        ->href("tel:{$cleanPhone}");
                }
            );
    }
}
