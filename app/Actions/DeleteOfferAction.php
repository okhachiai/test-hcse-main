<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Offer;
use App\Repositories\OfferRepository;

readonly class DeleteOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository
    ) {}

    public function execute(Offer $offer): void
    {
        $this->offerRepository->delete($offer->id);
    }
}
