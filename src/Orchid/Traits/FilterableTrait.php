<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait FilterableTrait
{
    /**
     * Apply filters to the query builder.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $filterableColumns
     * @return Builder
     */
    public function scopeFilter(Builder $query, Request $request, array $filterableColumns = []): Builder
    {
        $filterableColumns = empty($filterableColumns) ? $this->getFilterableColumns() : $filterableColumns;

        foreach ($filterableColumns as $column => $filterType) {
            if (is_numeric($column)) {
                // If numeric key, use the value as column name with default filter type
                $column = $filterType;
                $filterType = 'equals';
            }

            if ($request->filled($column)) {
                $value = $request->input($column);
                $this->applyFilter($query, $column, $value, $filterType);
            }
        }

        return $query;
    }

    /**
     * Get the filterable columns for the model.
     *
     * @return array
     */
    protected function getFilterableColumns(): array
    {
        if (property_exists($this, 'filterable')) {
            return $this->filterable;
        }

        return [];
    }

    /**
     * Apply a specific filter to the query.
     *
     * @param Builder $query
     * @param string $column
     * @param mixed $value
     * @param string $filterType
     * @return void
     */
    protected function applyFilter(Builder $query, string $column, $value, string $filterType): void
    {
        switch ($filterType) {
            case 'equals':
                $query->where($column, $value);
                break;
            case 'not_equals':
                $query->where($column, '!=', $value);
                break;
            case 'contains':
                $query->where($column, 'LIKE', "%{$value}%");
                break;
            case 'starts_with':
                $query->where($column, 'LIKE', "{$value}%");
                break;
            case 'ends_with':
                $query->where($column, 'LIKE', "%{$value}");
                break;
            case 'greater_than':
                $query->where($column, '>', $value);
                break;
            case 'greater_than_or_equal':
                $query->where($column, '>=', $value);
                break;
            case 'less_than':
                $query->where($column, '<', $value);
                break;
            case 'less_than_or_equal':
                $query->where($column, '<=', $value);
                break;
            case 'between':
                if (is_array($value) && count($value) === 2) {
                    $query->whereBetween($column, $value);
                }
                break;
            case 'in':
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->whereIn($column, explode(',', $value));
                }
                break;
            case 'not_in':
                if (is_array($value)) {
                    $query->whereNotIn($column, $value);
                } else {
                    $query->whereNotIn($column, explode(',', $value));
                }
                break;
            case 'null':
                $query->whereNull($column);
                break;
            case 'not_null':
                $query->whereNotNull($column);
                break;
            case 'date':
                $query->whereDate($column, $value);
                break;
            case 'month':
                $query->whereMonth($column, $value);
                break;
            case 'year':
                $query->whereYear($column, $value);
                break;
            case 'day':
                $query->whereDay($column, $value);
                break;
            default:
                // Custom filter callback
                if (is_callable($filterType)) {
                    $filterType($query, $column, $value);
                }
                break;
        }
    }

    /**
     * Get filter options for UI components.
     *
     * @return array
     */
    public static function getFilterOptions(): array
    {
        $instance = new static();
        $filterableColumns = $instance->getFilterableColumns();
        $options = [];

        foreach ($filterableColumns as $column => $filterType) {
            if (is_numeric($column)) {
                $column = $filterType;
                $filterType = 'equals';
            }

            $options[$column] = [
                'type' => $filterType,
                'label' => str_replace('_', ' ', ucfirst($column)),
                'placeholder' => 'Filter by ' . str_replace('_', ' ', $column),
            ];
        }

        return $options;
    }

    /**
     * Apply quick search across multiple columns.
     *
     * @param Builder $query
     * @param string $searchTerm
     * @param array $searchColumns
     * @return Builder
     */
    public function scopeQuickSearch(Builder $query, string $searchTerm, array $searchColumns = []): Builder
    {
        if (empty($searchTerm)) {
            return $query;
        }

        $searchColumns = empty($searchColumns) ? $this->getSearchableColumns() : $searchColumns;

        return $query->where(function (Builder $query) use ($searchTerm, $searchColumns) {
            foreach ($searchColumns as $column) {
                $query->orWhere($column, 'LIKE', "%{$searchTerm}%");
            }
        });
    }

    /**
     * Get searchable columns for the model.
     *
     * @return array
     */
    protected function getSearchableColumns(): array
    {
        if (property_exists($this, 'searchable')) {
            return $this->searchable;
        }

        return [];
    }
}