<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\TD;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class PhoneTD
{
    public static function make(string $name, string $title = null) : TD
    {
        return TD::make($name, $title ?? attrName($name))
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