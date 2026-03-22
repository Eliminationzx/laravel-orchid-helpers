<?php

declare(strict_types=1);

namespace Orchid\Helpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalComponent extends Component
{
    public function __construct(
        readonly public string $id,
        readonly public ?string $title = null,
        readonly public string $size = 'md',
        readonly public bool $centered = true,
        readonly public bool $scrollable = false,
        readonly public bool $staticBackdrop = false,
        readonly public bool $fade = true,
        readonly public ?string $closeButtonText = null,
        readonly public ?string $submitButtonText = null,
        readonly public ?string $submitButtonVariant = 'primary',
        readonly public bool $showCloseButton = true,
        readonly public bool $showSubmitButton = false,
        readonly public ?string $submitAction = null,
    ) {}

    public function modalClass(): string
    {
        $classes = ['modal'];

        if ($this->fade) {
            $classes[] = 'fade';
        }

        return implode(' ', $classes);
    }

    public function dialogClass(): string
    {
        $classes = ['modal-dialog'];

        if ($this->centered) {
            $classes[] = 'modal-dialog-centered';
        }

        if ($this->scrollable) {
            $classes[] = 'modal-dialog-scrollable';
        }

        $sizeClasses = [
            'sm' => 'modal-sm',
            'md' => '',
            'lg' => 'modal-lg',
            'xl' => 'modal-xl',
        ];

        if (!empty($sizeClasses[$this->size])) {
            $classes[] = $sizeClasses[$this->size];
        }

        return implode(' ', array_filter($classes));
    }

    public function dataAttributes(): array
    {
        $attrs = [];

        if ($this->staticBackdrop) {
            $attrs['data-bs-backdrop'] = 'static';
            $attrs['data-bs-keyboard'] = 'false';
        }

        return $attrs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.modal-component');
    }
}
