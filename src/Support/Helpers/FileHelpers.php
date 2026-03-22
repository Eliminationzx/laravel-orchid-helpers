<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

if (!function_exists('file_exists_safe')) {
    /**
     * Check if a file exists safely without throwing exceptions.
     *
     * @param string $path
     * @return bool
     */
    function file_exists_safe(string $path): bool
    {
        try {
            return file_exists($path);
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_read_safe')) {
    /**
     * Read file contents safely.
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    function file_read_safe(string $path, mixed $default = null): mixed
    {
        try {
            return file_get_contents($path);
        } catch (Exception) {
            return $default;
        }
    }
}

if (!function_exists('file_write_safe')) {
    /**
     * Write to a file safely.
     *
     * @param string $path
     * @param string $content
     * @param bool $createDirectory
     * @return bool
     */
    function file_write_safe(string $path, string $content, bool $createDirectory = true): bool
    {
        try {
            if ($createDirectory) {
                $directory = dirname($path);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
            }
            return file_put_contents($path, $content) !== false;
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_delete_safe')) {
    /**
     * Delete a file safely.
     *
     * @param string $path
     * @return bool
     */
    function file_delete_safe(string $path): bool
    {
        try {
            if (file_exists($path)) {
                return unlink($path);
            }
            return true;
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_copy_safe')) {
    /**
     * Copy a file safely.
     *
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     * @return bool
     */
    function file_copy_safe(string $source, string $destination, bool $overwrite = true): bool
    {
        try {
            if (!file_exists($source)) {
                return false;
            }
            if (!$overwrite && file_exists($destination)) {
                return false;
            }
            $directory = dirname($destination);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            return copy($source, $destination);
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_move_safe')) {
    /**
     * Move a file safely.
     *
     * @param string $source
     * @param string $destination
     * @param bool $overwrite
     * @return bool
     */
    function file_move_safe(string $source, string $destination, bool $overwrite = true): bool
    {
        try {
            if (!file_exists($source)) {
                return false;
            }
            if (!$overwrite && file_exists($destination)) {
                return false;
            }
            $directory = dirname($destination);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            return rename($source, $destination);
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_size_human')) {
    /**
     * Get human-readable file size.
     *
     * @param string $path
     * @param int $precision
     * @return string
     */
    function file_size_human(string $path, int $precision = 2): string
    {
        try {
            $bytes = filesize($path);
            if ($bytes === false) {
                return '0 B';
            }
            $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            $factor = floor((strlen((string) $bytes) - 1) / 3);
            $factor = min($factor, count($units) - 1);
            return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . ' ' . $units[$factor];
        } catch (Exception) {
            return '0 B';
        }
    }
}

if (!function_exists('file_extension')) {
    /**
     * Get file extension from path.
     *
     * @param string $path
     * @return string
     */
    function file_extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
}

if (!function_exists('file_name')) {
    /**
     * Get file name without extension from path.
     *
     * @param string $path
     * @return string
     */
    function file_name(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
}

if (!function_exists('file_basename')) {
    /**
     * Get file basename from path.
     *
     * @param string $path
     * @return string
     */
    function file_basename(string $path): string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }
}

if (!function_exists('file_directory')) {
    /**
     * Get directory from file path.
     *
     * @param string $path
     * @return string
     */
    function file_directory(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }
}

if (!function_exists('file_mime_type')) {
    /**
     * Get MIME type of a file.
     *
     * @param string $path
     * @return string|null
     */
    function file_mime_type(string $path): ?string
    {
        try {
            if (!file_exists($path)) {
                return null;
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mime ?: null;
        } catch (Exception) {
            return null;
        }
    }
}

if (!function_exists('file_is_image')) {
    /**
     * Check if file is an image.
     *
     * @param string $path
     * @return bool
     */
    function file_is_image(string $path): bool
    {
        $mime = file_mime_type($path);
        if (!$mime) {
            return false;
        }
        return str_starts_with($mime, 'image/');
    }
}

if (!function_exists('file_is_pdf')) {
    /**
     * Check if file is a PDF.
     *
     * @param string $path
     * @return bool
     */
    function file_is_pdf(string $path): bool
    {
        $mime = file_mime_type($path);
        return $mime === 'application/pdf';
    }
}

if (!function_exists('file_is_text')) {
    /**
     * Check if file is a text file.
     *
     * @param string $path
     * @return bool
     */
    function file_is_text(string $path): bool
    {
        $mime = file_mime_type($path);
        if (!$mime) {
            return false;
        }
        return str_starts_with($mime, 'text/') || in_array($mime, [
            'application/json',
            'application/xml',
            'application/javascript',
        ]);
    }
}

if (!function_exists('file_lines_count')) {
    /**
     * Count lines in a text file.
     *
     * @param string $path
     * @return int
     */
    function file_lines_count(string $path): int
    {
        try {
            if (!file_exists($path)) {
                return 0;
            }
            $file = fopen($path, 'rb');
            $lines = 0;
            while (!feof($file)) {
                $lines += substr_count(fread($file, 8192), "\n");
            }
            fclose($file);
            return $lines;
        } catch (Exception) {
            return 0;
        }
    }
}

if (!function_exists('file_temporary_path')) {
    /**
     * Generate a temporary file path.
     *
     * @param string|null $extension
     * @return string
     */
    function file_temporary_path(?string $extension = null): string
    {
        $tempDir = sys_get_temp_dir();
        $filename = uniqid('temp_', true);
        if ($extension) {
            $filename .= '.' . ltrim($extension, '.');
        }
        return $tempDir . DIRECTORY_SEPARATOR . $filename;
    }
}

if (!function_exists('file_ensure_directory')) {
    /**
     * Ensure directory exists.
     *
     * @param string $path
     * @param int $permissions
     * @return bool
     */
    function file_ensure_directory(string $path, int $permissions = 0755): bool
    {
        try {
            if (!is_dir($path)) {
                return mkdir($path, $permissions, true);
            }
            return true;
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_clean_directory')) {
    /**
     * Clean directory (remove all files and subdirectories).
     *
     * @param string $path
     * @param bool $removeSelf
     * @return bool
     */
    function file_clean_directory(string $path, bool $removeSelf = false): bool
    {
        try {
            if (!is_dir($path)) {
                return false;
            }
            $items = scandir($path);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $itemPath = $path . DIRECTORY_SEPARATOR . $item;
                if (is_dir($itemPath)) {
                    file_clean_directory($itemPath, true);
                } else {
                    unlink($itemPath);
                }
            }
            if ($removeSelf) {
                rmdir($path);
            }
            return true;
        } catch (Exception) {
            return false;
        }
    }
}

if (!function_exists('file_storage_path')) {
    /**
     * Get storage path for a file.
     *
     * @param string $path
     * @return string
     */
    function file_storage_path(string $path): string
    {
        return Storage::path($path);
    }
}

if (!function_exists('file_storage_url')) {
    /**
     * Get storage URL for a file.
     *
     * @param string $path
     * @return string
     */
    function file_storage_url(string $path): string
    {
        return Storage::url($path);
    }
}

if (!function_exists('file_storage_exists')) {
    /**
     * Check if file exists in storage.
     *
     * @param string $path
     * @return bool
     */
    function file_storage_exists(string $path): bool
    {
        return Storage::exists($path);
    }
}

if (!function_exists('file_storage_delete')) {
    /**
     * Delete file from storage.
     *
     * @param string $path
     * @return bool
     */
    function file_storage_delete(string $path): bool
    {
        return Storage::delete($path);
    }
}

if (!function_exists('file_storage_copy')) {
    /**
     * Copy file in storage.
     *
     * @param string $source
     * @param string $destination
     * @return bool
     */
    function file_storage_copy(string $source, string $destination): bool
    {
        return Storage::copy($source, $destination);
    }
}

if (!function_exists('file_storage_move')) {
    /**
     * Move file in storage.
     *
     * @param string $source
     * @param string $destination
     * @return bool
     */
    function file_storage_move(string $source, string $destination): bool
    {
        return Storage::move($source, $destination);
    }
}