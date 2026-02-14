<?php

declare(strict_types=1);

namespace App\Http\Rules;

use App\Domain\Enums\OfferState;
use App\Domain\Specifications\OfferCanBePublishedSpecification;
use App\Models\Offer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OfferCanBePublishedRule implements ValidationRule
{
    public function __construct(
        private readonly Offer $offer
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== OfferState::Published->value) {
            return;
        }

        if (! (new OfferCanBePublishedSpecification)->isSatisfiedBy($this->offer)) {
            $fail('This offer cannot be published from its current state.');
        }
    }
}
