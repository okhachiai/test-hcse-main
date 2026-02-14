<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Pagination;
use App\Http\Resources\OfferResource;
use App\Repositories\OfferRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListPublishedOffersAction
{
    public function __construct(
        private readonly OfferRepository $offerRepository
    ) {}

    public function execute(int $perPage = Pagination::DefaultPerPage->value): AnonymousResourceCollection
    {
        $offers = $this->offerRepository->getPublishedPaginated($perPage);

        return OfferResource::collection($offers);
    }
}
