<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageProcessingService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Check if Intervention Image is available
     *
     * @return bool
     */
    public function isInterventionImageAvailable(): bool
    {
        return class_exists('Intervention\Image\ImageManager');
    }

    /**
     * Resize image
     *
     * @param  string|UploadedFile  $image
     * @param  int  $width
     * @param  int  $height
     * @param  bool  $aspectRatio
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function resize($image, int $width, int $height, bool $aspectRatio = true, string $disk = 'public', ?string $outputPath = null): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->handleImageWithoutIntervention($image, $disk, $outputPath);
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $img = $imageManager->make($imageContent);
        
        if ($aspectRatio) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->resize($width, $height);
        }
        
        return $this->saveImage($img, $outputPath ?? $this->generateOutputPath($image, "resized_{$width}x{$height}"), $disk);
    }

    /**
     * Crop image
     *
     * @param  string|UploadedFile  $image
     * @param  int  $width
     * @param  int  $height
     * @param  int  $x
     * @param  int  $y
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function crop($image, int $width, int $height, int $x = 0, int $y = 0, string $disk = 'public', ?string $outputPath = null): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->handleImageWithoutIntervention($image, $disk, $outputPath);
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $img = $imageManager->make($imageContent);
        $img->crop($width, $height, $x, $y);
        
        return $this->saveImage($img, $outputPath ?? $this->generateOutputPath($image, "cropped_{$width}x{$height}"), $disk);
    }

    /**
     * Fit image to dimensions
     *
     * @param  string|UploadedFile  $image
     * @param  int  $width
     * @param  int  $height
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function fit($image, int $width, int $height, string $disk = 'public', ?string $outputPath = null): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->handleImageWithoutIntervention($image, $disk, $outputPath);
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $img = $imageManager->make($imageContent);
        $img->fit($width, $height);
        
        return $this->saveImage($img, $outputPath ?? $this->generateOutputPath($image, "fit_{$width}x{$height}"), $disk);
    }

    /**
     * Convert image format
     *
     * @param  string|UploadedFile  $image
     * @param  string  $format
     * @param  int  $quality
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function convert($image, string $format = 'jpg', int $quality = 90, string $disk = 'public', ?string $outputPath = null): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->handleImageWithoutIntervention($image, $disk, $outputPath);
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $img = $imageManager->make($imageContent);
        
        return $this->saveImage($img, $outputPath ?? $this->generateOutputPath($image, "converted"), $disk, $format, $quality);
    }

    /**
     * Apply watermark to image
     *
     * @param  string|UploadedFile  $image
     * @param  string|UploadedFile  $watermark
     * @param  string  $position
     * @param  int  $opacity
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function watermark($image, $watermark, string $position = 'bottom-right', int $opacity = 50, string $disk = 'public', ?string $outputPath = null): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->handleImageWithoutIntervention($image, $disk, $outputPath);
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $imageContent = $this->getImageContent($image, $disk);
        $watermarkContent = $this->getImageContent($watermark, $disk);
        
        if (!$imageContent || !$watermarkContent) {
            return null;
        }
        
        $img = $imageManager->make($imageContent);
        $watermarkImg = $imageManager->make($watermarkContent);
        
        $watermarkImg->opacity($opacity);
        $img->insert($watermarkImg, $position);
        
        return $this->saveImage($img, $outputPath ?? $this->generateOutputPath($image, "watermarked"), $disk);
    }

    /**
     * Optimize image (reduce file size)
     *
     * @param  string|UploadedFile  $image
     * @param  int  $quality
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function optimize($image, int $quality = 80, string $disk = 'public', ?string $outputPath = null): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->handleImageWithoutIntervention($image, $disk, $outputPath);
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $img = $imageManager->make($imageContent);
        
        // Get original format
        $extension = $this->getImageExtension($image);
        $format = $this->getFormatFromExtension($extension);
        
        return $this->saveImage($img, $outputPath ?? $this->generateOutputPath($image, "optimized"), $disk, $format, $quality);
    }

    /**
     * Create thumbnail
     *
     * @param  string|UploadedFile  $image
     * @param  int  $width
     * @param  int  $height
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    public function thumbnail($image, int $width = 150, int $height = 150, string $disk = 'public', ?string $outputPath = null): ?string
    {
        return $this->fit($image, $width, $height, $disk, $outputPath ?? $this->generateOutputPath($image, "thumbnail_{$width}x{$height}"));
    }

    /**
     * Get image dimensions
     *
     * @param  string|UploadedFile  $image
     * @param  string  $disk
     * @return array|null
     */
    public function getDimensions($image, string $disk = 'public'): ?array
    {
        if (!$this->isInterventionImageAvailable()) {
            return $this->getDimensionsWithoutIntervention($image, $disk);
        }
        
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $img = $imageManager->make($imageContent);
        
        return [
            'width' => $img->width(),
            'height' => $img->height(),
            'aspect_ratio' => $img->width() / $img->height(),
        ];
    }

    /**
     * Get image file size
     *
     * @param  string|UploadedFile  $image
     * @param  string  $disk
     * @return int|null
     */
    public function getFileSize($image, string $disk = 'public'): ?int
    {
        if ($image instanceof UploadedFile) {
            return $image->getSize();
        }
        
        if (Storage::disk($disk)->exists($image)) {
            return Storage::disk($disk)->size($image);
        }
        
        if (file_exists($image)) {
            return filesize($image);
        }
        
        return null;
    }

    /**
     * Get image MIME type
     *
     * @param  string|UploadedFile  $image
     * @param  string  $disk
     * @return string|null
     */
    public function getMimeType($image, string $disk = 'public'): ?string
    {
        if ($image instanceof UploadedFile) {
            return $image->getMimeType();
        }
        
        if (Storage::disk($disk)->exists($image)) {
            return Storage::disk($disk)->mimeType($image);
        }
        
        if (file_exists($image)) {
            return mime_content_type($image);
        }
        
        return null;
    }

    /**
     * Get image extension
     *
     * @param  string|UploadedFile  $image
     * @return string|null
     */
    public function getImageExtension($image): ?string
    {
        if ($image instanceof UploadedFile) {
            return strtolower($image->getClientOriginalExtension());
        }
        
        return strtolower(pathinfo($image, PATHINFO_EXTENSION));
    }

    /**
     * Check if file is an image
     *
     * @param  string|UploadedFile  $file
     * @param  string  $disk
     * @return bool
     */
    public function isImage($file, string $disk = 'public'): bool
    {
        $mimeType = $this->getMimeType($file, $disk);
        
        if (!$mimeType) {
            return false;
        }
        
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Get supported image formats
     *
     * @return array
     */
    public function getSupportedFormats(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    }

    /**
     * Get image content
     *
     * @param  string|UploadedFile  $image
     * @param  string  $disk
     * @return string|null
     */
    private function getImageContent($image, string $disk): ?string
    {
        if ($image instanceof UploadedFile) {
            return file_get_contents($image->getRealPath());
        }
        
        if (Storage::disk($disk)->exists($image)) {
            return Storage::disk($disk)->get($image);
        }
        
        if (file_exists($image)) {
            return file_get_contents($image);
        }
        
        return null;
    }

    /**
     * Save image
     *
     * @param  mixed  $image
     * @param  string  $path
     * @param  string  $disk
     * @param  string|null  $format
     * @param  int  $quality
     * @return string
     */
    private function saveImage($image, string $path, string $disk, ?string $format = null, int $quality = 90): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $format = $format ?? $this->getFormatFromExtension($extension);
        
        $content = $image->encode($format, $quality);
        Storage::disk($disk)->put($path, $content);
        
        return $path;
    }

    /**
     * Generate output path
     *
     * @param  string|UploadedFile  $image
     * @param  string  $suffix
     * @return string
     */
    private function generateOutputPath($image, string $suffix): string
    {
        $timestamp = time();
        $random = Str::random(8);
        
        if ($image instanceof UploadedFile) {
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
        } else {
            $originalName = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
        }
        
        $slug = Str::slug($originalName);
        
        return "processed/{$slug}_{$suffix}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get format from extension
     *
     * @param  string  $extension
     * @return string
     */
    private function getFormatFromExtension(string $extension): string
    {
        $formatMap = [
            'jpg' => 'jpg',
            'jpeg' => 'jpg',
            'png' => 'png',
            'gif' => 'gif',
            'webp' => 'webp',
            'bmp' => 'bmp',
            'svg' => 'svg',
        ];
        
        return $formatMap[strtolower($extension)] ?? 'jpg';
    }

    /**
     * Handle image without Intervention Image
     *
     * @param  string|UploadedFile  $image
     * @param  string  $disk
     * @param  string|null  $outputPath
     * @return string|null
     */
    private function handleImageWithoutIntervention($image, string $disk, ?string $outputPath): ?string
    {
        // Simply copy the image if Intervention Image is not available
        $imageContent = $this->getImageContent($image, $disk);
        
        if (!$imageContent) {
            return null;
        }
        
        $outputPath = $outputPath ?? $this->generateOutputPath($image, 'copy');
        Storage::disk($disk)->put($outputPath, $imageContent);
        
        return $outputPath;
    }

    /**
     * Get dimensions without Intervention Image
     *
     * @param  string|UploadedFile  $image
     * @param  string  $disk
     * @return array|null
     */
    private function getDimensionsWithoutIntervention($image, string $disk): ?array
    {
        $imagePath = null;
        
        if ($image instanceof UploadedFile) {
            $imagePath = $image->getRealPath();
        } elseif (Storage::disk($disk)->exists($image)) {
            $tempPath = tempnam(sys_get_temp_dir(), 'img');
            file_put_contents($tempPath, Storage::disk($disk)->get($image));
            $imagePath = $tempPath;
        } elseif (file_exists($image)) {
            $imagePath = $image;
        }
        
        if (!$imagePath) {
            return null;
        }
        
        $size = getimagesize($imagePath);
        
        if (!$size) {
            return null;
        }
        
        return [
            'width' => $size[0],
            'height' => $size[1],
            'aspect_ratio' => $size[0] / $size[1],
        ];
    }

    /**
     * Generate image placeholder
     *
     * @param  int  $width
     * @param  int  $height
     * @param  string  $backgroundColor
     * @param  string  $textColor
     * @param  string  $text
     * @param  string  $disk
     * @param  string  $path
     * @return string|null
     */
    public function generatePlaceholder(int $width = 300, int $height = 200, string $backgroundColor = 'cccccc', string $textColor = '000000', string $text = '', string $disk = 'public', string $path = 'placeholders'): ?string
    {
        if (!$this->isInterventionImageAvailable()) {
            return null;
        }
        
        $imageManager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        $img = $imageManager->canvas($width, $height, '#' . $backgroundColor);
        
        if (!empty($text)) {
            $img->text($text, $width / 2, $height / 2, function ($font) use ($textColor) {
                $font->file($this->getDefaultFontPath());
                $font->size(24);
                $font->color('#' . $textColor);
                $font->align('center');
                $font->valign('middle');
            });
        } else {
            $sizeText = "{$width} x {$height}";
            $img->text($sizeText, $width / 2, $height / 2, function ($font) use ($textColor) {
                $font->file($this->getDefaultFontPath());
                $font->size(18);
                $font->color('#' . $textColor);
                $font->align('center');
                $font->valign('middle');
            });
        }
        
        $filename = "placeholder_{$width}x{$height}_" . time() . '_' . Str::random(8) . '.png';
        $fullPath = rtrim($path, '/') . '/' . $filename;
        
        $content = $img->encode('png');
        Storage::disk($disk)->put($fullPath, $content);
        
        return $fullPath;
    }

    /**
     * Get default font path
     *
     * @return string
     */
    private function getDefaultFontPath(): string
    {
        // Try to find a system font
        $possibleFonts = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/tahoma.ttf',
        ];
        
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        // Return a fallback
        return __DIR__ . '/../../resources/fonts/arial.ttf';
    }
}
