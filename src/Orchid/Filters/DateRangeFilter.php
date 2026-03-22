<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\DateRange;

class DateRangeFilter extends Filter
{
    public function __construct(
        readonly protected string $field,
        readonly protected ?string $format = null,
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
        $field = $this->field;
        $start = $this->request->input("$field.start");
        $end   = $this->request->input("$field.end");

        if ($start === null && $end === null) {
            return $builder;
        }

        if ($start !== null && $end === null) {
            return $builder->whereDate($field, '>=', $start);
        }

        if ($start === null && $end !== null) {
            return $builder->whereDate($field, '<=', $end);
        }

        // Both dates provided
        if ($start === $end) {
            return $builder->whereDate($field, $start);
        }

        return $builder->whereBetween($field, [$start, $end]);
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display() : iterable
    {
        $dateRange = DateRange::make($this->field)
            ->title($this->name());

        if ($this->format !== null) {
            $dateRange = $dateRange->format($this->format);
        }

        return [$dateRange];
    }
}