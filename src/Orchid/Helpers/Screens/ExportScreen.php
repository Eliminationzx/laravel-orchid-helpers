<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Orchid\\Helpers\\Orchid\Helpers\Layouts\FormLayout;

abstract class ExportScreen extends AbstractScreen
{
    /**
     * The Eloquent model class name.
     */
    abstract protected function modelClass(): string;

    /**
     * Get the form fields for the export screen.
     */
    protected function fields(): array
    {
        return [
            \Orchid\Screen\Fields\Select::make('format')
                ->title('Export Format')
                ->options([
                    'csv' => 'CSV',
                    'xlsx' => 'Excel',
                    'json' => 'JSON',
                    'pdf' => 'PDF',
                ])
                ->required()
                ->value('csv')
                ->help('Select the export format'),
            
            \Orchid\Screen\Fields\DateRange::make('date_range')
                ->title('Date Range')
                ->help('Filter records by date range (optional)'),
            
            \Orchid\Screen\Fields\Select::make('columns')
                ->title('Columns to Export')
                ->multiple()
                ->options($this->getExportableColumns())
                ->help('Select columns to include in export (select all for all columns)'),
        ];
    }

    /**
     * Get exportable columns for the model.
     */
    protected function getExportableColumns(): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        // Get fillable columns by default
        $columns = $model->getFillable();
        
        // Add timestamps if they exist
        if ($model->timestamps) {
            $columns[] = 'created_at';
            $columns[] = 'updated_at';
        }
        
        // Add primary key
        $columns[] = $model->getKeyName();
        
        // Create options array
        $options = [];
        foreach ($columns as $column) {
            $options[$column] = ucwords(str_replace('_', ' ', $column));
        }
        
        return $options;
    }

    /**
     * Get the validation rules for export.
     */
    protected function rules(): array
    {
        return [
            'format' => 'required|in:csv,xlsx,json,pdf',
            'date_range.start' => 'nullable|date',
            'date_range.end' => 'nullable|date|after_or_equal:date_range.start',
            'columns' => 'nullable|array',
            'columns.*' => 'string|in:' . implode(',', array_keys($this->getExportableColumns())),
        ];
    }

    /**
     * Get the base query for export.
     */
    protected function baseQuery(): Builder
    {
        $modelClass = $this->modelClass();

        return $modelClass::query();
    }

    /**
     * Apply filters to the query.
     */
    protected function applyFilters(Builder $query, Request $request): Builder
    {
        // Apply date range filter if provided
        if ($request->filled('date_range.start') && $request->filled('date_range.end')) {
            $query->whereBetween('created_at', [
                $request->input('date_range.start'),
                $request->input('date_range.end'),
            ]);
        }

        return $query;
    }

    /**
     * Get data for export.
     */
    protected function getExportData(Request $request): Collection
    {
        $query = $this->baseQuery();
        $query = $this->applyFilters($query, $request);
        
        return $query->get();
    }

    /**
     * Process export based on format.
     */
    abstract protected function processExport(Collection $data, array $columns, string $format);

    /**
     * Get the screen data for the export screen.
     */
    public function query(): array
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        $this->authorize('viewAny', $model);
        
        return [];
    }

    /**
     * Handle the export process.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function export(Request $request)
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass();
        
        // Authorize export
        $this->authorize('viewAny', $model);
        
        // Validate request data
        $validated = $request->validate($this->rules());
        
        // Get export data
        $data = $this->getExportData($request);
        
        // Get selected columns or all columns
        $columns = $validated['columns'] ?? array_keys($this->getExportableColumns());
        
        // Process export
        return $this->processExport($data, $columns, $validated['format']);
    }

    /**
     * Get the layouts for the export screen.
     */
    public function layout(): iterable
    {
        return [
            FormLayout::make($this->fields()),
        ];
    }

    /**
     * Get the command bar for the export screen.
     */
    public function commandBar(): iterable
    {
        return [
            \OrchidHelpers\Orchid\Helpers\Buttons\SaveButton::make()
                ->method('export')
                ->label('Export'),
        ];
    }

    /**
     * Generate CSV export.
     */
    protected function generateCsv(Collection $data, array $columns): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = $this->getExportFilename('csv');
        
        return response()->streamDownload(function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Write headers
            fputcsv($file, $columns);
            
            // Write data
            foreach ($data as $row) {
                $rowData = [];
                foreach ($columns as $column) {
                    $rowData[] = $row->{$column} ?? '';
                }
                fputcsv($file, $rowData);
            }
            
            fclose($file);
        }, $filename);
    }

    /**
     * Get export filename.
     */
    protected function getExportFilename(string $format): string
    {
        $modelName = class_basename($this->modelClass());
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        return strtolower("{$modelName}_export_{$timestamp}.{$format}");
    }
}
