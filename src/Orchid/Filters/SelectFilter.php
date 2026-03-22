<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;

class SelectFilter extends Filter
{
    public function __construct(
        readonly private string $field,
        readonly private array $options = [],
        readonly private bool $multiple = false,
        readonly private ?string $placeholder = null,
    )
    {
        parent::__construct();
    }

    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name() : string
    {
        return attrName($this->field);
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters() : ?array
    {
        return [
            $this->field,
        ];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param  Builder  $builder
     *
     * @return Builder
     */
    public function run(Builder $builder) : Builder
    {
        $value = $this->request->input($this->field);

        if ($value === null || $value === '') {
            return $builder;
        }

        if ($this->multiple) {
            return $builder->whereIn($this->field, (array) $value);
        }

        return $builder->where($this->field, $value);
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display() : iterable
    {
        $select = Select::make($this->field)
            ->options($this->options)
            ->multiple($this->multiple)
            ->title($this->name());

        if ($this->placeholder !== null) {
            $select = $select->placeholder($this->placeholder);
        }

        return [$select];
    }
}
