<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Override;
use Tests\TestCase;

class GenerateOpenApiDocsCommandTest extends TestCase
{
    private string $outputDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->outputDir = storage_path('framework/testing');
        File::ensureDirectoryExists($this->outputDir);
    }

    #[Override]
    protected function tearDown(): void
    {
        $paths = [
            $this->outputDir.'/openapi-test.json',
            $this->outputDir.'/openapi-test.yaml',
            $this->outputDir.'/custom-openapi.json',
        ];
        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }
        parent::tearDown();
    }

    public function test_command_exits_successfully(): void
    {
        $exitCode = Artisan::call('openapi:generate', [
            '--output' => 'storage/framework/testing/openapi-test.json',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($this->outputDir.'/openapi-test.json');
    }

    public function test_command_generates_valid_json_openapi_spec(): void
    {
        $outputPath = $this->outputDir.'/openapi-test.json';

        Artisan::call('openapi:generate', [
            '--output' => 'storage/framework/testing/openapi-test.json',
        ]);

        $this->assertFileExists($outputPath);
        $content = File::get($outputPath);
        $decoded = json_decode($content, true);
        $this->assertNotNull($decoded, 'Generated file must be valid JSON');
        $this->assertArrayHasKey('openapi', $decoded);
        $this->assertArrayHasKey('info', $decoded);
        $this->assertArrayHasKey('paths', $decoded);
        $this->assertStringStartsWith('3.', (string) $decoded['openapi']);
        $this->assertSame('HelloCSE API', $decoded['info']['title'] ?? null);
    }

    public function test_command_generates_yaml_when_format_option_is_yaml(): void
    {
        $outputPath = $this->outputDir.'/openapi-test.yaml';

        Artisan::call('openapi:generate', [
            '--output' => 'storage/framework/testing/openapi-test.yaml',
            '--format' => 'yaml',
        ]);

        $this->assertFileExists($outputPath);
        $content = File::get($outputPath);
        $this->assertStringContainsString('openapi:', $content);
        $this->assertStringContainsString('HelloCSE API', $content);
        $this->assertStringContainsString('paths:', $content);
    }

    public function test_command_uses_custom_output_path(): void
    {
        $customPath = $this->outputDir.'/custom-openapi.json';

        Artisan::call('openapi:generate', [
            '--output' => 'storage/framework/testing/custom-openapi.json',
        ]);

        $this->assertFileExists($customPath);
        $content = File::get($customPath);
        $decoded = json_decode($content, true);
        $this->assertNotNull($decoded);
        $this->assertArrayHasKey('info', $decoded);
    }
}
