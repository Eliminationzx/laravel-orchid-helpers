<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ImportableTrait
{
    /**
     * Import data from various formats.
     *
     * @param Request $request
     * @param string $fieldName
     * @param array $options
     * @return array
     * @throws ValidationException
     */
    public static function importFromRequest(Request $request, string $fieldName = 'file', array $options = []): array
    {
        $file = $request->file($fieldName);
        
        if (!$file) {
            throw ValidationException::withMessages([
                $fieldName => 'No file uploaded.',
            ]);
        }

        return static::importFromFile($file, $options);
    }

    /**
     * Import data from uploaded file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     * @throws ValidationException
     */
    public static function importFromFile(UploadedFile $file, array $options = []): array
    {
        $format = $options['format'] ?? static::detectFileFormat($file);
        $data = static::parseImportFile($file, $format, $options);

        return static::processImportData($data, $options);
    }

    /**
     * Detect file format from extension.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected static function detectFileFormat(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        $formats = [
            'csv' => 'csv',
            'xlsx' => 'xlsx',
            'xls' => 'xls',
            'json' => 'json',
            'xml' => 'xml',
            'txt' => 'txt',
        ];

        return $formats[$extension] ?? 'csv';
    }

    /**
     * Parse import file based on format.
     *
     * @param UploadedFile $file
     * @param string $format
     * @param array $options
     * @return array
     */
    protected static function parseImportFile(UploadedFile $file, string $format, array $options): array
    {
        switch ($format) {
            case 'csv':
                return static::parseCsvFile($file, $options);
            case 'xlsx':
            case 'xls':
                return static::parseExcelFile($file, $format, $options);
            case 'json':
                return static::parseJsonFile($file, $options);
            case 'xml':
                return static::parseXmlFile($file, $options);
            case 'txt':
                return static::parseTextFile($file, $options);
            default:
                throw new \InvalidArgumentException("Unsupported import format: {$format}");
        }
    }

    /**
     * Parse CSV file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    protected static function parseCsvFile(UploadedFile $file, array $options): array
    {
        $delimiter = $options['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? '"';
        $escape = $options['escape'] ?? '\\';
        $hasHeaders = $options['has_headers'] ?? true;

        $rows = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if ($hasHeaders && ($headerRow = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            $headers = $headerRow;
        } else {
            $headers = [];
        }

        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            if ($hasHeaders && !empty($headers)) {
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $row[$index] ?? null;
                }
                $rows[] = $rowData;
            } else {
                $rows[] = $row;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse Excel file.
     *
     * @param UploadedFile $file
     * @param string $format
     * @param array $options
     * @return array
     */
    protected static function parseExcelFile(UploadedFile $file, string $format, array $options): array
    {
        // Check if PhpSpreadsheet is available
        $spreadsheetClass = 'PhpOffice\\PhpSpreadsheet\\IOFactory';
        
        if (!class_exists($spreadsheetClass)) {
            throw new \RuntimeException('PhpSpreadsheet is required for Excel imports.');
        }

        $spreadsheet = $spreadsheetClass::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        
        $hasHeaders = $options['has_headers'] ?? true;
        $startRow = $hasHeaders ? 2 : 1;
        
        $rows = [];
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        // Get headers
        $headers = [];
        if ($hasHeaders) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $headers[] = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
            }
        }
        
        // Get data
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                
                if ($hasHeaders && !empty($headers)) {
                    $header = $headers[$col - 1] ?? "column_{$col}";
                    $rowData[$header] = $value;
                } else {
                    $rowData[] = $value;
                }
            }
            
            // Skip empty rows
            if (!empty(array_filter($rowData, function ($value) {
                return $value !== null && $value !== '';
            }))) {
                $rows[] = $rowData;
            }
        }
        
        return $rows;
    }

    /**
     * Parse JSON file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    protected static function parseJsonFile(UploadedFile $file, array $options): array
    {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON format: ' . json_last_error_msg());
        }
        
        // Handle different JSON structures
        if (isset($options['json_path'])) {
            $data = data_get($data, $options['json_path'], []);
        }
        
        // Ensure we have an array of rows
        if (!is_array($data) || (count($data) > 0 && !is_array($data[0]))) {
            $data = [$data];
        }
        
        return $data;
    }

    /**
     * Parse XML file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    protected static function parseXmlFile(UploadedFile $file, array $options): array
    {
        $xml = simplexml_load_file($file->getPathname());
        
        if ($xml === false) {
            throw new \InvalidArgumentException('Invalid XML format.');
        }
        
        $rows = [];
        $itemPath = $options['item_path'] ?? 'item';
        
        foreach ($xml->xpath("//{$itemPath}") as $item) {
            $rowData = [];
            foreach ($item->children() as $child) {
                $rowData[$child->getName()] = (string) $child;
            }
            $rows[] = $rowData;
        }
        
        return $rows;
    }

    /**
     * Parse text file.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    protected static function parseTextFile(UploadedFile $file, array $options): array
    {
        $delimiter = $options['delimiter'] ?? "\t";
        $hasHeaders = $options['has_headers'] ?? true;
        
        $content = file_get_contents($file->getPathname());
        $lines = explode("\n", trim($content));
        
        $rows = [];
        
        if ($hasHeaders && !empty($lines)) {
            $headers = explode($delimiter, array_shift($lines));
        } else {
            $headers = [];
        }
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            
            $values = explode($delimiter, $line);
            
            if ($hasHeaders && !empty($headers)) {
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $values[$index] ?? null;
                }
                $rows[] = $rowData;
            } else {
                $rows[] = $values;
            }
        }
        
        return $rows;
    }

    /**
     * Process import data.
     *
     * @param array $data
     * @param array $options
     * @return array
     * @throws ValidationException
     */
    protected static function processImportData(array $data, array $options): array
    {
        $results = [
            'total' => count($data),
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'imported' => [],
        ];

        $batchSize = $options['batch_size'] ?? 100;
        $validationRules = $options['rules'] ?? static::getImportValidationRules();
        $mapping = $options['mapping'] ?? static::getImportFieldMapping();
        $updateExisting = $options['update_existing'] ?? false;
        $uniqueKey = $options['unique_key'] ?? 'id';

        $batch = [];
        $batchCount = 0;

        foreach ($data as $index => $row) {
            try {
                // Map fields if mapping provided
                $mappedRow = static::mapImportRow($row, $mapping);
                
                // Validate row
                $validator = Validator::make($mappedRow, $validationRules);
                
                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
                
                // Prepare model data
                $modelData = static::prepareImportData($mappedRow, $options);
                
                // Check for existing record
                $existing = null;
                if ($updateExisting && isset($modelData[$uniqueKey])) {
                    $existing = static::where($uniqueKey, $modelData[$uniqueKey])->first();
                }
                
                if ($existing) {
                    // Update existing
                    $existing->update($modelData);
                    $model = $existing;
                    $action = 'updated';
                } else {
                    // Create new
                    $model = static::create($modelData);
                    $action = 'created';
                }
                
                $batch[] = $model;
                $batchCount++;
                
                $results['imported'][] = [
                    'index' => $index,
                    'id' => $model->getKey(),
                    'action' => $action,
                    'data' => $modelData,
                ];
                
                $results['successful']++;
                
                // Process batch
                if ($batchCount >= $batchSize) {
                    static::processImportBatch($batch, $options);
                    $batch = [];
                    $batchCount = 0;
                }
                
            } catch (ValidationException $e) {
                $results['failed']++;
                $results['errors'][$index] = [
                    'row' => $row,
                    'errors' => $e->errors(),
                ];
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][$index] = [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        // Process remaining batch
        if (!empty($batch)) {
            static::processImportBatch($batch, $options);
        }
        
        return $results;
    }

    /**
     * Get import validation rules.
     *
     * @return array
     */
    protected static function getImportValidationRules(): array
    {
        $instance = new static();
        
        if (property_exists($instance, 'importRules')) {
            return $instance->importRules;
        }
        
        // Default to model's validation rules if available
        if (method_exists($instance, 'rules')) {
            return $instance->rules();
        }
        
        return [];
    }

    /**
     * Get import field mapping.
     *
     * @return array
     */
    protected static function getImportFieldMapping(): array
    {
        $instance = new static();
        
        if (property_exists($instance, 'importMapping')) {
            return $instance->importMapping;
        }
        
        return [];
    }

    /**
     * Map import row using field mapping.
     *
     * @param array $row
     * @param array $mapping
     * @return array
     */
    protected static function mapImportRow(array $row, array $mapping): array
    {
        if (empty($mapping)) {
            return $row;
        }
        
        $mapped = [];
        
        foreach ($mapping as $source => $target) {
            if (is_numeric($source)) {
                // If numeric key, use target as both source and target
                $source = $target;
            }
            
            if (array_key_exists($source, $row)) {
                $mapped[$target] = $row[$source];
            } elseif (isset($row[$target])) {
                $mapped[$target] = $row[$target];
            }
        }
        
        return $mapped;
    }

    /**
     * Prepare import data for model.
     *
     * @param array $row
     * @param array $options
     * @return array
     */
    protected static function prepareImportData(array $row, array $options): array
    {
        $instance = new static();
        $fillable = $instance->getFillable();
        
        // Filter only fillable fields
        $data = array_intersect_key($row, array_flip($fillable));
        
        // Apply transformations if any
        if (property_exists($instance, 'importTransformations')) {
            foreach ($instance->importTransformations as $field => $transformation) {
                if (isset($data[$field]) && is_callable($transformation)) {
                    $data[$field] = $transformation($data[$field], $row);
                }
            }
        }
        
        // Set default values
        $defaults = $options['defaults'] ?? [];
        foreach ($defaults as $field => $value) {
            if (!isset($data[$field])) {
                $data[$field] = is_callable($value) ? $value($row) : $value;
            }
        }
        
        return $data;
    }

    /**
     * Process import batch.
     *
     * @param array $models
     * @param array $options
     * @return void
     */
    protected static function processImportBatch(array $models, array $options): void
    {
        // Can be overridden to add batch processing logic
        // Example: dispatch jobs, send notifications, etc.
    }

    /**
     * Get import template for UI.
     *
     * @return array
     */
    public static function getImportTemplate(): array
    {
        $instance = new static();
        $fillable = $instance->getFillable();
        
        $template = [];
        foreach ($fillable as $field) {
            $template[$field] = [
                'label' => str_replace('_', ' ', ucfirst($field)),
                'required' => in_array($field, $instance->getRequiredFields() ?? []),
                'type' => static::getFieldType($field),
            ];
        }
        
        return $template;
    }

    /**
     * Get field type for import template.
     *
     * @param string $field
     * @return string
     */
    protected static function getFieldType(string $field): string
    {
        $instance = new static();
        
        if (method_exists($instance, 'getCasts') && isset($instance->getCasts()[$field])) {
            return $instance->getCasts()[$field];
        }
        
        // Guess type from field name
        if (str_contains($field, 'email')) {
            return 'email';
        }
        
        if (str_contains($field, 'password')) {
            return 'password';
        }
        
        if (str_contains($field, 'date') || str_contains($field, 'at')) {
            return 'date';
        }
        
        if (str_contains($field, 'amount') || str_contains($field, 'price') || str_contains($field, 'cost')) {
            return 'decimal';
        }
        
        return 'string';
    }

    /**
     * Get required fields for import.
     *
     * @return array
     */
    protected function getRequiredFields(): array
    {
        $instance = new static();
        
        if (property_exists($instance, 'importRequired')) {
            return $instance->importRequired;
        }
        
        // Check validation rules for required fields
        $rules = static::getImportValidationRules();
        $required = [];
        
        foreach ($rules as $field => $rule) {
            if (is_string($rule) && str_contains($rule, 'required')) {
                $required[] = $field;
            } elseif (is_array($rule) && in_array('required', $rule)) {
                $required[] = $field;
            }
        }
        
        return $required;
    }
}
