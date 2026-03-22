<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Screens;

use OrchidHelpers\\Orchid\Helpers\Buttons\SaveButton;

abstract class EditScreen extends ShowScreen
{
    public function commandBar() : iterable
    {
        return [
            SaveButton::make(),
        ];
    }

    protected function field(string $field) : string
    {
        return "model.$field";
    }
}
