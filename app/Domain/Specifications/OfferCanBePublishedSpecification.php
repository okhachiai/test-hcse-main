<?php

declare(strict_types=1);

namespace App\Domain\Specifications;

use App\Domain\Enums\OfferState;
use App\Models\Offer;

class OfferCanBePublishedSpecification
{
    public function isSatisfiedBy(Offer $offer): bool
    {
        /** @var OfferState $state */
        $state = $offer->state;

        return $state === OfferState::Published
            || $state->canTransitionTo(OfferState::Published);
    }
}
