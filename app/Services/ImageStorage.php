<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageStorage
{
    private const string DISK = 'public';

    private const array PLACEHOLDER_PATHS = [
        'offers/placeholder.jpg',
        'products/placeholder.jpg',
    ];

    public function store(UploadedFile $file, string $directory): string
    {
        $filename = Str::uuid()->toString().'.'.$file->extension();

        $path = $file->storeAs($directory, $filename, ['disk' => self::DISK]);

        if ($path === false) {
            throw new RuntimeException("Failed to store file in directory: {$directory}");
        }

        return $path;
    }

    public function replace(?string $oldPath, UploadedFile $file, string $directory): string
    {
        $newPath = $this->store($file, $directory);

        $this->delete($oldPath);

        return $newPath;
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (in_array($path, self::PLACEHOLDER_PATHS, true)) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }
}
