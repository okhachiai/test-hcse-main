<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\ImageStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageStorageTest extends TestCase
{
    private ImageStorage $imageStorage;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->imageStorage = new ImageStorage();
    }

    public function test_store_creates_file_and_returns_path(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg');

        $path = $this->imageStorage->store($file, 'offers');

        $this->assertStringStartsWith('offers/', $path);
        $this->assertStringEndsWith('.jpg', $path);
        $this->assertMatchesRegularExpression('#^offers/[a-f0-9-]{36}\.jpg$#', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_replace_stores_new_file_deletes_old_and_returns_new_path(): void
    {
        $oldFile = UploadedFile::fake()->image('old.jpg')->size(100);
        $oldPath = $this->imageStorage->store($oldFile, 'offers');

        $this->assertTrue(Storage::disk('public')->exists($oldPath));

        $newFile = UploadedFile::fake()->image('new.png')->size(100);
        $newPath = $this->imageStorage->replace($oldPath, $newFile, 'offers');

        $this->assertStringStartsWith('offers/', $newPath);
        $this->assertStringEndsWith('.png', $newPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_replace_with_null_old_path_only_stores_new_file(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg');
        $path = $this->imageStorage->replace(null, $file, 'products');

        $this->assertStringStartsWith('products/', $path);
        Storage::disk('public')->assertExists($path);
    }
}
