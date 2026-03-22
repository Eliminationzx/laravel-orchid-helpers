<?php

declare(strict_types=1);

namespace Orchid\Helpers\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataExportService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Export data to CSV format
     *
     * @param  array|Collection|BaseCollection  $data
     * @param  array  $headers
     * @param  string|null  $filename
     * @return StreamedResponse
     */
    public function exportToCsv($data, array $headers = [], ?string $filename = null): StreamedResponse
    {
        $filename = $filename ?? 'export_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response()->streamDownload(function () use ($data, $headers) {
            $output = fopen('php://output', 'w');
            
            // Write headers
            if (!empty($headers)) {
                fputcsv($output, $headers);
            } elseif ($data instanceof Collection || $data instanceof BaseCollection) {
                $firstItem = $data->first();
                if (is_array($firstItem) || is_object($firstItem)) {
                    $headers = array_keys((array) $firstItem);
                    fputcsv($output, $headers);
                }
            }
            
            // Write data
            foreach ($data as $row) {
                if (is_array($row)) {
                    fputcsv($output, $row);
                } elseif (is_object($row)) {
                    fputcsv($output, (array) $row);
                } else {
                    fputcsv($output, [$row]);
                }
            }
            
            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export data to JSON format
     *
     * @param  array|Collection|BaseCollection  $data
     * @param  string|null  $filename
     * @param  int  $options
     * @return StreamedResponse
     */
    public function exportToJson($data, ?string $filename = null, int $options = JSON_PRETTY_PRINT): StreamedResponse
    {
        $filename = $filename ?? 'export_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->streamDownload(function () use ($data, $options) {
            echo json_encode($data, $options);
        }, $filename, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export data to Excel format (using Laravel Excel if available)
     *
     * @param  array|Collection|BaseCollection  $data
     * @param  array  $headers
     * @param  string|null  $filename
     * @param  string|null  $sheetName
     * @return mixed
     */
    public function exportToExcel($data, array $headers = [], ?string $filename = null, ?string $sheetName = null)
    {
        $filename = $filename ?? 'export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $sheetName = $sheetName ?? 'Sheet1';
        
        // Check if Laravel Excel is available
        if ($this->isLaravelExcelAvailable()) {
            return $this->exportToExcelWithLaravelExcel($data, $headers, $filename, $sheetName);
        }
        
        // Fallback to CSV if Excel is not available
        return $this->exportToCsv($data, $headers, str_replace('.xlsx', '.csv', $filename));
    }

    /**
     * Export query results with pagination support
     *
     * @param  Builder  $query
     * @param  string  $format
     * @param  array  $headers
     * @param  string|null  $filename
     * @param  int  $chunkSize
     * @return StreamedResponse
     */
    public function exportQuery(Builder $query, string $format = 'csv', array $headers = [], ?string $filename = null, int $chunkSize = 1000): StreamedResponse
    {
        $filename = $filename ?? 'export_' . date('Y-m-d_H-i-s') . '.' . $format;
        
        return response()->streamDownload(function () use ($query, $format, $headers, $chunkSize) {
            $output = fopen('php://output', 'w');
            
            // Write headers if provided
            if (!empty($headers)) {
                fputcsv($output, $headers);
            }
            
            // Stream results in chunks
            $query->chunk($chunkSize, function ($chunk) use ($output, $headers) {
                foreach ($chunk as $row) {
                    if (empty($headers)) {
                        // Write headers from first chunk
                        $headers = array_keys($row->toArray());
                        fputcsv($output, $headers);
                    }
                    
                    fputcsv($output, $row->toArray());
                }
            });
            
            fclose($output);
        }, $filename, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Save export to storage disk
     *
     * @param  array|Collection|BaseCollection  $data
     * @param  string  $format
     * @param  string  $disk
     * @param  string  $path
     * @param  array  $headers
     * @return string
     */
    public function saveToDisk($data, string $format = 'csv', string $disk = 'local', string $path = 'exports', array $headers = []): string
    {
        $filename = 'export_' . date('Y-m-d_H-i-s') . '.' . $format;
        $fullPath = rtrim($path, '/') . '/' . $filename;
        
        $content = '';
        
        if ($format === 'csv') {
            $output = fopen('php://temp', 'w');
            
            if (!empty($headers)) {
                fputcsv($output, $headers);
            } elseif ($data instanceof Collection || $data instanceof BaseCollection) {
                $firstItem = $data->first();
                if (is_array($firstItem) || is_object($firstItem)) {
                    $headers = array_keys((array) $firstItem);
                    fputcsv($output, $headers);
                }
            }
            
            foreach ($data as $row) {
                if (is_array($row)) {
                    fputcsv($output, $row);
                } elseif (is_object($row)) {
                    fputcsv($output, (array) $row);
                } else {
                    fputcsv($output, [$row]);
                }
            }
            
            rewind($output);
            $content = stream_get_contents($output);
            fclose($output);
        } elseif ($format === 'json') {
            $content = json_encode($data, JSON_PRETTY_PRINT);
        }
        
        Storage::disk($disk)->put($fullPath, $content);
        
        return $fullPath;
    }

    /**
     * Generate export filename with timestamp
     *
     * @param  string  $prefix
     * @param  string  $extension
     * @return string
     */
    public function generateFilename(string $prefix = 'export', string $extension = 'csv'): string
    {
        return $prefix . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
    }

    /**
     * Get supported export formats
     *
     * @return array
     */
    public function getSupportedFormats(): array
    {
        $formats = ['csv', 'json'];
        
        if ($this->isLaravelExcelAvailable()) {
            $formats[] = 'xlsx';
            $formats[] = 'xls';
        }
        
        return $formats;
    }

    /**
     * Check if Laravel Excel is available
     *
     * @return bool
     */
    private function isLaravelExcelAvailable(): bool
    {
        return class_exists('Maatwebsite\Excel\Facades\Excel');
    }

    /**
     * Export data using Laravel Excel
     *
     * @param  mixed  $data
     * @param  array  $headers
     * @param  string  $filename
     * @param  string  $sheetName
     * @return mixed
     */
    private function exportToExcelWithLaravelExcel($data, array $headers, string $filename, string $sheetName)
    {
        $excelClass = 'Maatwebsite\Excel\Facades\Excel';
        $fromCollectionInterface = 'Maatwebsite\Excel\Concerns\FromCollection';
        $withHeadingsInterface = 'Maatwebsite\Excel\Concerns\WithHeadings';
        
        $export = new class($data, $headers, $sheetName) implements $fromCollectionInterface, $withHeadingsInterface {
            private $data;
            private $headers;
            private $sheetName;
            
            public function __construct($data, array $headers, string $sheetName)
            {
                $this->data = $data;
                $this->headers = $headers;
                $this->sheetName = $sheetName;
            }
            
            public function collection()
            {
                if ($this->data instanceof Collection || $this->data instanceof BaseCollection) {
                    return $this->data;
                }
                
                return collect($this->data);
            }
            
            public function headings(): array
            {
                if (!empty($this->headers)) {
                    return $this->headers;
                }
                
                if ($this->data instanceof Collection || $this->data instanceof BaseCollection) {
                    $firstItem = $this->data->first();
                    if (is_array($firstItem) || is_object($firstItem)) {
                        return array_keys((array) $firstItem);
                    }
                }
                
                return [];
            }
            
            public function title(): string
            {
                return $this->sheetName;
            }
        };
        
        return $excelClass::download($export, $filename);
    }
}
