<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class SearchFilter extends Filter
{
    public function __construct(
        readonly protected string $field,
        readonly protected bool $exactMatch = false,
        readonly protected ?string $placeholder = null,
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

        if ($value === null || trim($value) === '') {
            return $builder;
        }

        $value = trim($value);

        if ($this->exactMatch) {
            return $builder->where($this->field, $value);
        }

        return $builder->where($this->field, 'LIKE', "%{$value}%");
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display() : iterable
    {
        $input = Input::make($this->field)
            ->type('text')
            ->title($this->name())
            ->value($this->request->input($this->field));

        if ($this->placeholder !== null) {
            $input = $input->placeholder($this->placeholder);
        }

        return [$input];
    }
}
