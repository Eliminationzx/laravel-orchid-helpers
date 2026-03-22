<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class CardLayout
{
    public static function make(string $title, array $content): array
    {
        return [
            Layout::view('orchid-helpers::card', [
                'title' => $title,
                'content' => $content,
            ]),
        ];
    }

    public static function blank(array $content): array
    {
        return [
            Layout::view('orchid-helpers::card-blank', [
                'content' => $content,
            ]),
        ];
    }
}
