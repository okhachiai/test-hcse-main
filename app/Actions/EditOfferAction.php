<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Offer;
use App\Repositories\OfferRepository;

readonly class EditOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository
    ) {}

    public function execute(int $offerId): Offer
    {
        return $this->offerRepository->findOrFail($offerId);
    }
}
