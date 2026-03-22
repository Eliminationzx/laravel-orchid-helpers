<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait SortableTrait
{
    /**
     * Apply sorting to the query builder.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $sortableColumns
     * @return Builder
     */
    public function scopeSort(Builder $query, Request $request, array $sortableColumns = []): Builder
    {
        $sortableColumns = empty($sortableColumns) ? $this->getSortableColumns() : $sortableColumns;

        $sortColumn = $request->input('sort', $this->getDefaultSortColumn());
        $sortDirection = $request->input('direction', $this->getDefaultSortDirection());

        // Validate sort column
        if (!in_array($sortColumn, $sortableColumns) && !array_key_exists($sortColumn, $sortableColumns)) {
            $sortColumn = $this->getDefaultSortColumn();
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = $this->getDefaultSortDirection();
        }

        // Apply sorting
        if (array_key_exists($sortColumn, $sortableColumns)) {
            // Custom sorting callback
            $sortCallback = $sortableColumns[$sortColumn];
            if (is_callable($sortCallback)) {
                $sortCallback($query, $sortDirection);
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        return $query;
    }

    /**
     * Get the sortable columns for the model.
     *
     * @return array
     */
    protected function getSortableColumns(): array
    {
        if (property_exists($this, 'sortable')) {
            return $this->sortable;
        }

        // Default to all fillable columns plus timestamps
        $columns = array_merge(
            $this->getFillable(),
            ['id', 'created_at', 'updated_at']
        );

        return array_fill_keys($columns, true);
    }

    /**
     * Get the default sort column.
     *
     * @return string
     */
    protected function getDefaultSortColumn(): string
    {
        if (property_exists($this, 'defaultSortColumn')) {
            return $this->defaultSortColumn;
        }

        return 'id';
    }

    /**
     * Get the default sort direction.
     *
     * @return string
     */
    protected function getDefaultSortDirection(): string
    {
        if (property_exists($this, 'defaultSortDirection')) {
            return $this->defaultSortDirection;
        }

        return 'desc';
    }

    /**
     * Apply multiple sorting criteria.
     *
     * @param Builder $query
     * @param array $sortCriteria
     * @return Builder
     */
    public function scopeMultiSort(Builder $query, array $sortCriteria): Builder
    {
        foreach ($sortCriteria as $column => $direction) {
            if (in_array(strtolower($direction), ['asc', 'desc'])) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * Get sorting options for UI components.
     *
     * @return array
     */
    public static function getSortOptions(): array
    {
        $instance = new static();
        $sortableColumns = $instance->getSortableColumns();
        $options = [];

        foreach ($sortableColumns as $column => $callback) {
            if (is_numeric($column)) {
                $column = $callback;
                $callback = null;
            }

            $options[$column] = [
                'label' => str_replace('_', ' ', ucfirst($column)),
                'default_direction' => $instance->getDefaultSortDirectionForColumn($column),
            ];
        }

        return $options;
    }

    /**
     * Get default sort direction for a specific column.
     *
     * @param string $column
     * @return string
     */
    protected function getDefaultSortDirectionForColumn(string $column): string
    {
        $defaultDirections = property_exists($this, 'columnSortDirections') 
            ? $this->columnSortDirections 
            : [];

        return $defaultDirections[$column] ?? $this->getDefaultSortDirection();
    }

    /**
     * Apply natural sorting for alphanumeric columns.
     *
     * @param Builder $query
     * @param string $column
     * @param string $direction
     * @return Builder
     */
    public function scopeNaturalSort(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        // For MySQL, use ORDER BY column + 0 for natural sorting
        if (config('database.default') === 'mysql') {
            return $query->orderByRaw("{$column} + 0 {$direction}");
        }

        // For PostgreSQL, use ORDER BY column::bytea
        if (config('database.default') === 'pgsql') {
            return $query->orderByRaw("{$column}::bytea {$direction}");
        }

        // Fallback to regular sorting
        return $query->orderBy($column, $direction);
    }

    /**
     * Apply case-insensitive sorting.
     *
     * @param Builder $query
     * @param string $column
     * @param string $direction
     * @return Builder
     */
    public function scopeCaseInsensitiveSort(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        return $query->orderByRaw("LOWER({$column}) {$direction}");
    }

    /**
     * Toggle sort direction for a column.
     *
     * @param string $currentColumn
     * @param string $currentDirection
     * @param string $newColumn
     * @return array
     */
    public static function toggleSort(string $currentColumn, string $currentDirection, string $newColumn): array
    {
        if ($currentColumn === $newColumn) {
            // Toggle direction if same column
            $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // New column, use default direction
            $instance = new static();
            $newDirection = $instance->getDefaultSortDirectionForColumn($newColumn);
        }

        return [
            'column' => $newColumn,
            'direction' => $newDirection,
        ];
    }
}
