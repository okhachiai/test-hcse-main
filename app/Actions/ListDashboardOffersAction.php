<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Pagination;
use App\Repositories\OfferRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

readonly class ListDashboardOffersAction
{
    public function __construct(
        private OfferRepository $offerRepository
    ) {}

    public function execute(Request $request, int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return $this->offerRepository->getFiltered(
            state: $request->query('state'),
            name: $request->query('name'),
            slug: $request->query('slug'),
            userId: $request->user()->id,
            perPage: $perPage
        );
    }
}
