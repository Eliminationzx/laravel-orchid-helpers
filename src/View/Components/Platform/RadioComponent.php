<?php

declare(strict_types=1);

namespace OrchidHelpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class RadioComponent extends Component
{
    private readonly bool $checked;

    public function __construct(
        readonly public Repository|Model|string|null $target = null,
        readonly public ?string $name = null,
        readonly public mixed $value = '1',
        readonly public ?string $label = null,
        readonly public bool $required = false,
        readonly public bool $disabled = false,
        readonly public bool $inline = false,
        readonly public ?string $id = null,
        readonly public ?string $help = null,
        readonly public ?string $error = null,
        readonly public array $attributes = [],
    ) {
        $this->checked = $this->isChecked();
    }

    private function isChecked(): bool
    {
        if ($this->target instanceof Repository || $this->target instanceof Model) {
            $targetValue = data_get($this->target, $this->name);
            return (string) $targetValue === (string) $this->value;
        }

        if (is_string($this->target)) {
            return $this->target === (string) $this->value;
        }

        $oldValue = old($this->name);
        return $oldValue === (string) $this->value;
    }

    public function radioId(): string
    {
        return $this->id ?? $this->name . '_' . $this->value ?? uniqid('radio_');
    }

    public function wrapperClass(): string
    {
        $classes = ['form-check'];

        if ($this->inline) {
            $classes[] = 'form-check-inline';
        }

        return implode(' ', $classes);
    }

    public function inputClass(): string
    {
        $classes = ['form-check-input'];

        if ($this->error) {
            $classes[] = 'is-invalid';
        }

        return implode(' ', $classes);
    }

    public function labelClass(): string
    {
        return 'form-check-label';
    }

    public function inputAttributes(): array
    {
        $attrs = array_merge([
            'type' => 'radio',
            'name' => $this->name,
            'id' => $this->radioId(),
            'value' => $this->value,
            'class' => $this->inputClass(),
        ], $this->attributes);

        if ($this->checked) {
            $attrs['checked'] = 'checked';
        }

        if ($this->required) {
            $attrs['required'] = 'required';
        }

        if ($this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        // Filter out null values
        return array_filter($attrs, fn($value) => $value !== null);
    }

    public function hasError(): bool
    {
        return !empty($this->error);
    }

    public function hasHelp(): bool
    {
        return !empty($this->help);
    }

    public function hasLabel(): bool
    {
        return !empty($this->label);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.radio-component');
    }
}
