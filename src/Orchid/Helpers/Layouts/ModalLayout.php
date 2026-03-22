<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class ModalLayout
{
    public static function make(string $title, array $content, array $options = []): array
    {
        $defaultOptions = [
            'size' => 'md',
            'closeButton' => __('Close'),
            'submitButton' => true,
            'submitText' => __('Save'),
            'method' => 'POST',
        ];

        $options = array_merge($defaultOptions, $options);

        $modal = Layout::modal($title, $content)
            ->size($options['size'])
            ->method($options['method'])
            ->title($title);

        if ($options['closeButton']) {
            $modal = $modal->closeButton($options['closeButton']);
        }

        if ($options['submitButton']) {
            $modal = $modal->applyButton($options['submitText']);
        }

        return [$modal];
    }

    public static function confirm(string $title, string $message, array $options = []): array
    {
        $defaultOptions = [
            'size' => 'sm',
            'confirmText' => __('Confirm'),
            'cancelText' => __('Cancel'),
            'method' => 'POST',
            'type' => 'warning',
        ];

        $options = array_merge($defaultOptions, $options);

        $content = [
            Layout::view('orchid-helpers::modal-confirm', [
                'message' => $message,
                'type' => $options['type'],
            ]),
        ];

        $modal = Layout::modal($title, $content)
            ->size($options['size'])
            ->method($options['method'])
            ->title($title)
            ->applyButton($options['confirmText'])
            ->closeButton($options['cancelText']);

        return [$modal];
    }

    public static function large(string $title, array $content): array
    {
        return self::make($title, $content, ['size' => 'lg']);
    }

    public static function small(string $title, array $content): array
    {
        return self::make($title, $content, ['size' => 'sm']);
    }

    public static function withForm(string $title, array $fields, array $options = []): array
    {
        $content = [
            Layout::rows($fields),
        ];

        return self::make($title, $content, $options);
    }
}
