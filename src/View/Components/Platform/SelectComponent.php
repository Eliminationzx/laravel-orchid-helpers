<?php

declare(strict_types=1);

namespace OrchidHelpers\View\Components\Platform;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class SelectComponent extends Component
{
    private readonly mixed $value;

    public function __construct(
        readonly public Repository|Model|string|null $target = null,
        readonly public ?string $name = null,
        readonly public array|Collection $options = [],
        readonly public ?string $label = null,
        readonly public ?string $placeholder = null,
        readonly public bool $required = false,
        readonly public bool $disabled = false,
        readonly public bool $multiple = false,
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

    public function selectId(): string
    {
        return $this->id ?? $this->name ?? uniqid('select_');
    }

    public function selectClass(): string
    {
        $classes = ['form-select'];

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

    public function selectAttributes(): array
    {
        $attrs = array_merge([
            'name' => $this->name . ($this->multiple ? '[]' : ''),
            'id' => $this->selectId(),
            'class' => $this->selectClass(),
        ], $this->attributes);

        if ($this->required) {
            $attrs['required'] = 'required';
        }

        if ($this->disabled) {
            $attrs['disabled'] = 'disabled';
        }

        if ($this->multiple) {
            $attrs['multiple'] = 'multiple';
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

    public function hasPlaceholder(): bool
    {
        return !empty($this->placeholder);
    }

    public function isSelected($optionValue): bool
    {
        if ($this->multiple && is_array($this->value)) {
            return in_array($optionValue, $this->value);
        }

        return (string) $optionValue === (string) $this->value;
    }

    public function getOptions(): array
    {
        if ($this->options instanceof Collection) {
            return $this->options->toArray();
        }

        return $this->options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('orchid-helpers::components.platform.select-component');
    }
}