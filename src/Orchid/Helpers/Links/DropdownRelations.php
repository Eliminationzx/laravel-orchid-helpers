<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Orchid\Screen\Actions\DropDown;

class DropdownRelations
{
    public static function make() : DropDown
    {
        return DropDown::make(__('Relations'))
            ->icon('bs.share');
    }
}
