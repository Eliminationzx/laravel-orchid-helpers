<?php

declare(strict_types=1);

namespace Orchid\Helpers\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DataImportService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Import data from CSV file
     *
     * @param  UploadedFile|string  $file
     * @param  array  $rules
     * @param  array  $customMessages
     * @param  array  $customAttributes
     * @param  bool  $hasHeaders
     * @return Collection
     * @throws ValidationException
     */
    public function importFromCsv($file, array $rules = [], array $customMessages = [], array $customAttributes = [], bool $hasHeaders = true): Collection
    {
        $filePath = $this->getFilePath($file);
        $data = $this->parseCsvFile($filePath, $hasHeaders);
        
        if (!empty($rules)) {
            $this->validateData($data, $rules, $customMessages, $customAttributes);
        }
        
        return collect($data);
    }

    /**
     * Import data from JSON file
     *
     * @param  UploadedFile|string  $file
     * @param  array  $rules
     * @param  array  $customMessages
     * @param  array  $customAttributes
     * @return Collection
     * @throws ValidationException
     */
    public function importFromJson($file, array $rules = [], array $customMessages = [], array $customAttributes = []): Collection
    {
        $filePath = $this->getFilePath($file);
        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON file: ' . json_last_error_msg());
        }
        
        $data = is_array($data) ? $data : [$data];
        
        if (!empty($rules)) {
            $this->validateData($data, $rules, $customMessages, $customAttributes);
        }
        
        return collect($data);
    }

    /**
     * Import data from Excel file (using Laravel Excel if available)
     *
     * @param  UploadedFile|string  $file
     * @param  array  $rules
     * @param  array  $customMessages
     * @param  array  $customAttributes
     * @param  string|null  $sheetName
     * @return Collection
     * @throws ValidationException
     */
    public function importFromExcel($file, array $rules = [], array $customMessages = [], array $customAttributes = [], ?string $sheetName = null): Collection
    {
        if ($this->isLaravelExcelAvailable()) {
            return $this->importFromExcelWithLaravelExcel($file, $rules, $customMessages, $customAttributes, $sheetName);
        }
        
        // Fallback to CSV if Excel is not available
        return $this->importFromCsv($file, $rules, $customMessages, $customAttributes);
    }

    /**
     * Validate imported data
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $customMessages
     * @param  array  $customAttributes
     * @return void
     * @throws ValidationException
     */
    public function validateData(array $data, array $rules, array $customMessages = [], array $customAttributes = []): void
    {
        foreach ($data as $index => $row) {
            $validator = Validator::make(
                $row,
                $rules,
                $customMessages,
                $customAttributes
            );
            
            if ($validator->fails()) {
                throw new ValidationException($validator, null, "Validation failed for row {$index}: " . implode(', ', $validator->errors()->all()));
            }
        }
    }

    /**
     * Process imported data in chunks
     *
     * @param  UploadedFile|string  $file
     * @param  string  $format
     * @param  callable  $callback
     * @param  int  $chunkSize
     * @param  bool  $hasHeaders
     * @return int
     */
    public function processInChunks($file, string $format, callable $callback, int $chunkSize = 1000, bool $hasHeaders = true): int
    {
        $filePath = $this->getFilePath($file);
        $processedCount = 0;
        
        if ($format === 'csv') {
            $handle = fopen($filePath, 'r');
            
            if ($hasHeaders) {
                fgetcsv($handle); // Skip headers
            }
            
            $chunk = [];
            while (($row = fgetcsv($handle)) !== false) {
                $chunk[] = $row;
                
                if (count($chunk) >= $chunkSize) {
                    $callback($chunk, $processedCount);
                    $processedCount += count($chunk);
                    $chunk = [];
                }
            }
            
            if (!empty($chunk)) {
                $callback($chunk, $processedCount);
                $processedCount += count($chunk);
            }
            
            fclose($handle);
        } elseif ($format === 'json') {
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON file: ' . json_last_error_msg());
            }
            
            $data = is_array($data) ? $data : [$data];
            $chunks = array_chunk($data, $chunkSize);
            
            foreach ($chunks as $chunk) {
                $callback($chunk, $processedCount);
                $processedCount += count($chunk);
            }
        }
        
        return $processedCount;
    }

    /**
     * Get file extension
     *
     * @param  UploadedFile|string  $file
     * @return string
     */
    public function getFileExtension($file): string
    {
        if ($file instanceof UploadedFile) {
            return strtolower($file->getClientOriginalExtension());
        }
        
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * Get supported import formats
     *
     * @return array
     */
    public function getSupportedFormats(): array
    {
        $formats = ['csv', 'json'];
        
        if ($this->isLaravelExcelAvailable()) {
            $formats[] = 'xlsx';
            $formats[] = 'xls';
            $formats[] = 'ods';
        }
        
        return $formats;
    }

    /**
     * Check if file format is supported
     *
     * @param  UploadedFile|string  $file
     * @return bool
     */
    public function isFormatSupported($file): bool
    {
        $extension = $this->getFileExtension($file);
        return in_array($extension, $this->getSupportedFormats());
    }

    /**
     * Get file size in human readable format
     *
     * @param  UploadedFile|string  $file
     * @return string
     */
    public function getFileSize($file): string
    {
        if ($file instanceof UploadedFile) {
            $size = $file->getSize();
        } else {
            $size = filesize($this->getFilePath($file));
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        
        return number_format($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Get file path from UploadedFile or string
     *
     * @param  UploadedFile|string  $file
     * @return string
     */
    private function getFilePath($file): string
    {
        if ($file instanceof UploadedFile) {
            return $file->getRealPath();
        }
        
        if (Storage::exists($file)) {
            return Storage::path($file);
        }
        
        return $file;
    }

    /**
     * Parse CSV file
     *
     * @param  string  $filePath
     * @param  bool  $hasHeaders
     * @return array
     */
    private function parseCsvFile(string $filePath, bool $hasHeaders = true): array
    {
        $data = [];
        $handle = fopen($filePath, 'r');
        
        if ($hasHeaders) {
            $headers = fgetcsv($handle);
        }
        
        while (($row = fgetcsv($handle)) !== false) {
            if ($hasHeaders && isset($headers)) {
                $data[] = array_combine($headers, $row);
            } else {
                $data[] = $row;
            }
        }
        
        fclose($handle);
        return $data;
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
     * Import data using Laravel Excel
     *
     * @param  UploadedFile|string  $file
     * @param  array  $rules
     * @param  array  $customMessages
     * @param  array  $customAttributes
     * @param  string|null  $sheetName
     * @return Collection
     * @throws ValidationException
     */
    private function importFromExcelWithLaravelExcel($file, array $rules, array $customMessages, array $customAttributes, ?string $sheetName): Collection
    {
        $filePath = $this->getFilePath($file);
        $excelClass = 'Maatwebsite\Excel\Facades\Excel';
        $excelConstants = 'Maatwebsite\Excel\Excel';
        
        $data = $excelClass::toArray($filePath, null, constant($excelConstants . '::XLSX'));
        
        if ($sheetName && isset($data[$sheetName])) {
            $sheetData = $data[$sheetName];
        } else {
            $sheetData = reset($data);
        }
        
        // Remove headers if present
        $headers = array_shift($sheetData);
        $processedData = [];
        
        foreach ($sheetData as $row) {
            if (count($headers) === count($row)) {
                $processedData[] = array_combine($headers, $row);
            } else {
                $processedData[] = $row;
            }
        }
        
        if (!empty($rules)) {
            $this->validateData($processedData, $rules, $customMessages, $customAttributes);
        }
        
        return collect($processedData);
    }

    /**
     * Clean up imported data (remove empty rows, trim values, etc.)
     *
     * @param  array  $data
     * @param  bool  $removeEmptyRows
     * @param  bool  $trimValues
     * @return array
     */
    public function cleanData(array $data, bool $removeEmptyRows = true, bool $trimValues = true): array
    {
        $cleanedData = [];
        
        foreach ($data as $row) {
            if ($trimValues && is_array($row)) {
                $row = array_map(function ($value) {
                    return is_string($value) ? trim($value) : $value;
                }, $row);
            }
            
            if ($removeEmptyRows) {
                $isEmpty = true;
                foreach ($row as $value) {
                    if (!empty($value) || $value === 0 || $value === '0') {
                        $isEmpty = false;
                        break;
                    }
                }
                
                if ($isEmpty) {
                    continue;
                }
            }
            
            $cleanedData[] = $row;
        }
        
        return $cleanedData;
    }

    /**
     * Map imported data to model attributes
     *
     * @param  array  $data
     * @param  array  $mapping
     * @return array
     */
    public function mapData(array $data, array $mapping): array
    {
        $mappedData = [];
        
        foreach ($data as $row) {
            $mappedRow = [];
            
            foreach ($mapping as $sourceKey => $targetKey) {
                if (isset($row[$sourceKey])) {
                    $mappedRow[$targetKey] = $row[$sourceKey];
                } elseif (is_callable($targetKey)) {
                    $mappedRow[$sourceKey] = $targetKey($row);
                }
            }
            
            $mappedData[] = $mappedRow;
        }
        
        return $mappedData;
    }
}
