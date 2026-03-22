<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileStorageService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Store uploaded file
     *
     * @param  UploadedFile  $file
     * @param  string  $disk
     * @param  string  $directory
     * @param  string|null  $filename
     * @return string
     */
    public function storeUploadedFile(UploadedFile $file, string $disk = 'public', string $directory = 'uploads', ?string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }

    /**
     * Store file from content
     *
     * @param  string  $content
     * @param  string  $extension
     * @param  string  $disk
     * @param  string  $directory
     * @param  string|null  $filename
     * @return string
     */
    public function storeFromContent(string $content, string $extension, string $disk = 'public', string $directory = 'uploads', ?string $filename = null): string
    {
        $filename = $filename ?? $this->generateFilenameFromExtension($extension);
        $path = rtrim($directory, '/') . '/' . $filename;
        
        Storage::disk($disk)->put($path, $content);
        
        return $path;
    }

    /**
     * Store file from base64
     *
     * @param  string  $base64
     * @param  string  $disk
     * @param  string  $directory
     * @param  string|null  $filename
     * @return string|null
     */
    public function storeFromBase64(string $base64, string $disk = 'public', string $directory = 'uploads', ?string $filename = null): ?string
    {
        if (!preg_match('/^data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+);base64,(.*)$/', $base64, $matches)) {
            return null;
        }
        
        $mimeType = $matches[1];
        $content = base64_decode($matches[2]);
        $extension = $this->getExtensionFromMimeType($mimeType);
        
        if (!$extension) {
            return null;
        }
        
        $filename = $filename ?? $this->generateFilenameFromExtension($extension);
        $path = rtrim($directory, '/') . '/' . $filename;
        
        Storage::disk($disk)->put($path, $content);
        
        return $path;
    }

    /**
     * Get file URL
     *
     * @param  string  $path
     * @param  string  $disk
     * @return string|null
     */
    public function getUrl(string $path, string $disk = 'public'): ?string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        return Storage::disk($disk)->url($path);
    }

    /**
     * Get file contents
     *
     * @param  string  $path
     * @param  string  $disk
     * @return string|null
     */
    public function getContents(string $path, string $disk = 'public'): ?string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        return Storage::disk($disk)->get($path);
    }

    /**
     * Download file
     *
     * @param  string  $path
     * @param  string  $disk
     * @param  string|null  $downloadName
     * @return StreamedResponse
     */
    public function download(string $path, string $disk = 'public', ?string $downloadName = null): StreamedResponse
    {
        $downloadName = $downloadName ?? basename($path);
        
        return Storage::disk($disk)->download($path, $downloadName);
    }

    /**
     * Delete file
     *
     * @param  string  $path
     * @param  string  $disk
     * @return bool
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        if (!Storage::disk($disk)->exists($path)) {
            return false;
        }
        
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Delete multiple files
     *
     * @param  array  $paths
     * @param  string  $disk
     * @return bool
     */
    public function deleteMultiple(array $paths, string $disk = 'public'): bool
    {
        $success = true;
        
        foreach ($paths as $path) {
            if (!Storage::disk($disk)->exists($path)) {
                continue;
            }
            
            if (!Storage::disk($disk)->delete($path)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Move file
     *
     * @param  string  $fromPath
     * @param  string  $toPath
     * @param  string  $disk
     * @return bool
     */
    public function move(string $fromPath, string $toPath, string $disk = 'public'): bool
    {
        if (!Storage::disk($disk)->exists($fromPath)) {
            return false;
        }
        
        return Storage::disk($disk)->move($fromPath, $toPath);
    }

    /**
     * Copy file
     *
     * @param  string  $fromPath
     * @param  string  $toPath
     * @param  string  $disk
     * @return bool
     */
    public function copy(string $fromPath, string $toPath, string $disk = 'public'): bool
    {
        if (!Storage::disk($disk)->exists($fromPath)) {
            return false;
        }
        
        return Storage::disk($disk)->copy($fromPath, $toPath);
    }

    /**
     * Check if file exists
     *
     * @param  string  $path
     * @param  string  $disk
     * @return bool
     */
    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get file size
     *
     * @param  string  $path
     * @param  string  $disk
     * @return int|null
     */
    public function getSize(string $path, string $disk = 'public'): ?int
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        return Storage::disk($disk)->size($path);
    }

    /**
     * Get file size in human readable format
     *
     * @param  string  $path
     * @param  string  $disk
     * @return string|null
     */
    public function getHumanReadableSize(string $path, string $disk = 'public'): ?string
    {
        $size = $this->getSize($path, $disk);
        
        if ($size === null) {
            return null;
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        
        return number_format($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Get file MIME type
     *
     * @param  string  $path
     * @param  string  $disk
     * @return string|null
     */
    public function getMimeType(string $path, string $disk = 'public'): ?string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        return Storage::disk($disk)->mimeType($path);
    }

    /**
     * Get file last modified time
     *
     * @param  string  $path
     * @param  string  $disk
     * @return int|null
     */
    public function getLastModified(string $path, string $disk = 'public'): ?int
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        return Storage::disk($disk)->lastModified($path);
    }

    /**
     * List files in directory
     *
     * @param  string  $directory
     * @param  string  $disk
     * @param  bool  $recursive
     * @return array
     */
    public function listFiles(string $directory = '', string $disk = 'public', bool $recursive = false): array
    {
        return Storage::disk($disk)->files($directory, $recursive);
    }

    /**
     * List directories
     *
     * @param  string  $directory
     * @param  string  $disk
     * @param  bool  $recursive
     * @return array
     */
    public function listDirectories(string $directory = '', string $disk = 'public', bool $recursive = false): array
    {
        return Storage::disk($disk)->directories($directory, $recursive);
    }

    /**
     * Create directory
     *
     * @param  string  $directory
     * @param  string  $disk
     * @return bool
     */
    public function createDirectory(string $directory, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->makeDirectory($directory);
    }

    /**
     * Delete directory
     *
     * @param  string  $directory
     * @param  string  $disk
     * @return bool
     */
    public function deleteDirectory(string $directory, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->deleteDirectory($directory);
    }

    /**
     * Generate filename from uploaded file
     *
     * @param  UploadedFile  $file
     * @return string
     */
    public function generateFilename(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $slug = Str::slug($originalName);
        $timestamp = time();
        $random = Str::random(8);
        
        return "{$slug}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Generate filename from extension
     *
     * @param  string  $extension
     * @return string
     */
    public function generateFilenameFromExtension(string $extension): string
    {
        $timestamp = time();
        $random = Str::random(8);
        
        return "file_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get extension from MIME type
     *
     * @param  string  $mimeType
     * @return string|null
     */
    public function getExtensionFromMimeType(string $mimeType): ?string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
            'application/json' => 'json',
        ];
        
        return $mimeMap[$mimeType] ?? null;
    }

    /**
     * Get allowed MIME types for upload
     *
     * @return array
     */
    public function getAllowedMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            'application/json',
        ];
    }

    /**
     * Get allowed extensions for upload
     *
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            'pdf', 'doc', 'docx', 'xls', 'xlsx',
            'txt', 'csv', 'json',
        ];
    }

    /**
     * Validate file by MIME type
     *
     * @param  UploadedFile  $file
     * @param  array  $allowedMimeTypes
     * @return bool
     */
    public function validateMimeType(UploadedFile $file, array $allowedMimeTypes = []): bool
    {
        if (empty($allowedMimeTypes)) {
            $allowedMimeTypes = $this->getAllowedMimeTypes();
        }
        
        return in_array($file->getMimeType(), $allowedMimeTypes);
    }

    /**
     * Validate file by extension
     *
     * @param  UploadedFile  $file
     * @param  array  $allowedExtensions
     * @return bool
     */
    public function validateExtension(UploadedFile $file, array $allowedExtensions = []): bool
    {
        if (empty($allowedExtensions)) {
            $allowedExtensions = $this->getAllowedExtensions();
        }
        
        return in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions);
    }

    /**
     * Validate file size
     *
     * @param  UploadedFile  $file
     * @param  int  $maxSizeInBytes
     * @return bool
     */
    public function validateSize(UploadedFile $file, int $maxSizeInBytes = 10485760): bool
    {
        return $file->getSize() <= $maxSizeInBytes;
    }

    /**
     * Get disk configuration
     *
     * @param  string  $disk
     * @return array|null
     */
    public function getDiskConfig(string $disk = 'public'): ?array
    {
        $config = config("filesystems.disks.{$disk}");
        
        return $config ?: null;
    }

    /**
     * Get available disks
     *
     * @return array
     */
    public function getAvailableDisks(): array
    {
        return array_keys(config('filesystems.disks', []));
    }
}