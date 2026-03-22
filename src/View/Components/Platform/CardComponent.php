<?php

declare(strict_types=1);

namespace OrchidHelpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardComponent extends Component
{
    public function __construct(
        readonly public ?string $title = null,
        readonly public ?string $subtitle = null,
        readonly public ?string $header = null,
        readonly public ?string $footer = null,
        readonly public bool $border = true,
        readonly public bool $shadow = false,
        readonly public string $padding = 'p-3',
        readonly public ?string $icon = null,
        readonly public ?string $background = null,
        readonly public ?string $textColor = null,
    ) {}

    public function cardClass(): string
    {
        $classes = ['card'];

        if ($this->border) {
            $classes[] = 'border';
        } else {
            $classes[] = 'border-0';
        }

        if ($this->shadow) {
            $classes[] = 'shadow-sm';
        }

        if ($this->background) {
            $classes[] = "bg-{$this->background}";
        }

        if ($this->textColor) {
            $classes[] = "text-{$this->textColor}";
        }

        return implode(' ', $classes);
    }

    public function bodyClass(): string
    {
        return "card-body {$this->padding}";
    }

    public function hasHeader(): bool
    {
        return !empty($this->header) || !empty($this->title) || !empty($this->icon);
    }

    public function hasFooter(): bool
    {
        return !empty($this->footer);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.card-component');
    }
}