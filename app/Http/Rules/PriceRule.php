<?php

declare(strict_types=1);

namespace App\Http\Rules;

use App\Domain\ValueObjects\Price;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

class PriceRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $stringValue = is_scalar($value) ? (string) $value : '';

        try {
            Price::fromString($stringValue);
        } catch (InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
