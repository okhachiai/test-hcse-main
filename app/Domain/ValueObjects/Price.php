<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Price
{
    private function __construct(
        private float $amount
    ) {}

    public static function fromFloat(float $amount): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        return new self(round($amount, 2));
    }

    public static function fromString(string $amount): self
    {
        $value = filter_var($amount, FILTER_VALIDATE_FLOAT);

        if ($value === false) {
            throw new InvalidArgumentException('Price must be a valid number.');
        }

        return self::fromFloat($value);
    }

    public function value(): float
    {
        return $this->amount;
    }

    public function equals(self $other): bool
    {
        return abs($this->amount - $other->amount) < 0.001;
    }
}
