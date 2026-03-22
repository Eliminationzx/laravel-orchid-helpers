<?php

declare(strict_types=1);

namespace Orchid\Helpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AlertComponent extends Component
{
    public function __construct(
        readonly public string $type = 'info',
        readonly public string $message = '',
        readonly public bool $dismissible = false,
        readonly public ?string $icon = null,
    ) {}

    public function alertClass(): string
    {
        $classes = [
            'info' => 'alert-info',
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'danger' => 'alert-danger',
            'error' => 'alert-danger',
            'primary' => 'alert-primary',
            'secondary' => 'alert-secondary',
            'light' => 'alert-light',
            'dark' => 'alert-dark',
        ];

        return $classes[$this->type] ?? $classes['info'];
    }

    public function alertIcon(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        $icons = [
            'info' => 'bs.info-circle',
            'success' => 'bs.check-circle',
            'warning' => 'bs.exclamation-triangle',
            'danger' => 'bs.x-circle',
            'error' => 'bs.x-circle',
            'primary' => 'bs.info',
            'secondary' => 'bs.info',
            'light' => 'bs.info',
            'dark' => 'bs.info',
        ];

        return $icons[$this->type] ?? $icons['info'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.alert-component');
    }
}
