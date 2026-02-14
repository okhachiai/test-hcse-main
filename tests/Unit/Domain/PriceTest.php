<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\ValueObjects\Price;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function test_from_float_accepts_valid_price(): void
    {
        $price = Price::fromFloat(19.99);

        $this->assertSame(19.99, $price->value());
    }

    public function test_from_float_accepts_zero(): void
    {
        $price = Price::fromFloat(0.0);

        $this->assertSame(0.0, $price->value());
    }

    public function test_from_float_rejects_negative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price cannot be negative');

        Price::fromFloat(-5.00);
    }

    public function test_from_string_accepts_valid_number(): void
    {
        $price = Price::fromString('29.99');

        $this->assertSame(29.99, $price->value());
    }

    public function test_from_string_rejects_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price must be a valid number');

        Price::fromString('not-a-number');
    }

    public function test_equals_returns_true_for_same_amount(): void
    {
        $price1 = Price::fromFloat(10.50);
        $price2 = Price::fromFloat(10.50);

        $this->assertTrue($price1->equals($price2));
    }

    public function test_equals_returns_false_for_different_amount(): void
    {
        $price1 = Price::fromFloat(10.00);
        $price2 = Price::fromFloat(20.00);

        $this->assertFalse($price1->equals($price2));
    }
}
