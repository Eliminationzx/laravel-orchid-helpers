<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Screen\Layout;
use OrchidHelpers\Orchid\Helpers\Layouts\ModelsTableLayout;

abstract class ListScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function model(): string;

    /**
     * Get the base query builder for the list.
     */
    protected function baseQuery(): Builder
    {
        $modelClass = $this->model();

        return $modelClass::query();
    }

    /**
     * Get the table columns for the list.
     */
    abstract protected function columns(): array;

    /**
     * Get the filters for the list.
     */
    protected function filters(): array
    {
        return [];
    }

    /**
     * Get the actions for the list.
     */
    protected function actions(): array
    {
        return [];
    }

    /**
     * Get the command bar for the list.
     */
    public function commandBar(): iterable
    {
        return $this->actions();
    }

    /**
     * Get the layouts for the list.
     */
    public function layout(): iterable
    {
        $layouts = [];

        if (!empty($this->filters())) {
            // In Orchid, filters are typically added to the screen via the filters() method
            // The Layout::selection() method might not exist, so we'll use a different approach
            // We'll add filters through the screen's filter method instead
        }

        $layouts[] = ModelsTableLayout::make($this->columns());

        return $layouts;
    }

    /**
     * Query data for the list screen.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function query(): array
    {
        $this->authorizeList($this->model());

        $query = $this->buildQuery();

        $models = $query->paginate($this->perPage());

        return [
            'models' => $models,
        ];
    }

    /**
     * Build the query with filters applied.
     */
    protected function buildQuery(): Builder
    {
        $query = $this->baseQuery();

        // Apply filters if any
        foreach ($this->filters() as $filter) {
            if ($filter instanceof \Orchid\Filters\Filter) {
                $query = $filter->filter($query);
            }
        }

        return $query;
    }

    /**
     * Get the number of items per page.
     */
    protected function perPage(): int
    {
        return request()->input('perPage', 25);
    }
}