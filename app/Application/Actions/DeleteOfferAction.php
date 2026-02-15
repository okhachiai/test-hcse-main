<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Contracts\OfferRepositoryInterface;
use App\Models\Offer;

readonly class DeleteOfferAction
{
    public function __construct(
        private OfferRepositoryInterface $offerRepository
    ) {}

    public function execute(Offer $offer): void
    {
        $this->offerRepository->delete($offer->id);
    }
}
