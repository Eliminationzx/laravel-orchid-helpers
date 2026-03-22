<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use OrchidHelpers\Orchid\Helpers\Layouts\DashboardLayout;
use OrchidHelpers\Orchid\Helpers\Layouts\FormLayout;

abstract class ReportScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get report metrics (counts, sums, averages, etc.).
     */
    abstract protected function metrics(): array;

    /**
     * Get report charts data.
     */
    abstract protected function charts(): array;

    /**
     * Get report tables data.
     */
    abstract protected function tables(): array;

    /**
     * Get report filters form fields.
     */
    protected function filters(): array
    {
        return [
            \Orchid\Screen\Fields\DateRange::make('date_range')
                ->title('Date Range')
                ->required()
                ->value([
                    'start' => now()->subMonth()->format('Y-m-d'),
                    'end' => now()->format('Y-m-d'),
                ])
                ->help('Select date range for the report'),
            
            \Orchid\Screen\Fields\Select::make('group_by')
                ->title('Group By')
                ->options([
                    'day' => 'Day',
                    'week' => 'Week',
                    'month' => 'Month',
                    'year' => 'Year',
                ])
                ->value('month')
                ->help('Select grouping for time-based reports'),
        ];
    }

    /**
     * Get the base query for reports.
     */
    protected function baseQuery(): Builder
    {
        $modelClass = $this->modelClass();

        return $modelClass::query();
    }

    /**
     * Apply report filters to the query.
     */
    protected function applyFilters(Builder $query, Request $request): Builder
    {
        // Apply date range filter
        if ($request->filled('date_range.start') && $request->filled('date_range.end')) {
            $query->whereBetween('created_at', [
                $request->input('date_range.start'),
                $request->input('date_range.end'),
            ]);
        }

        // Apply additional filters
        $this->applyCustomFilters($query, $request);

        return $query;
    }

    /**
     * Apply custom filters (to be overridden by subclasses).
     */
    protected function applyCustomFilters(Builder $query, Request $request): void
    {
        // Override in subclasses to apply custom filters
    }

    /**
     * Calculate metrics based on filtered data.
     */
    protected function calculateMetrics(Builder $query, Request $request): array
    {
        $metrics = [];
        
        foreach ($this->metrics() as $key => $config) {
            $metrics[$key] = $this->calculateMetric($query, $config, $request);
        }
        
        return $metrics;
    }

    /**
     * Calculate a single metric.
     */
    protected function calculateMetric(Builder $query, array $config, Request $request)
    {
        $type = $config['type'] ?? 'count';
        $field = $config['field'] ?? '*';
        
        $metricQuery = clone $query;
        
        switch ($type) {
            case 'count':
                return $metricQuery->count();
            case 'sum':
                return $metricQuery->sum($field);
            case 'avg':
                return $metricQuery->avg($field);
            case 'min':
                return $metricQuery->min($field);
            case 'max':
                return $metricQuery->max($field);
            case 'distinct':
                return $metricQuery->distinct()->count($field);
            default:
                return 0;
        }
    }

    /**
     * Generate charts data.
     */
    protected function generateCharts(Builder $query, Request $request): array
    {
        $charts = [];
        
        foreach ($this->charts() as $key => $config) {
            $charts[$key] = $this->generateChart($query, $config, $request);
        }
        
        return $charts;
    }

    /**
     * Generate a single chart.
     */
    protected function generateChart(Builder $query, array $config, Request $request): array
    {
        $type = $config['type'] ?? 'line';
        $xField = $config['x_field'] ?? 'created_at';
        $yField = $config['y_field'] ?? 'id';
        $aggregation = $config['aggregation'] ?? 'count';
        $groupBy = $request->input('group_by', 'month');
        
        $chartQuery = clone $query;
        
        // Group by time period
        switch ($groupBy) {
            case 'day':
                $chartQuery->selectRaw("DATE({$xField}) as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("DATE({$xField})")
                    ->orderBy('period');
                break;
            case 'week':
                $chartQuery->selectRaw("YEARWEEK({$xField}) as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("YEARWEEK({$xField})")
                    ->orderBy('period');
                break;
            case 'month':
                $chartQuery->selectRaw("DATE_FORMAT({$xField}, '%Y-%m') as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("DATE_FORMAT({$xField}, '%Y-%m')")
                    ->orderBy('period');
                break;
            case 'year':
                $chartQuery->selectRaw("YEAR({$xField}) as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("YEAR({$xField})")
                    ->orderBy('period');
                break;
        }
        
        $data = $chartQuery->get();
        
        return [
            'type' => $type,
            'labels' => $data->pluck('period')->toArray(),
            'data' => $data->pluck('value')->toArray(),
            'config' => $config,
        ];
    }

    /**
     * Generate tables data.
     */
    protected function generateTables(Builder $query, Request $request): array
    {
        $tables = [];
        
        foreach ($this->tables() as $key => $config) {
            $tables[$key] = $this->generateTable($query, $config, $request);
        }
        
        return $tables;
    }

    /**
     * Generate a single table.
     */
    protected function generateTable(Builder $query, array $config, Request $request): array
    {
        $columns = $config['columns'] ?? [];
        $limit = $config['limit'] ?? 10;
        $orderBy = $config['order_by'] ?? 'created_at';
        $orderDirection = $config['order_direction'] ?? 'desc';
        
        $tableQuery = clone $query;
        
        $data = $tableQuery->orderBy($orderBy, $orderDirection)
            ->limit($limit)
            ->get();
        
        return [
            'columns' => $columns,
            'data' => $data,
            'config' => $config,
        ];
    }

    /**
     * Get the screen data for the report screen.
     */
    public function query(Request $request): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('viewAny', $model);
        
        $baseQuery = $this->baseQuery();
        $filteredQuery = $this->applyFilters($baseQuery, $request);
        
        $metrics = $this->calculateMetrics($filteredQuery, $request);
        $charts = $this->generateCharts($filteredQuery, $request);
        $tables = $this->generateTables($filteredQuery, $request);
        
        return [
            'metrics' => $metrics,
            'charts' => $charts,
            'tables' => $tables,
            'filters' => $request->all(),
        ];
    }

    /**
     * Get the layouts for the report screen.
     */
    public function layout(): iterable
    {
        return [
            FormLayout::make($this->filters()),
            DashboardLayout::make(
                metrics: $this->metrics(),
                charts: $this->charts(),
                tables: $this->tables()
            ),
        ];
    }

    /**
     * Get the command bar for the report screen.
     */
    public function commandBar(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('generate')
                ->label('Generate Report'),
            
            \Orchid\Screen\Actions\Link::make('Export Report')
                ->icon('cloud-download')
                ->route($this->getExportRouteName())
                ->target('_blank'),
        ];
    }

    /**
     * Get export route name.
     */
    protected function getExportRouteName(): string
    {
        $currentRoute = request()->route()->getName();
        
        return str_replace('.report', '.export', $currentRoute);
    }

    /**
     * Handle report generation.
     */
    public function generate(Request $request): array
    {
        // This method is called when the report form is submitted
        // The query() method will handle the actual report generation
        return $this->query($request);
    }

    /**
     * Export report data.
     */
    public function export(Request $request)
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('viewAny', $model);
        
        $baseQuery = $this->baseQuery();
        $filteredQuery = $this->applyFilters($baseQuery, $request);
        
        $data = $filteredQuery->get();
        
        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Write headers
            fputcsv($file, ['ID', 'Created At', 'Data']);
            
            // Write data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->created_at,
                    json_encode($row->toArray()),
                ]);
            }
            
            fclose($file);
        }, 'report-export-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Get report description.
     */
    protected function getReportDescription(): string
    {
        return 'Analytics and reporting dashboard';
    }
}
