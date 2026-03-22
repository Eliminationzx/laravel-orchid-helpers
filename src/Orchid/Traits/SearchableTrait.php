<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait SearchableTrait
{
    /**
     * Apply search to the query builder.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $searchableColumns
     * @return Builder
     */
    public function scopeSearch(Builder $query, Request $request, array $searchableColumns = []): Builder
    {
        $searchTerm = $request->input('search', '');
        
        if (empty($searchTerm)) {
            return $query;
        }

        $searchableColumns = empty($searchableColumns) ? $this->getSearchableColumns() : $searchableColumns;

        return $query->where(function (Builder $query) use ($searchTerm, $searchableColumns) {
            foreach ($searchableColumns as $column => $searchType) {
                if (is_numeric($column)) {
                    // If numeric key, use the value as column name with default search type
                    $column = $searchType;
                    $searchType = 'contains';
                }

                $this->applySearch($query, $column, $searchTerm, $searchType);
            }
        });
    }

    /**
     * Get the searchable columns for the model.
     *
     * @return array
     */
    protected function getSearchableColumns(): array
    {
        if (property_exists($this, 'searchable')) {
            return $this->searchable;
        }

        // Default to all string-based columns
        return $this->getFillable();
    }

    /**
     * Apply a specific search type to the query.
     *
     * @param Builder $query
     * @param string $column
     * @param string $searchTerm
     * @param string $searchType
     * @return void
     */
    protected function applySearch(Builder $query, string $column, string $searchTerm, string $searchType): void
    {
        switch ($searchType) {
            case 'contains':
                $query->orWhere($column, 'LIKE', "%{$searchTerm}%");
                break;
            case 'starts_with':
                $query->orWhere($column, 'LIKE', "{$searchTerm}%");
                break;
            case 'ends_with':
                $query->orWhere($column, 'LIKE', "%{$searchTerm}");
                break;
            case 'exact':
                $query->orWhere($column, $searchTerm);
                break;
            case 'exact_case_insensitive':
                $query->orWhereRaw("LOWER({$column}) = LOWER(?)", [$searchTerm]);
                break;
            case 'soundex':
                if (config('database.default') === 'mysql') {
                    $query->orWhereRaw("SOUNDEX({$column}) = SOUNDEX(?)", [$searchTerm]);
                }
                break;
            case 'fulltext':
                if (config('database.default') === 'mysql') {
                    $query->orWhereRaw("MATCH({$column}) AGAINST(? IN BOOLEAN MODE)", [$searchTerm]);
                }
                break;
            case 'regex':
                if (config('database.default') === 'mysql') {
                    $query->orWhereRaw("{$column} REGEXP ?", [$searchTerm]);
                } elseif (config('database.default') === 'pgsql') {
                    $query->orWhereRaw("{$column} ~ ?", [$searchTerm]);
                }
                break;
            case 'numeric_range':
                if (is_numeric($searchTerm)) {
                    $range = floatval($searchTerm) * 0.1; // 10% range
                    $min = $searchTerm - $range;
                    $max = $searchTerm + $range;
                    $query->orWhereBetween($column, [$min, $max]);
                }
                break;
            case 'date':
                try {
                    $date = \Carbon\Carbon::parse($searchTerm);
                    $query->orWhereDate($column, $date);
                } catch (\Exception $e) {
                    // Ignore invalid dates
                }
                break;
            case 'boolean':
                $boolValue = filter_var($searchTerm, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($boolValue !== null) {
                    $query->orWhere($column, $boolValue);
                }
                break;
            default:
                // Custom search callback
                if (is_callable($searchType)) {
                    $searchType($query, $column, $searchTerm);
                } else {
                    // Default to contains
                    $query->orWhere($column, 'LIKE', "%{$searchTerm}%");
                }
                break;
        }
    }

    /**
     * Apply advanced search with multiple criteria.
     *
     * @param Builder $query
     * @param array $searchCriteria
     * @return Builder
     */
    public function scopeAdvancedSearch(Builder $query, array $searchCriteria): Builder
    {
        return $query->where(function (Builder $query) use ($searchCriteria) {
            foreach ($searchCriteria as $column => $criteria) {
                if (is_array($criteria)) {
                    $value = $criteria['value'] ?? '';
                    $operator = $criteria['operator'] ?? '=';
                    $type = $criteria['type'] ?? 'text';
                    
                    $this->applyAdvancedSearch($query, $column, $value, $operator, $type);
                } else {
                    $this->applyAdvancedSearch($query, $column, $criteria);
                }
            }
        });
    }

    /**
     * Apply advanced search criteria.
     *
     * @param Builder $query
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @param string $type
     * @return void
     */
    protected function applyAdvancedSearch(Builder $query, string $column, $value, string $operator = '=', string $type = 'text'): void
    {
        if (empty($value) && $value !== '0' && $value !== 0 && $value !== false) {
            return;
        }

        switch ($type) {
            case 'text':
                if ($operator === 'like') {
                    $query->where($column, 'LIKE', "%{$value}%");
                } else {
                    $query->where($column, $operator, $value);
                }
                break;
            case 'number':
                $query->where($column, $operator, floatval($value));
                break;
            case 'date':
                try {
                    $date = \Carbon\Carbon::parse($value);
                    if ($operator === 'between' && is_array($value)) {
                        $query->whereBetween($column, $value);
                    } else {
                        $query->whereDate($column, $operator, $date);
                    }
                } catch (\Exception $e) {
                    // Ignore invalid dates
                }
                break;
            case 'boolean':
                $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($boolValue !== null) {
                    $query->where($column, $operator, $boolValue);
                }
                break;
            case 'array':
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->whereIn($column, explode(',', $value));
                }
                break;
            case 'null':
                if ($value) {
                    $query->whereNull($column);
                } else {
                    $query->whereNotNull($column);
                }
                break;
        }
    }

    /**
     * Get search options for UI components.
     *
     * @return array
     */
    public static function getSearchOptions(): array
    {
        $instance = new static();
        $searchableColumns = $instance->getSearchableColumns();
        $options = [];

        foreach ($searchableColumns as $column => $searchType) {
            if (is_numeric($column)) {
                $column = $searchType;
                $searchType = 'contains';
            }

            $options[$column] = [
                'type' => $searchType,
                'label' => str_replace('_', ' ', ucfirst($column)),
                'placeholder' => 'Search ' . str_replace('_', ' ', $column),
            ];
        }

        return $options;
    }

    /**
     * Perform fuzzy search using Levenshtein distance (for small datasets).
     *
     * @param Builder $query
     * @param string $searchTerm
     * @param array $columns
     * @param int $threshold
     * @return Builder
     */
    public function scopeFuzzySearch(Builder $query, string $searchTerm, array $columns = [], int $threshold = 3): Builder
    {
        $columns = empty($columns) ? $this->getSearchableColumns() : $columns;
        
        // This is a simplified implementation - for production, consider using
        // database-specific fuzzy search extensions or external search engines
        return $query->where(function (Builder $query) use ($searchTerm, $columns, $threshold) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$searchTerm}%");
            }
        });
    }

    /**
     * Get search suggestions for autocomplete.
     *
     * @param string $searchTerm
     * @param array $columns
     * @param int $limit
     * @return array
     */
    public static function getSearchSuggestions(string $searchTerm, array $columns = [], int $limit = 10): array
    {
        $instance = new static();
        $columns = empty($columns) ? $instance->getSearchableColumns() : $columns;
        
        $query = $instance->newQuery();
        
        foreach ($columns as $column) {
            $query->orWhere($column, 'LIKE', "{$searchTerm}%");
        }
        
        return $query->limit($limit)->pluck($columns[0] ?? 'id')->toArray();
    }

    /**
     * Highlight search terms in text.
     *
     * @param string $text
     * @param string $searchTerm
     * @param string $highlightTag
     * @return string
     */
    public static function highlightSearchTerm(string $text, string $searchTerm, string $highlightTag = 'mark'): string
    {
        if (empty($searchTerm) || empty($text)) {
            return $text;
        }

        $pattern = '/' . preg_quote($searchTerm, '/') . '/i';
        return preg_replace($pattern, "<{$highlightTag}>$0</{$highlightTag}>", $text);
    }
}
