<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class NumberRangeFilter extends Filter
{
    public function __construct(
        readonly protected string $field,
        readonly protected ?float $min = null,
        readonly protected ?float $max = null,
        readonly protected ?float $step = null,
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
            "{$this->field}_min",
            "{$this->field}_max",
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
        $min = $this->request->input("{$this->field}_min");
        $max = $this->request->input("{$this->field}_max");

        return $builder
            ->when($min !== null, fn(Builder $builder) => $builder->where($this->field, '>=', $min))
            ->when($max !== null, fn(Builder $builder) => $builder->where($this->field, '<=', $max));
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display() : iterable
    {
        $minField = Input::make("{$this->field}_min")
            ->type('number')
            ->title($this->name() . ' (from)')
            ->value($this->request->input("{$this->field}_min"));

        $maxField = Input::make("{$this->field}_max")
            ->type('number')
            ->title($this->name() . ' (to)')
            ->value($this->request->input("{$this->field}_max"));

        if ($this->min !== null) {
            $minField = $minField->min($this->min);
            $maxField = $maxField->min($this->min);
        }

        if ($this->max !== null) {
            $minField = $minField->max($this->max);
            $maxField = $maxField->max($this->max);
        }

        if ($this->step !== null) {
            $minField = $minField->step($this->step);
            $maxField = $maxField->step($this->step);
        }

        return [$minField, $maxField];
    }
}