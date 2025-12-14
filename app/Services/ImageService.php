<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Upload and process product image
     * 
     * @return array{filename: string, path: string, path_thumb: string, path_medium: string, mime_type: string, file_size: int}
     */
    public function uploadProductImage(UploadedFile $file, int $productId): array
    {
        $filename = $this->generateFilename($file);
        $basePath = "products/{$productId}";

        // Create directories if they don't exist
        Storage::disk('public')->makeDirectory($basePath);
        Storage::disk('public')->makeDirectory("{$basePath}/thumbs");
        Storage::disk('public')->makeDirectory("{$basePath}/medium");

        // Store original (optimized)
        $originalPath = "{$basePath}/{$filename}";
        $image = $this->manager->read($file->getRealPath());

        // Resize if too large (max 1920px width)
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        // Optimize and save
        $encoded = $image->toWebp(85);
        Storage::disk('public')->put($originalPath, $encoded);

        // Create thumbnail (200x200)
        $thumbPath = "{$basePath}/thumbs/{$filename}";
        $thumb = $this->manager->read($file->getRealPath());
        $thumb->cover(200, 200);
        $thumbEncoded = $thumb->toWebp(80);
        Storage::disk('public')->put($thumbPath, $thumbEncoded);

        // Create medium (800px width)
        $mediumPath = "{$basePath}/medium/{$filename}";
        $medium = $this->manager->read($file->getRealPath());
        if ($medium->width() > 800) {
            $medium->scale(width: 800);
        }
        $mediumEncoded = $medium->toWebp(85);
        Storage::disk('public')->put($mediumPath, $mediumEncoded);

        return [
            'filename' => $filename,
            'path' => $originalPath,
            'path_thumb' => $thumbPath,
            'path_medium' => $mediumPath,
            'mime_type' => 'image/webp',
            'file_size' => strlen($encoded),
        ];
    }

    /**
     * Upload category or brand image
     */
    public function uploadCategoryImage(UploadedFile $file, string $type = 'category'): string
    {
        $filename = $this->generateFilename($file);
        $path = "{$type}s/{$filename}";

        $image = $this->manager->read($file->getRealPath());

        // Resize to 400x400 for category/brand images
        $image->cover(400, 400);

        $encoded = $image->toWebp(85);
        Storage::disk('public')->put($path, $encoded);

        return $path;
    }

    /**
     * Delete product images
     */
    public function deleteProductImages(int $productId): void
    {
        Storage::disk('public')->deleteDirectory("products/{$productId}");
    }

    /**
     * Delete single image
     */
    public function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = 'webp'; // We convert all images to webp
        return Str::uuid() . '.' . $extension;
    }

    /**
     * Validate image file
     */
    public function validate(UploadedFile $file): bool
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        if ($file->getSize() > $maxSize) {
            return false;
        }

        return true;
    }

    /**
     * Get placeholder image URL
     */
    public function getPlaceholder(string $type = 'product'): string
    {
        return asset("images/placeholder-{$type}.png");
    }
}
