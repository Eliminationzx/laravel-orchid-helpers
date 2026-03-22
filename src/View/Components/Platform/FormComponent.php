<?php

declare(strict_types=1);

namespace OrchidHelpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormComponent extends Component
{
    public function __construct(
        readonly public string $action = '',
        readonly public string $method = 'POST',
        readonly public bool $hasFiles = false,
        readonly public bool $inline = false,
        readonly public string $spacing = 'mb-3',
        readonly public ?string $submitText = null,
        readonly public ?string $cancelText = null,
        readonly public ?string $cancelUrl = null,
        readonly public bool $showCancel = false,
        readonly public string $submitVariant = 'primary',
        readonly public string $cancelVariant = 'secondary',
        readonly public ?string $id = null,
        readonly public array $attributes = [],
    ) {}

    public function formMethod(): string
    {
        $methods = ['POST', 'GET', 'PUT', 'PATCH', 'DELETE'];
        return in_array(strtoupper($this->method), $methods) ? strtoupper($this->method) : 'POST';
    }

    public function isSpoofedMethod(): bool
    {
        return in_array($this->formMethod(), ['PUT', 'PATCH', 'DELETE']);
    }

    public function formClass(): string
    {
        $classes = [];

        if ($this->inline) {
            $classes[] = 'form-inline';
        }

        return implode(' ', $classes);
    }

    public function fieldClass(): string
    {
        return $this->spacing;
    }

    public function buttonGroupClass(): string
    {
        return $this->inline ? 'd-flex align-items-center gap-2' : 'd-flex gap-2';
    }

    public function formAttributes(): array
    {
        $attrs = array_merge([
            'action' => $this->action,
            'method' => $this->formMethod() === 'GET' ? 'GET' : 'POST',
        ], $this->attributes);

        if ($this->hasFiles) {
            $attrs['enctype'] = 'multipart/form-data';
        }

        if ($this->id) {
            $attrs['id'] = $this->id;
        }

        return $attrs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.form-component');
    }
}