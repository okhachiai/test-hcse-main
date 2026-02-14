<?php

declare(strict_types=1);

namespace App\Http\Rules;

use App\Domain\ValueObjects\Sku;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

class SkuRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        try {
            Sku::fromString($value);
        } catch (InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
