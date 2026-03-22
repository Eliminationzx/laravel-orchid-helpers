<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Screens;

use Illuminate\Support\Collection;
use Orchid\\Helpers\\Orchid\Helpers\Layouts\DashboardLayout;

abstract class DashboardScreen extends AbstractScreen
{
    /**
     * Get dashboard metrics (key performance indicators).
     */
    abstract protected function metrics(): array;

    /**
     * Get dashboard charts.
     */
    abstract protected function charts(): array;

    /**
     * Get dashboard tables.
     */
    abstract protected function tables(): array;

    /**
     * Get dashboard cards (widgets).
     */
    protected function cards(): array
    {
        return [];
    }

    /**
     * Get dashboard alerts/notifications.
     */
    protected function alerts(): array
    {
        return [];
    }

    /**
     * Get dashboard quick actions.
     */
    protected function quickActions(): array
    {
        return [];
    }

    /**
     * Get dashboard recent activity.
     */
    protected function recentActivity(): array
    {
        return [];
    }

    /**
     * Calculate metrics data.
     */
    protected function calculateMetrics(): array
    {
        $metrics = [];
        
        foreach ($this->metrics() as $key => $config) {
            $metrics[$key] = $this->calculateMetric($config);
        }
        
        return $metrics;
    }

    /**
     * Calculate a single metric.
     */
    protected function calculateMetric(array $config)
    {
        $type = $config['type'] ?? 'count';
        $modelClass = $config['model'] ?? null;
        $field = $config['field'] ?? '*';
        $conditions = $config['conditions'] ?? [];
        
        if (!$modelClass) {
            return 0;
        }
        
        $query = $modelClass::query();
        
        // Apply conditions
        foreach ($conditions as $condition) {
            $query->where($condition['field'], $condition['operator'] ?? '=', $condition['value']);
        }
        
        switch ($type) {
            case 'count':
                return $query->count();
            case 'sum':
                return $query->sum($field);
            case 'avg':
                return $query->avg($field);
            case 'min':
                return $query->min($field);
            case 'max':
                return $query->max($field);
            case 'today':
                return $query->whereDate('created_at', today())->count();
            case 'this_week':
                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
            case 'this_month':
                return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
            case 'this_year':
                return $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->count();
            default:
                return 0;
        }
    }

    /**
     * Generate charts data.
     */
    protected function generateCharts(): array
    {
        $charts = [];
        
        foreach ($this->charts() as $key => $config) {
            $charts[$key] = $this->generateChart($config);
        }
        
        return $charts;
    }

    /**
     * Generate a single chart.
     */
    protected function generateChart(array $config): array
    {
        $type = $config['type'] ?? 'line';
        $modelClass = $config['model'] ?? null;
        $xField = $config['x_field'] ?? 'created_at';
        $yField = $config['y_field'] ?? 'id';
        $aggregation = $config['aggregation'] ?? 'count';
        $period = $config['period'] ?? 'month';
        $limit = $config['limit'] ?? 12;
        
        if (!$modelClass) {
            return [
                'type' => $type,
                'labels' => [],
                'data' => [],
            ];
        }
        
        $query = $modelClass::query();
        
        // Apply date range if specified
        if (isset($config['date_range'])) {
            $query->whereBetween('created_at', [
                $config['date_range']['start'],
                $config['date_range']['end'],
            ]);
        }
        
        // Group by time period
        switch ($period) {
            case 'day':
                $query->selectRaw("DATE({$xField}) as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("DATE({$xField})")
                    ->orderBy('period', 'desc')
                    ->limit($limit);
                break;
            case 'week':
                $query->selectRaw("YEARWEEK({$xField}) as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("YEARWEEK({$xField})")
                    ->orderBy('period', 'desc')
                    ->limit($limit);
                break;
            case 'month':
                $query->selectRaw("DATE_FORMAT({$xField}, '%Y-%m') as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("DATE_FORMAT({$xField}, '%Y-%m')")
                    ->orderBy('period', 'desc')
                    ->limit($limit);
                break;
            case 'year':
                $query->selectRaw("YEAR({$xField}) as period, {$aggregation}({$yField}) as value")
                    ->groupByRaw("YEAR({$xField})")
                    ->orderBy('period', 'desc')
                    ->limit($limit);
                break;
        }
        
        $data = $query->get()->reverse();
        
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
    protected function generateTables(): array
    {
        $tables = [];
        
        foreach ($this->tables() as $key => $config) {
            $tables[$key] = $this->generateTable($config);
        }
        
        return $tables;
    }

    /**
     * Generate a single table.
     */
    protected function generateTable(array $config): array
    {
        $modelClass = $config['model'] ?? null;
        $columns = $config['columns'] ?? [];
        $limit = $config['limit'] ?? 10;
        $orderBy = $config['order_by'] ?? 'created_at';
        $orderDirection = $config['order_direction'] ?? 'desc';
        $conditions = $config['conditions'] ?? [];
        
        if (!$modelClass) {
            return [
                'columns' => $columns,
                'data' => [],
            ];
        }
        
        $query = $modelClass::query();
        
        // Apply conditions
        foreach ($conditions as $condition) {
            $query->where($condition['field'], $condition['operator'] ?? '=', $condition['value']);
        }
        
        $data = $query->orderBy($orderBy, $orderDirection)
            ->limit($limit)
            ->get();
        
        return [
            'columns' => $columns,
            'data' => $data,
            'config' => $config,
        ];
    }

    /**
     * Get the screen data for the dashboard screen.
     */
    public function query(): array
    {
        $metrics = $this->calculateMetrics();
        $charts = $this->generateCharts();
        $tables = $this->generateTables();
        $cards = $this->cards();
        $alerts = $this->alerts();
        $quickActions = $this->quickActions();
        $recentActivity = $this->recentActivity();
        
        return [
            'metrics' => $metrics,
            'charts' => $charts,
            'tables' => $tables,
            'cards' => $cards,
            'alerts' => $alerts,
            'quickActions' => $quickActions,
            'recentActivity' => $recentActivity,
        ];
    }

    /**
     * Get the layouts for the dashboard screen.
     */
    public function layout(): iterable
    {
        $layouts = [];
        
        // Add metrics
        $layouts[] = DashboardLayout::make(
            metrics: $this->metrics(),
            charts: $this->charts(),
            tables: $this->tables()
        );
        
        // Add cards if any
        $cards = $this->cards();
        if (!empty($cards)) {
            $layouts[] = DashboardLayout::withCards($cards);
        }
        
        return $layouts;
    }

    /**
     * Get the command bar for the dashboard screen.
     */
    public function commandBar(): iterable
    {
        $actions = [];
        
        // Add quick actions to command bar
        foreach ($this->quickActions() as $action) {
            $actions[] = \Orchid\Screen\Actions\Link::make($action['label'])
                ->icon($action['icon'] ?? 'plus')
                ->route($action['route'])
                ->class('btn btn-primary');
        }
        
        // Add refresh button
        $actions[] = \Orchid\Screen\Actions\Button::make('Refresh')
            ->icon('refresh')
            ->method('refresh')
            ->class('btn btn-secondary');
        
        return $actions;
    }

    /**
     * Handle dashboard refresh.
     */
    public function refresh(): array
    {
        // This method is called when the refresh button is clicked
        // The query() method will handle the data refresh
        return $this->query();
    }

    /**
     * Get dashboard title.
     */
    public function name(): ?string
    {
        return 'Dashboard';
    }

    /**
     * Get dashboard description.
     */
    public function description(): ?string
    {
        return 'Overview of your application';
    }

    /**
     * Get dashboard welcome message.
     */
    protected function getWelcomeMessage(): string
    {
        $hour = now()->hour;
        
        if ($hour < 12) {
            return 'Good morning!';
        } elseif ($hour < 18) {
            return 'Good afternoon!';
        } else {
            return 'Good evening!';
        }
    }
}
