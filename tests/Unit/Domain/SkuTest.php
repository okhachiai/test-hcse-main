<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\ValueObjects\Sku;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SkuTest extends TestCase
{
    public function test_from_string_accepts_valid_sku(): void
    {
        $sku = Sku::fromString('ABC-123_xyz');

        $this->assertSame('ABC-123_xyz', $sku->value());
    }

    public function test_from_string_trims_whitespace(): void
    {
        $sku = Sku::fromString('  SKU123  ');

        $this->assertSame('SKU123', $sku->value());
    }

    public function test_from_string_rejects_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SKU cannot be empty');

        Sku::fromString('');
    }

    public function test_from_string_rejects_whitespace_only(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SKU cannot be empty');

        Sku::fromString('   ');
    }

    public function test_from_string_rejects_invalid_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SKU must contain only letters, numbers, hyphens and underscores');

        Sku::fromString('SKU WITH SPACES');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $sku1 = Sku::fromString('ABC');
        $sku2 = Sku::fromString('ABC');

        $this->assertTrue($sku1->equals($sku2));
    }

    public function test_equals_returns_false_for_different_value(): void
    {
        $sku1 = Sku::fromString('ABC');
        $sku2 = Sku::fromString('XYZ');

        $this->assertFalse($sku1->equals($sku2));
    }
}
