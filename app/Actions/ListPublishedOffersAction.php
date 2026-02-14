<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Resources\OfferResource;
use App\Repositories\OfferRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListPublishedOffersAction
{
    public function __construct(
        private readonly OfferRepository $offerRepository
    ) {}

    public function execute(int $perPage = 15): AnonymousResourceCollection
    {
        $offers = $this->offerRepository->getPublishedPaginated($perPage);

        return OfferResource::collection($offers);
    }
}
