<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Search in model with simple text matching
     *
     * @param  Builder  $query
     * @param  string  $searchTerm
     * @param  array  $columns
     * @param  string  $mode
     * @return Builder
     */
    public function searchInModel(Builder $query, string $searchTerm, array $columns = [], string $mode = 'or'): Builder
    {
        if (empty($searchTerm)) {
            return $query;
        }
        
        $searchTerm = $this->sanitizeSearchTerm($searchTerm);
        $words = $this->extractSearchWords($searchTerm);
        
        return $query->where(function ($query) use ($words, $columns, $mode) {
            foreach ($words as $word) {
                if ($mode === 'or') {
                    $query->orWhere(function ($subQuery) use ($word, $columns) {
                        $this->applyWordSearch($subQuery, $word, $columns);
                    });
                } else {
                    $this->applyWordSearch($query, $word, $columns);
                }
            }
        });
    }

    /**
     * Search with advanced filters
     *
     * @param  Builder  $query
     * @param  array  $filters
     * @return Builder
     */
    public function searchWithFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $filter) {
            if (is_array($filter)) {
                $this->applyAdvancedFilter($query, $field, $filter);
            } else {
                $this->applySimpleFilter($query, $field, $filter);
            }
        }
        
        return $query;
    }

    /**
     * Search with full-text search (if supported)
     *
     * @param  Builder  $query
     * @param  string  $searchTerm
     * @param  array  $columns
     * @param  string  $mode
     * @return Builder
     */
    public function fullTextSearch(Builder $query, string $searchTerm, array $columns = [], string $mode = 'natural'): Builder
    {
        if (empty($searchTerm) || empty($columns)) {
            return $query;
        }
        
        $searchTerm = $this->sanitizeSearchTerm($searchTerm);
        
        // Check if full-text search is supported
        if ($this->isFullTextSupported()) {
            $columnsStr = implode(', ', $columns);
            $match = "MATCH({$columnsStr}) AGAINST(? IN {$mode} MODE)";
            
            return $query->whereRaw($match, [$searchTerm])
                        ->orderByRaw($match . ' DESC', [$searchTerm]);
        }
        
        // Fallback to simple search
        return $this->searchInModel($query, $searchTerm, $columns, 'or');
    }

    /**
     * Search with pagination
     *
     * @param  Builder  $query
     * @param  string  $searchTerm
     * @param  array  $columns
     * @param  int  $perPage
     * @param  int  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchPaginated(Builder $query, string $searchTerm, array $columns = [], int $perPage = 15, int $page = 1)
    {
        $query = $this->searchInModel($query, $searchTerm, $columns);
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Search across multiple models
     *
     * @param  array  $models
     * @param  string  $searchTerm
     * @param  array  $modelConfig
     * @param  int  $limitPerModel
     * @return Collection
     */
    public function searchAcrossModels(array $models, string $searchTerm, array $modelConfig = [], int $limitPerModel = 10): Collection
    {
        $results = collect();
        
        foreach ($models as $modelClass) {
            $query = $modelClass::query();
            $config = $modelConfig[$modelClass] ?? [];
            $columns = $config['columns'] ?? [];
            $relations = $config['relations'] ?? [];
            
            // Apply search
            $query = $this->searchInModel($query, $searchTerm, $columns);
            
            // Load relations if specified
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            // Get results
            $modelResults = $query->limit($limitPerModel)->get();
            
            // Add model type to results
            foreach ($modelResults as $result) {
                $result->search_result_type = class_basename($modelClass);
            }
            
            $results = $results->merge($modelResults);
        }
        
        return $results;
    }

    /**
     * Search with autocomplete/suggestions
     *
     * @param  Builder  $query
     * @param  string  $searchTerm
     * @param  string  $column
     * @param  int  $limit
     * @return Collection
     */
    public function autocomplete(Builder $query, string $searchTerm, string $column, int $limit = 10): Collection
    {
        if (empty($searchTerm)) {
            return collect();
        }
        
        $searchTerm = $this->sanitizeSearchTerm($searchTerm);
        
        return $query->select($column)
                    ->where($column, 'LIKE', "{$searchTerm}%")
                    ->distinct()
                    ->orderBy($column)
                    ->limit($limit)
                    ->pluck($column);
    }

    /**
     * Search with date range
     *
     * @param  Builder  $query
     * @param  string  $dateColumn
     * @param  string|null  $startDate
     * @param  string|null  $endDate
     * @param  string  $format
     * @return Builder
     */
    public function searchByDateRange(Builder $query, string $dateColumn, ?string $startDate, ?string $endDate, string $format = 'Y-m-d'): Builder
    {
        if ($startDate) {
            $query->whereDate($dateColumn, '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate($dateColumn, '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Search with numeric range
     *
     * @param  Builder  $query
     * @param  string  $column
     * @param  float|null  $min
     * @param  float|null  $max
     * @return Builder
     */
    public function searchByNumericRange(Builder $query, string $column, ?float $min, ?float $max): Builder
    {
        if ($min !== null) {
            $query->where($column, '>=', $min);
        }
        
        if ($max !== null) {
            $query->where($column, '<=', $max);
        }
        
        return $query;
    }

    /**
     * Search with multiple select values
     *
     * @param  Builder  $query
     * @param  string  $column
     * @param  array  $values
     * @param  string  $operator
     * @return Builder
     */
    public function searchByMultipleValues(Builder $query, string $column, array $values, string $operator = 'in'): Builder
    {
        if (empty($values)) {
            return $query;
        }
        
        if ($operator === 'in') {
            $query->whereIn($column, $values);
        } elseif ($operator === 'not_in') {
            $query->whereNotIn($column, $values);
        }
        
        return $query;
    }

    /**
     * Search with related model
     *
     * @param  Builder  $query
     * @param  string  $relation
     * @param  string  $searchTerm
     * @param  array  $columns
     * @return Builder
     */
    public function searchInRelation(Builder $query, string $relation, string $searchTerm, array $columns = []): Builder
    {
        if (empty($searchTerm)) {
            return $query;
        }
        
        $searchTerm = $this->sanitizeSearchTerm($searchTerm);
        $words = $this->extractSearchWords($searchTerm);
        
        return $query->whereHas($relation, function ($relationQuery) use ($words, $columns) {
            foreach ($words as $word) {
                $relationQuery->where(function ($subQuery) use ($word, $columns) {
                    $this->applyWordSearch($subQuery, $word, $columns);
                });
            }
        });
    }

    /**
     * Get search statistics
     *
     * @param  Builder  $query
     * @param  string  $searchTerm
     * @param  array  $columns
     * @return array
     */
    public function getSearchStatistics(Builder $query, string $searchTerm, array $columns = []): array
    {
        $startTime = microtime(true);
        
        $searchQuery = clone $query;
        $searchQuery = $this->searchInModel($searchQuery, $searchTerm, $columns);
        
        $totalResults = $searchQuery->count();
        $executionTime = microtime(true) - $startTime;
        
        return [
            'search_term' => $searchTerm,
            'total_results' => $totalResults,
            'execution_time' => round($executionTime, 4),
            'columns_searched' => $columns,
            'has_results' => $totalResults > 0,
        ];
    }

    /**
     * Build search index for model
     *
     * @param  string  $modelClass
     * @param  array  $columns
     * @param  string  $indexName
     * @return bool
     */
    public function buildSearchIndex(string $modelClass, array $columns, string $indexName = ''): bool
    {
        if (!$this->isFullTextSupported()) {
            return false;
        }
        
        $table = (new $modelClass)->getTable();
        $indexName = $indexName ?: "{$table}_search_idx";
        $columnsStr = implode(', ', $columns);
        
        try {
            // Drop existing index if exists
            DB::statement("DROP INDEX IF EXISTS {$indexName} ON {$table}");
            
            // Create full-text index
            DB::statement("CREATE FULLTEXT INDEX {$indexName} ON {$table} ({$columnsStr})");
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sanitize search term
     *
     * @param  string  $searchTerm
     * @return string
     */
    private function sanitizeSearchTerm(string $searchTerm): string
    {
        // Remove SQL injection attempts
        $searchTerm = preg_replace('/[\/\\\'\";]/', ' ', $searchTerm);
        
        // Trim and remove extra spaces
        $searchTerm = trim(preg_replace('/\s+/', ' ', $searchTerm));
        
        return $searchTerm;
    }

    /**
     * Extract search words
     *
     * @param  string  $searchTerm
     * @return array
     */
    private function extractSearchWords(string $searchTerm): array
    {
        $words = explode(' ', $searchTerm);
        $words = array_filter($words, function ($word) {
            return strlen($word) > 1;
        });
        
        return array_unique($words);
    }

    /**
     * Apply word search to query
     *
     * @param  Builder  $query
     * @param  string  $word
     * @param  array  $columns
     * @return void
     */
    private function applyWordSearch(Builder $query, string $word, array $columns): void
    {
        if (empty($columns)) {
            // If no columns specified, search in all string columns
            $table = $query->getModel()->getTable();
            $columns = $this->getStringColumns($table);
        }
        
        $query->where(function ($subQuery) use ($word, $columns) {
            foreach ($columns as $column) {
                $subQuery->orWhere($column, 'LIKE', "%{$word}%");
            }
        });
    }

    /**
     * Apply simple filter
     *
     * @param  Builder  $query
     * @param  string  $field
     * @param  mixed  $value
     * @return void
     */
    private function applySimpleFilter(Builder $query, string $field, $value): void
    {
        if ($value === null || $value === '') {
            return;
        }
        
        if (is_array($value)) {
            $query->whereIn($field, $value);
        } else {
            $query->where($field, $value);
        }
    }

    /**
     * Apply advanced filter
     *
     * @param  Builder  $query
     * @param  string  $field
     * @param  array  $filter
     * @return void
     */
    private function applyAdvancedFilter(Builder $query, string $field, array $filter): void
    {
        $operator = $filter['operator'] ?? '=';
        $value = $filter['value'] ?? null;
        
        if ($value === null || $value === '') {
            return;
        }
        
        switch ($operator) {
            case 'like':
                $query->where($field, 'LIKE', "%{$value}%");
                break;
            case 'not_like':
                $query->where($field, 'NOT LIKE', "%{$value}%");
                break;
            case 'starts_with':
                $query->where($field, 'LIKE', "{$value}%");
                break;
            case 'ends_with':
                $query->where($field, 'LIKE', "%{$value}");
                break;
            case 'in':
                $query->whereIn($field, (array) $value);
                break;
            case 'not_in':
                $query->whereNotIn($field, (array) $value);
                break;
            case 'between':
                $query->whereBetween($field, (array) $value);
                break;
            case 'not_between':
                $query->whereNotBetween($field, (array) $value);
                break;
            case 'null':
                $query->whereNull($field);
                break;
            case 'not_null':
                $query->whereNotNull($field);
                break;
            default:
                $query->where($field, $operator, $value);
        }
    }

    /**
     * Get string columns from table
     *
     * @param  string  $table
     * @return array
     */
    private function getStringColumns(string $table): array
    {
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing($table);
            $stringColumns = [];
            
            foreach ($columns as $column) {
                $type = DB::getSchemaBuilder()->getColumnType($table, $column);
                if (in_array($type, ['string', 'text', 'varchar', 'char'])) {
                    $stringColumns[] = $column;
                }
            }
            
            return $stringColumns;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if full-text search is supported
     *
     * @return bool
     */
    private function isFullTextSupported(): bool
    {
        try {
            $result = DB::select("SHOW ENGINES");
            
            foreach ($result as $engine) {
                if (strtolower($engine->Engine) === 'innodb' && strtolower($engine->Support) !== 'no') {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available search operators
     *
     * @return array
     */
    public function getAvailableOperators(): array
    {
        return [
            '=', '!=', '<', '>', '<=', '>=',
            'like', 'not_like', 'starts_with', 'ends_with',
            'in', 'not_in', 'between', 'not_between',
            'null', 'not_null',
        ];
    }

    /**
     * Get search modes
     *
     * @return array
     */
    public function getSearchModes(): array
    {
        return [
            'simple' => 'Simple text search',
            'advanced' => 'Advanced filters',
            'fulltext' => 'Full-text search',
            'autocomplete' => 'Autocomplete',
        ];
    }
}
