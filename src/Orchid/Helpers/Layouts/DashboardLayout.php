<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Layouts;

use Orchid\Support\Facades\Layout;

class DashboardLayout
{
    public static function make(array $metrics, array $charts = [], array $tables = []): array
    {
        $layouts = [];
        
        // Add metrics
        if (!empty($metrics)) {
            $layouts[] = Layout::metrics($metrics);
        }
        
        // Add charts in a grid
        if (!empty($charts)) {
            $chartColumns = [];
            foreach ($charts as $chart) {
                $chartColumns[] = Layout::view('orchid-helpers::chart', [
                    'chart' => $chart,
                ]);
            }
            $layouts[] = Layout::columns($chartColumns);
        }
        
        // Add tables
        foreach ($tables as $table) {
            $layouts[] = Layout::table($table['key'] ?? 'table', $table['columns'] ?? []);
        }
        
        return $layouts;
    }

    public static function metricsOnly(array $metrics, int $chunkSize = 6): array
    {
        return ModelMetricLayout::make($metrics);
    }

    public static function withCards(array $cards, int $columns = 3): array
    {
        $chunked = array_chunk($cards, $columns);
        
        $rows = [];
        foreach ($chunked as $rowCards) {
            $cardColumns = [];
            foreach ($rowCards as $card) {
                $cardColumns[] = CardLayout::blank($card['content'] ?? []);
            }
            $rows[] = Layout::columns($cardColumns);
        }
        
        return $rows;
    }
}