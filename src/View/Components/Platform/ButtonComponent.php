<?php

declare(strict_types=1);

namespace OrchidHelpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonComponent extends Component
{
    public function __construct(
        readonly public string $type = 'button',
        readonly public string $variant = 'primary',
        readonly public string $size = 'md',
        readonly public bool $outline = false,
        readonly public bool $disabled = false,
        readonly public ?string $icon = null,
        readonly public string $iconPosition = 'left',
        readonly public ?string $href = null,
        readonly public string $target = '_self',
        readonly public ?string $onclick = null,
    ) {}

    public function buttonClass(): string
    {
        $classes = ['btn'];

        // Variant classes
        if ($this->outline) {
            $classes[] = "btn-outline-{$this->variant}";
        } else {
            $classes[] = "btn-{$this->variant}";
        }

        // Size classes
        $sizeClasses = [
            'sm' => 'btn-sm',
            'md' => '',
            'lg' => 'btn-lg',
        ];
        if (!empty($sizeClasses[$this->size])) {
            $classes[] = $sizeClasses[$this->size];
        }

        // Disabled class
        if ($this->disabled) {
            $classes[] = 'disabled';
        }

        return implode(' ', array_filter($classes));
    }

    public function isLink(): bool
    {
        return !empty($this->href);
    }

    public function tagName(): string
    {
        return $this->isLink() ? 'a' : 'button';
    }

    public function attributes(): array
    {
        $attrs = [];

        if ($this->isLink()) {
            $attrs['href'] = $this->href;
            $attrs['target'] = $this->target;
        } else {
            $attrs['type'] = $this->type;
            if ($this->disabled) {
                $attrs['disabled'] = 'disabled';
            }
        }

        if ($this->onclick) {
            $attrs['onclick'] = $this->onclick;
        }

        return $attrs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.button-component');
    }
}