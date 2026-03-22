<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\TD;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class EmailTD
{
    public static function make(string $name, string $title = null) : TD
    {
        return TD::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name) : ?Link {
                    $email = $repository->get($name);

                    if($email === null || $email === '') {
                        return null;
                    }

                    return Link::make($email)
                        ->icon('envelope')
                        ->href("mailto:{$email}");
                }
            );
    }
}
