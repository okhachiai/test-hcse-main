<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\Enums\Pagination;
use App\Http\Resources\OfferResource;
use App\Infrastructure\QueryServices\OfferQueryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListPublishedOffersAction
{
    public function __construct(
        private readonly OfferQueryService $offerQueryService
    ) {}

    public function execute(int $perPage = Pagination::DefaultPerPage->value): AnonymousResourceCollection
    {
        $offers = $this->offerQueryService->listPublished($perPage);

        return OfferResource::collection($offers);
    }
}
