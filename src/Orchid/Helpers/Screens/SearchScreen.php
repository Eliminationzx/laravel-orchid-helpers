<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use OrchidHelpers\Orchid\Helpers\Layouts\FormLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\ModelsTableLayout;

abstract class SearchScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get the search form fields.
     */
    abstract protected function searchFields(): array;

    /**
     * Get the table columns for search results.
     */
    abstract protected function resultColumns(): array;

    /**
     * Get the base query for search.
     */
    protected function baseQuery(): Builder
    {
        $modelClass = $this->modelClass();

        return $modelClass::query();
    }

    /**
     * Apply search filters to the query.
     */
    protected function applySearchFilters(Builder $query, Request $request): Builder
    {
        $searchFields = $this->searchFields();
        
        foreach ($searchFields as $field => $config) {
            if ($request->filled($field)) {
                $value = $request->input($field);
                $this->applyFieldFilter($query, $field, $value, $config);
            }
        }

        return $query;
    }

    /**
     * Apply filter for a specific field.
     */
    protected function applyFieldFilter(Builder $query, string $field, $value, array $config): void
    {
        $type = $config['type'] ?? 'text';
        $operator = $config['operator'] ?? '=';

        switch ($type) {
            case 'text':
                if ($operator === 'like') {
                    $query->where($field, 'LIKE', "%{$value}%");
                } else {
                    $query->where($field, $operator, $value);
                }
                break;
            case 'date':
            case 'datetime':
                if (is_array($value) && isset($value['start']) && isset($value['end'])) {
                    $query->whereBetween($field, [$value['start'], $value['end']]);
                } else {
                    $query->whereDate($field, $operator, $value);
                }
                break;
            case 'number':
                if (is_array($value) && isset($value['min']) && isset($value['max'])) {
                    $query->whereBetween($field, [$value['min'], $value['max']]);
                } else {
                    $query->where($field, $operator, $value);
                }
                break;
            case 'boolean':
                $query->where($field, (bool) $value);
                break;
            case 'select':
            case 'multiselect':
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
                break;
        }
    }

    /**
     * Get search results.
     */
    protected function getSearchResults(Request $request): LengthAwarePaginator
    {
        $query = $this->baseQuery();
        $query = $this->applySearchFilters($query, $request);
        
        // Apply sorting
        if ($request->filled('sort_by')) {
            $direction = $request->input('sort_direction', 'asc');
            $query->orderBy($request->input('sort_by'), $direction);
        }
        
        // Apply pagination
        $perPage = $request->input('per_page', 25);
        
        return $query->paginate($perPage);
    }

    /**
     * Get the screen data for the search screen.
     */
    public function query(Request $request): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('viewAny', $model);
        
        $results = collect();
        
        // Only perform search if there are search parameters
        if ($this->hasSearchParameters($request)) {
            $results = $this->getSearchResults($request);
        }
        
        return [
            'results' => $results,
            'searchParams' => $request->all(),
        ];
    }

    /**
     * Check if request has search parameters.
     */
    protected function hasSearchParameters(Request $request): bool
    {
        $searchFields = array_keys($this->searchFields());
        
        foreach ($searchFields as $field) {
            if ($request->filled($field)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the layouts for the search screen.
     */
    public function layout(): iterable
    {
        return [
            FormLayout::make($this->getSearchFormFields()),
            ModelsTableLayout::make($this->resultColumns()),
        ];
    }

    /**
     * Get search form fields with additional controls.
     */
    protected function getSearchFormFields(): array
    {
        $fields = $this->searchFields();
        
        // Add search button
        $fields[] = \Orchid\Screen\Actions\Button::make('Search')
            ->method('search')
            ->icon('magnifier')
            ->class('btn btn-primary');
        
        // Add reset button
        $fields[] = \Orchid\Screen\Actions\Button::make('Reset')
            ->method('reset')
            ->icon('refresh')
            ->class('btn btn-secondary');
        
        return $fields;
    }

    /**
     * Get the command bar for the search screen.
     */
    public function commandBar(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('search')
                ->label('Search'),
        ];
    }

    /**
     * Handle search request.
     */
    public function search(Request $request): array
    {
        // This method is called when the search form is submitted
        // The query() method will handle the actual search logic
        return $this->query($request);
    }

    /**
     * Handle reset request.
     */
    public function reset(): RedirectResponse
    {
        return redirect()->route(request()->route()->getName());
    }

    /**
     * Export search results.
     */
    public function export(Request $request)
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('viewAny', $model);
        
        $results = $this->getSearchResults($request);
        
        // Export logic would be implemented here
        // This could use the ExportScreen functionality
        
        return response()->streamDownload(function () use ($results) {
            // Export implementation
        }, 'search-results.csv');
    }

    /**
     * Get advanced search tips.
     */
    protected function getSearchTips(): array
    {
        return [
            'Use * as wildcard for text searches',
            'For date ranges, use the date range picker',
            'Multiple selection fields support selecting multiple values',
            'Leave fields empty to ignore them in search',
        ];
    }
}