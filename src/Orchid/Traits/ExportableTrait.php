<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ExportableTrait
{
    /**
     * Export data to various formats.
     *
     * @param Builder $query
     * @param string $format
     * @param array $columns
     * @param array $options
     * @return StreamedResponse
     */
    public function scopeExport(Builder $query, string $format = 'csv', array $columns = [], array $options = []): StreamedResponse
    {
        $columns = empty($columns) ? $this->getExportableColumns() : $columns;
        $data = $query->get();

        return $this->exportData($data, $columns, $format, $options);
    }

    /**
     * Get exportable columns for the model.
     *
     * @return array
     */
    protected function getExportableColumns(): array
    {
        if (property_exists($this, 'exportable')) {
            return $this->exportable;
        }

        // Default to all fillable columns plus timestamps and ID
        return array_merge(
            ['id'],
            $this->getFillable(),
            ['created_at', 'updated_at']
        );
    }

    /**
     * Export data with custom transformation.
     *
     * @param Collection $data
     * @param array $columns
     * @param string $format
     * @param array $options
     * @return StreamedResponse
     */
    public function exportData(Collection $data, array $columns, string $format = 'csv', array $options = []): StreamedResponse
    {
        $filename = $this->getExportFilename($format, $options);
        $headers = $this->getExportHeaders($format, $filename);

        return response()->streamDownload(
            function () use ($data, $columns, $format, $options) {
                $this->generateExportContent($data, $columns, $format, $options);
            },
            $filename,
            $headers
        );
    }

    /**
     * Get export filename.
     *
     * @param string $format
     * @param array $options
     * @return string
     */
    protected function getExportFilename(string $format, array $options = []): string
    {
        $name = $options['filename'] ?? strtolower(class_basename($this));
        $timestamp = date('Y-m-d_H-i-s');
        
        return "{$name}_export_{$timestamp}.{$format}";
    }

    /**
     * Get export headers.
     *
     * @param string $format
     * @param string $filename
     * @return array
     */
    protected function getExportHeaders(string $format, string $filename): array
    {
        $contentTypes = [
            'csv' => 'text/csv',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'pdf' => 'application/pdf',
            'json' => 'application/json',
            'xml' => 'application/xml',
        ];

        return [
            'Content-Type' => $contentTypes[$format] ?? 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
    }

    /**
     * Generate export content.
     *
     * @param Collection $data
     * @param array $columns
     * @param string $format
     * @param array $options
     * @return void
     */
    protected function generateExportContent(Collection $data, array $columns, string $format, array $options): void
    {
        switch ($format) {
            case 'csv':
                $this->generateCsv($data, $columns, $options);
                break;
            case 'json':
                $this->generateJson($data, $columns, $options);
                break;
            case 'xml':
                $this->generateXml($data, $columns, $options);
                break;
            case 'xlsx':
            case 'xls':
                $this->generateExcel($data, $columns, $format, $options);
                break;
            case 'pdf':
                $this->generatePdf($data, $columns, $options);
                break;
            default:
                $this->generateText($data, $columns, $options);
                break;
        }
    }

    /**
     * Generate CSV export.
     *
     * @param Collection $data
     * @param array $columns
     * @param array $options
     * @return void
     */
    protected function generateCsv(Collection $data, array $columns, array $options): void
    {
        $delimiter = $options['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? '"';
        $escape = $options['escape'] ?? '\\';
        $includeHeaders = $options['include_headers'] ?? true;

        $handle = fopen('php://output', 'w');
        
        if ($includeHeaders) {
            $headers = array_map(function ($column) use ($options) {
                return $options['headers'][$column] ?? $this->getColumnLabel($column);
            }, $columns);
            
            fputcsv($handle, $headers, $delimiter, $enclosure, $escape);
        }

        foreach ($data as $row) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $this->getExportValue($row, $column, $options);
            }
            fputcsv($handle, $rowData, $delimiter, $enclosure, $escape);
        }

        fclose($handle);
    }

    /**
     * Generate JSON export.
     *
     * @param Collection $data
     * @param array $columns
     * @param array $options
     * @return void
     */
    protected function generateJson(Collection $data, array $columns, array $options): void
    {
        $exportData = $data->map(function ($row) use ($columns, $options) {
            $item = [];
            foreach ($columns as $column) {
                $item[$column] = $this->getExportValue($row, $column, $options);
            }
            return $item;
        });

        echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate XML export.
     *
     * @param Collection $data
     * @param array $columns
     * @param array $options
     * @return void
     */
    protected function generateXml(Collection $data, array $columns, array $options): void
    {
        $root = $options['root'] ?? 'data';
        $item = $options['item'] ?? 'item';
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo "<{$root}>";
        
        foreach ($data as $row) {
            echo "<{$item}>";
            foreach ($columns as $column) {
                $value = htmlspecialchars($this->getExportValue($row, $column, $options));
                echo "<{$column}>{$value}</{$column}>";
            }
            echo "</{$item}>";
        }
        
        echo "</{$root}>";
    }

    /**
     * Generate Excel export (requires PhpSpreadsheet).
     *
     * @param Collection $data
     * @param array $columns
     * @param string $format
     * @param array $options
     * @return void
     */
    protected function generateExcel(Collection $data, array $columns, string $format, array $options): void
    {
        // Check if PhpSpreadsheet is available
        $spreadsheetClass = 'PhpOffice\\PhpSpreadsheet\\Spreadsheet';
        $xlsxWriterClass = 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx';
        $xlsWriterClass = 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls';
        
        if (!class_exists($spreadsheetClass)) {
            // Fallback to CSV if PhpSpreadsheet not available
            $this->generateCsv($data, $columns, $options);
            return;
        }

        $spreadsheet = new $spreadsheetClass();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $col = 1;
        foreach ($columns as $column) {
            $sheet->setCellValueByColumnAndRow($col, 1, $this->getColumnLabel($column));
            $col++;
        }
        
        // Set data
        $row = 2;
        foreach ($data as $item) {
            $col = 1;
            foreach ($columns as $column) {
                $sheet->setCellValueByColumnAndRow($col, $row, $this->getExportValue($item, $column, $options));
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range(1, count($columns)) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }
        
        // Write to output
        if ($format === 'xlsx' && class_exists($xlsxWriterClass)) {
            $writer = new $xlsxWriterClass($spreadsheet);
        } elseif (class_exists($xlsWriterClass)) {
            $writer = new $xlsWriterClass($spreadsheet);
        } else {
            // Fallback to CSV
            $this->generateCsv($data, $columns, $options);
            return;
        }
        
        $writer->save('php://output');
    }

    /**
     * Generate PDF export (requires DomPDF or similar).
     *
     * @param Collection $data
     * @param array $columns
     * @param array $options
     * @return void
     */
    protected function generatePdf(Collection $data, array $columns, array $options): void
    {
        // Simple HTML table for PDF (requires DomPDF or similar)
        $html = '<html><head><style>table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #f2f2f2; }</style></head><body>';
        $html .= '<h1>' . ($options['title'] ?? class_basename($this) . ' Export') . '</h1>';
        $html .= '<table>';
        
        // Headers
        $html .= '<tr>';
        foreach ($columns as $column) {
            $html .= '<th>' . htmlspecialchars($this->getColumnLabel($column)) . '</th>';
        }
        $html .= '</tr>';
        
        // Data
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $html .= '<td>' . htmlspecialchars($this->getExportValue($row, $column, $options)) . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        
        echo $html;
    }

    /**
     * Generate plain text export.
     *
     * @param Collection $data
     * @param array $columns
     * @param array $options
     * @return void
     */
    protected function generateText(Collection $data, array $columns, array $options): void
    {
        $delimiter = $options['delimiter'] ?? "\t";
        
        // Headers
        $headers = array_map(function ($column) {
            return $this->getColumnLabel($column);
        }, $columns);
        
        echo implode($delimiter, $headers) . "\n";
        
        // Data
        foreach ($data as $row) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $this->getExportValue($row, $column, $options);
            }
            echo implode($delimiter, $rowData) . "\n";
        }
    }

    /**
     * Get column label for export.
     *
     * @param string $column
     * @return string
     */
    protected function getColumnLabel(string $column): string
    {
        $labels = property_exists($this, 'exportLabels') ? $this->exportLabels : [];
        
        return $labels[$column] ?? str_replace('_', ' ', ucfirst($column));
    }

    /**
     * Get export value for a column.
     *
     * @param mixed $row
     * @param string $column
     * @param array $options
     * @return mixed
     */
    protected function getExportValue($row, string $column, array $options = [])
    {
        $transformers = property_exists($this, 'exportTransformers') ? $this->exportTransformers : [];
        
        if (isset($transformers[$column]) && is_callable($transformers[$column])) {
            return $transformers[$column]($row, $column);
        }
        
        // Handle nested properties
        if (str_contains($column, '.')) {
            return data_get($row, $column);
        }
        
        // Handle accessor methods
        $accessor = 'get' . str_replace('_', '', ucwords($column, '_')) . 'Attribute';
        if (method_exists($row, $accessor)) {
            return $row->{$column};
        }
        
        return $row->{$column} ?? '';
    }

    /**
     * Export with filters from request.
     *
     * @param Builder $query
     * @param Request $request
     * @param string $format
     * @param array $columns
     * @return StreamedResponse
     */
    public function scopeExportFromRequest(Builder $query, Request $request, string $format = 'csv', array $columns = []): StreamedResponse
    {
        // Apply filters if FilterableTrait is used
        if (method_exists($this, 'scopeFilter')) {
            $query->filter($request);
        }
        
        // Apply sorting if SortableTrait is used
        if (method_exists($this, 'scopeSort')) {
            $query->sort($request);
        }
        
        return $this->scopeExport($query, $format, $columns, [
            'filename' => $request->input('filename'),
            'title' => $request->input('title'),
        ]);
    }

    /**
     * Get export formats supported.
     *
     * @return array
     */
    public static function getExportFormats(): array
    {
        return [
            'csv' => 'CSV (Comma Separated Values)',
            'xlsx' => 'Excel (XLSX)',
            'xls' => 'Excel (XLS)',
            'json' => 'JSON',
            'xml' => 'XML',
            'pdf' => 'PDF',
            'txt' => 'Plain Text',
        ];
    }

    /**
     * Get export template for UI.
     *
     * @return array
     */
    public static function getExportTemplate(): array
    {
        $instance = new static();
        $columns = $instance->getExportableColumns();
        
        $template = [];
        foreach ($columns as $column) {
            $template[$column] = [
                'label' => $instance->getColumnLabel($column),
                'enabled' => true,
                'order' => array_search($column, $columns),
            ];
        }
        
        return $template;
    }
}
