<?php

declare(strict_types=1);

namespace OrchidHelpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class InputComponent extends Component
{
    private readonly mixed $value;

    public function __construct(
        readonly public Repository|Model|string|null $target = null,
        readonly public ?string $name = null,
        readonly public string $type = 'text',
        readonly public ?string $label = null,
        readonly public ?string $placeholder = null,
        readonly public bool $required = false,
        readonly public bool $disabled = false,
        readonly public bool $readonly = false,
        readonly public ?string $id = null,
        readonly public ?string $help = null,
        readonly public ?string $error = null,
        readonly public array $attributes = [],
    ) {
        $this->value = $this->getValue();
    }

    private function getValue(): mixed
    {
        if ($this->target instanceof Repository || $this->target instanceof Model) {
            return data_get($this->target, $this->name);
        }

        if (is_string($this->target)) {
            return $this->target;
        }

        return old($this->name);
    }

    public function inputId(): string
    {
        return $this->id ?? $this->name ?? uniqid('input_');
    }

    public function inputClass(): string
    {
        $classes = ['form-control'];

        if ($this->error) {
            $classes[] = 'is-invalid';
        }

        return implode(' ', $classes);
    }

    public function labelClass(): string
    {
        $classes = ['form-label'];

        if ($this->required) {
            $classes[] = 'required';
        }

        return implode(' ', $classes);
    }

    public function inputAttributes(): array
    {
        $attrs = array_merge([
            'type' => $this->type,
            'name' => $this->name,
            'id' => $this->inputId(),
            'value' => $this->value,
            'placeholder' => $this->placeholder,
            'class' => $this->inputClass(),
        ], $this->attributes);

        if ($this->required) {
            $attrs['required'] = 'required';
        }

        if ($this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->readonly) {
            $attrs['readonly'] = 'readonly';
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
        return view('orchid-helpers::components.platform.input-component');
    }
}