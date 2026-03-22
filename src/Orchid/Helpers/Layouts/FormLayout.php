<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class FormLayout
{
    public static function make(array $fields): array
    {
        return [
            Layout::rows($fields),
        ];
    }

    public static function withSections(array $sections): array
    {
        $layouts = [];
        
        foreach ($sections as $section) {
            $layouts[] = Layout::view('orchid-helpers::form-section', [
                'title' => $section['title'] ?? null,
                'description' => $section['description'] ?? null,
                'fields' => $section['fields'] ?? [],
            ]);
        }
        
        return $layouts;
    }

    public static function inline(array $fields, int $columns = 2): array
    {
        $chunked = array_chunk($fields, $columns);
        
        $rows = [];
        foreach ($chunked as $rowFields) {
            $rows[] = Layout::columns(
                array_map(fn($field) => Layout::rows([$field]), $rowFields)
            );
        }
        
        return $rows;
    }
}