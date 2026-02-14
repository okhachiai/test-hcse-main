<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Data\DashboardData;
use App\Domain\Enums\OfferState;
use App\Domain\Enums\Pagination;
use App\Infrastructure\QueryServices\OfferQueryService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

readonly class ListDashboardOffersAction
{
    public function __construct(
        private OfferQueryService $offerQueryService
    ) {}

    public function execute(Request $request, int $perPage = Pagination::DefaultPerPage->value): DashboardData
    {
        $offers = $this->offerQueryService->listForDashboard(
            userId: (auth()->user() ?? throw new AuthenticationException)->id,
            state: $request->query('state'),
            name: $request->query('name'),
            slug: $request->query('slug'),
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
