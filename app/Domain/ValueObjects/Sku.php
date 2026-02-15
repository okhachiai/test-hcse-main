<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Sku
{
    private const int MIN_LENGTH = 1;

    private const int MAX_LENGTH = 255;

    private const string PATTERN = '/^[a-zA-Z0-9_-]+$/';

    private function __construct(
        private string $value
    ) {}

    public static function fromString(string $value): self
    {
        $trimmed = trim($value);

        if (strlen($trimmed) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('SKU cannot be empty.');
        }

        if (strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('SKU cannot exceed %d characters.', self::MAX_LENGTH));
        }

        if (! preg_match(self::PATTERN, $trimmed)) {
            throw new InvalidArgumentException('SKU must contain only letters, numbers, hyphens and underscores.');
        }

        return new self($trimmed);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
