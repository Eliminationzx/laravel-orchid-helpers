<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class AccordionLayout
{
    public static function make(array $items, bool $allowMultiple = false): array
    {
        return [
            Layout::view('orchid-helpers::accordion', [
                'items' => $items,
                'allowMultiple' => $allowMultiple,
            ]),
        ];
    }

    public static function item(string $title, array $content, bool $expanded = false): array
    {
        return [
            'title' => $title,
            'content' => $content,
            'expanded' => $expanded,
        ];
    }
}
