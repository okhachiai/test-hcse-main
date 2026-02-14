<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\DashboardData;
use App\Enums\OfferState;
use App\Enums\Pagination;
use App\Repositories\OfferRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

readonly class ListDashboardOffersAction
{
    public function __construct(
        private OfferRepository $offerRepository
    ) {}

    public function execute(Request $request, int $perPage = Pagination::DefaultPerPage->value): DashboardData
    {
        $offers = $this->offerRepository->getFiltered(
            state: $request->query('state'),
            name: $request->query('name'),
            slug: $request->query('slug'),
            userId: (auth()->user() ?? throw new AuthenticationException)->id,
            perPage: $perPage
        );

        return new DashboardData(
            offers: $offers,
            filterParams: $this->buildFilterParams($request),
            activeState: $request->filled('state') ? $request->query('state') : null,
            offerStates: OfferState::labels(),
        );
    }

    /**
     * @return array<string, string>
     */
    private function buildFilterParams(Request $request): array
    {
        return array_filter([
            'name' => $request->query('name'),
            'slug' => $request->query('slug'),
        ], fn (mixed $v): bool => $v !== null && $v !== '');
    }
}
