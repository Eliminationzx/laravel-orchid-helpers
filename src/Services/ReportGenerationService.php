<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportGenerationService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Generate summary report
     *
     * @param  Builder  $query
     * @param  array  $metrics
     * @param  string|null  $groupBy
     * @param  array  $filters
     * @return array
     */
    public function generateSummary(Builder $query, array $metrics, ?string $groupBy = null, array $filters = []): array
    {
        $this->applyFilters($query, $filters);
        
        $report = [
            'metrics' => [],
            'groups' => [],
            'total' => 0,
        ];
        
        // Calculate metrics
        foreach ($metrics as $metric) {
            $report['metrics'][$metric] = $this->calculateMetric($query, $metric);
        }
        
        // Group data if needed
        if ($groupBy) {
            $report['groups'] = $this->groupData($query, $groupBy, $metrics);
        }
        
        $report['total'] = $query->count();
        
        return $report;
    }

    /**
     * Generate time series report
     *
     * @param  Builder  $query
     * @param  string  $dateColumn
     * @param  string  $interval
     * @param  array  $metrics
     * @param  array  $filters
     * @return array
     */
    public function generateTimeSeries(Builder $query, string $dateColumn, string $interval = 'day', array $metrics = ['count'], array $filters = []): array
    {
        $this->applyFilters($query, $filters);
        
        $intervals = $this->getTimeIntervals($interval);
        $timeSeries = [];
        
        foreach ($intervals as $intervalLabel) {
            $intervalQuery = clone $query;
            $this->applyTimeFilter($intervalQuery, $dateColumn, $interval, $intervalLabel);
            
            $data = ['interval' => $intervalLabel];
            
            foreach ($metrics as $metric) {
                $data[$metric] = $this->calculateMetric($intervalQuery, $metric);
            }
            
            $timeSeries[] = $data;
        }
        
        return [
            'time_series' => $timeSeries,
            'interval' => $interval,
            'metrics' => $metrics,
        ];
    }

    /**
     * Generate comparison report
     *
     * @param  Builder  $baseQuery
     * @param  Builder  $comparisonQuery
     * @param  array  $metrics
     * @param  string  $comparisonType
     * @return array
     */
    public function generateComparison(Builder $baseQuery, Builder $comparisonQuery, array $metrics, string $comparisonType = 'percentage'): array
    {
        $baseData = [];
        $comparisonData = [];
        
        foreach ($metrics as $metric) {
            $baseValue = $this->calculateMetric($baseQuery, $metric);
            $comparisonValue = $this->calculateMetric($comparisonQuery, $metric);
            
            $baseData[$metric] = $baseValue;
            $comparisonData[$metric] = $comparisonValue;
            
            if ($comparisonType === 'percentage' && $baseValue != 0) {
                $change = (($comparisonValue - $baseValue) / $baseValue) * 100;
            } else {
                $change = $comparisonValue - $baseValue;
            }
            
            $comparisonData[$metric . '_change'] = $change;
        }
        
        return [
            'base' => $baseData,
            'comparison' => $comparisonData,
            'comparison_type' => $comparisonType,
        ];
    }

    /**
     * Generate pivot table report
     *
     * @param  Builder  $query
     * @param  string  $rowColumn
     * @param  string  $columnColumn
     * @param  string  $valueMetric
     * @param  array  $filters
     * @return array
     */
    public function generatePivotTable(Builder $query, string $rowColumn, string $columnColumn, string $valueMetric = 'count', array $filters = []): array
    {
        $this->applyFilters($query, $filters);
        
        $rows = $query->select($rowColumn)->distinct()->pluck($rowColumn)->toArray();
        $columns = $query->select($columnColumn)->distinct()->pluck($columnColumn)->toArray();
        
        $pivotData = [];
        $rowTotals = [];
        $columnTotals = array_fill_keys($columns, 0);
        $grandTotal = 0;
        
        foreach ($rows as $row) {
            $pivotData[$row] = [];
            $rowTotals[$row] = 0;
            
            foreach ($columns as $column) {
                $cellQuery = clone $query;
                $cellQuery->where($rowColumn, $row)->where($columnColumn, $column);
                
                $value = $this->calculateMetric($cellQuery, $valueMetric);
                $pivotData[$row][$column] = $value;
                
                $rowTotals[$row] += $value;
                $columnTotals[$column] += $value;
                $grandTotal += $value;
            }
        }
        
        return [
            'pivot_data' => $pivotData,
            'rows' => $rows,
            'columns' => $columns,
            'row_totals' => $rowTotals,
            'column_totals' => $columnTotals,
            'grand_total' => $grandTotal,
            'value_metric' => $valueMetric,
        ];
    }

    /**
     * Generate distribution report
     *
     * @param  Builder  $query
     * @param  string  $column
     * @param  array  $ranges
     * @param  array  $filters
     * @return array
     */
    public function generateDistribution(Builder $query, string $column, array $ranges = [], array $filters = []): array
    {
        $this->applyFilters($query, $filters);
        
        if (empty($ranges)) {
            // Auto-generate ranges based on data
            $min = $query->min($column);
            $max = $query->max($column);
            $rangeCount = 10;
            $step = ($max - $min) / $rangeCount;
            
            for ($i = 0; $i < $rangeCount; $i++) {
                $start = $min + ($i * $step);
                $end = $min + (($i + 1) * $step);
                $ranges[] = ['from' => $start, 'to' => $end];
            }
        }
        
        $distribution = [];
        $total = 0;
        
        foreach ($ranges as $range) {
            $rangeQuery = clone $query;
            
            if (isset($range['from'])) {
                $rangeQuery->where($column, '>=', $range['from']);
            }
            
            if (isset($range['to'])) {
                $rangeQuery->where($column, '<', $range['to']);
            }
            
            $count = $rangeQuery->count();
            $distribution[] = [
                'range' => $range,
                'count' => $count,
                'percentage' => 0, // Will be calculated later
            ];
            
            $total += $count;
        }
        
        // Calculate percentages
        foreach ($distribution as &$item) {
            if ($total > 0) {
                $item['percentage'] = ($item['count'] / $total) * 100;
            }
        }
        
        return [
            'distribution' => $distribution,
            'total' => $total,
            'column' => $column,
        ];
    }

    /**
     * Generate trend analysis report
     *
     * @param  Builder  $query
     * @param  string  $dateColumn
     * @param  string  $metric
     * @param  array  $filters
     * @return array
     */
    public function generateTrendAnalysis(Builder $query, string $dateColumn, string $metric = 'count', array $filters = []): array
    {
        $this->applyFilters($query, $filters);
        
        $timeSeries = $this->generateTimeSeries($query, $dateColumn, 'month', [$metric], $filters);
        
        $values = array_column($timeSeries['time_series'], $metric);
        $trend = $this->calculateTrend($values);
        
        return [
            'time_series' => $timeSeries['time_series'],
            'trend' => $trend,
            'metric' => $metric,
            'is_increasing' => $trend['slope'] > 0,
            'is_decreasing' => $trend['slope'] < 0,
            'is_stable' => abs($trend['slope']) < 0.01,
        ];
    }

    /**
     * Calculate metric
     *
     * @param  Builder  $query
     * @param  string  $metric
     * @return mixed
     */
    private function calculateMetric(Builder $query, string $metric)
    {
        $metric = strtolower($metric);
        
        switch ($metric) {
            case 'count':
                return $query->count();
            case 'sum':
                // Need column for sum
                return 0;
            case 'avg':
                // Need column for average
                return 0;
            case 'min':
                // Need column for min
                return 0;
            case 'max':
                // Need column for max
                return 0;
            default:
                if (str_starts_with($metric, 'sum_')) {
                    $column = substr($metric, 4);
                    return $query->sum($column);
                } elseif (str_starts_with($metric, 'avg_')) {
                    $column = substr($metric, 4);
                    return $query->avg($column);
                } elseif (str_starts_with($metric, 'min_')) {
                    $column = substr($metric, 4);
                    return $query->min($column);
                } elseif (str_starts_with($metric, 'max_')) {
                    $column = substr($metric, 4);
                    return $query->max($column);
                }
                
                return 0;
        }
    }

    /**
     * Apply filters to query
     *
     * @param  Builder  $query
     * @param  array  $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $filter) {
            if (isset($filter['column'], $filter['operator'], $filter['value'])) {
                $query->where($filter['column'], $filter['operator'], $filter['value']);
            } elseif (isset($filter['column'], $filter['value'])) {
                $query->where($filter['column'], $filter['value']);
            }
        }
    }

    /**
     * Apply time filter to query
     *
     * @param  Builder  $query
     * @param  string  $dateColumn
     * @param  string  $interval
     * @param  string  $intervalLabel
     * @return void
     */
    private function applyTimeFilter(Builder $query, string $dateColumn, string $interval, string $intervalLabel): void
    {
        $date = $this->parseIntervalLabel($intervalLabel, $interval);
        
        switch ($interval) {
            case 'day':
                $query->whereDate($dateColumn, $date);
                break;
            case 'week':
                $query->whereBetween($dateColumn, [
                    $date->startOfWeek(),
                    $date->endOfWeek(),
                ]);
                break;
            case 'month':
                $query->whereMonth($dateColumn, $date->month)
                      ->whereYear($dateColumn, $date->year);
                break;
            case 'year':
                $query->whereYear($dateColumn, $date->year);
                break;
        }
    }

    /**
     * Get time intervals
     *
     * @param  string  $interval
     * @return array
     */
    private function getTimeIntervals(string $interval): array
    {
        $intervals = [];
        $now = now();
        
        switch ($interval) {
            case 'day':
                for ($i = 30; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $intervals[] = $date->format('Y-m-d');
                }
                break;
            case 'week':
                for ($i = 12; $i >= 0; $i--) {
                    $date = $now->copy()->subWeeks($i);
                    $intervals[] = $date->format('Y-W');
                }
                break;
            case 'month':
                for ($i = 12; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $intervals[] = $date->format('Y-m');
                }
                break;
            case 'year':
                for ($i = 5; $i >= 0; $i--) {
                    $date = $now->copy()->subYears($i);
                    $intervals[] = $date->format('Y');
                }
                break;
        }
        
        return $intervals;
    }

    /**
     * Parse interval label to date
     *
     * @param  string  $label
     * @param  string  $interval
     * @return \Carbon\Carbon
     */
    private function parseIntervalLabel(string $label, string $interval): \Carbon\Carbon
    {
        switch ($interval) {
            case 'day':
                return \Carbon\Carbon::parse($label);
            case 'week':
                [$year, $week] = explode('-', $label);
                return \Carbon\Carbon::now()->setISODate($year, $week);
            case 'month':
                return \Carbon\Carbon::parse($label . '-01');
            case 'year':
                return \Carbon\Carbon::create($year, 1, 1);
            default:
                return \Carbon\Carbon::now();
        }
    }

    /**
     * Group data by column
     *
     * @param  Builder  $query
     * @param  string  $groupBy
     * @param  array  $metrics
     * @return array
     */
    private function groupData(Builder $query, string $groupBy, array $metrics): array
    {
        $groups = $query->select($groupBy, DB::raw('COUNT(*) as count'))
                       ->groupBy($groupBy)
                       ->get();
        
        $result = [];
        
        foreach ($groups as $group) {
            $groupQuery = clone $query;
            $groupQuery->where($groupBy, $group->$groupBy);
            
            $groupData = [
                'value' => $group->$groupBy,
                'count' => $group->count,
            ];
            
            foreach ($metrics as $metric) {
                if ($metric !== 'count') {
                    $groupData[$metric] = $this->calculateMetric($groupQuery, $metric);
                }
            }
            
            $result[] = $groupData;
        }
        
        return $result;
    }

    /**
     * Calculate trend (linear regression)
     *
     * @param  array  $values
     * @return array
     */
    private function calculateTrend(array $values): array
    {
        $n = count($values);
        
        if ($n < 2) {
            return ['slope' => 0, 'intercept' => 0, 'r_squared' => 0];
        }
        
        $xSum = 0;
        $ySum = 0;
        $xySum = 0;
        $x2Sum = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $x = $i;
            $y = $values[$i];
            
            $xSum += $x;
            $ySum += $y;
            $xySum += $x * $y;
            $x2Sum += $x * $x;
        }
        
        $slope = ($n * $xySum - $xSum * $ySum) / ($n * $x2Sum - $xSum * $xSum);
        $intercept = ($ySum - $slope * $xSum) / $n;
        
        // Calculate R-squared
        $yMean = $ySum / $n;
        $ssTotal = 0;
        $ssResidual = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $y = $values[$i];
            $yPredicted = $slope * $i + $intercept;
            
            $ssTotal += pow($y - $yMean, 2);
            $ssResidual += pow($y - $yPredicted, 2);
        }
        
        $rSquared = $ssTotal > 0 ? 1 - ($ssResidual / $ssTotal) : 0;
        
        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'r_squared' => $rSquared,
            'trend_strength' => $this->getTrendStrength($rSquared),
        ];
    }

    /**
     * Get trend strength description
     *
     * @param  float  $rSquared
     * @return string
     */
    private function getTrendStrength(float $rSquared): string
    {
        if ($rSquared >= 0.8) {
            return 'strong';
        } elseif ($rSquared >= 0.5) {
            return 'moderate';
        } elseif ($rSquared >= 0.2) {
            return 'weak';
        } else {
            return 'very weak or no trend';
        }
    }

    /**
     * Export report to array
     *
     * @param  array  $report
     * @param  string  $format
     * @return array
     */
    public function exportReport(array $report, string $format = 'array'): array
    {
        if ($format === 'json') {
            return json_decode(json_encode($report), true);
        }
        
        return $report;
    }

    /**
     * Get available report types
     *
     * @return array
     */
    public function getAvailableReportTypes(): array
    {
        return [
            'summary' => 'Summary Report',
            'time_series' => 'Time Series Report',
            'comparison' => 'Comparison Report',
            'pivot_table' => 'Pivot Table Report',
            'distribution' => 'Distribution Report',
            'trend_analysis' => 'Trend Analysis Report',
        ];
    }

    /**
     * Get report metrics
     *
     * @return array
     */
    public function getAvailableMetrics(): array
    {
        return [
            'count' => 'Count',
            'sum' => 'Sum',
            'avg' => 'Average',
            'min' => 'Minimum',
            'max' => 'Maximum',
        ];
    }

    /**
     * Get time intervals
     *
     * @return array
     */
    public function getAvailableTimeIntervals(): array
    {
        return [
            'day' => 'Daily',
            'week' => 'Weekly',
            'month' => 'Monthly',
            'year' => 'Yearly',
        ];
    }
}
