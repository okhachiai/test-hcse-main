<?php

declare(strict_types=1);

namespace App\Infrastructure\QueryServices;

use App\Domain\Enums\Pagination;
use App\Domain\StateRules\OfferStateRules;
use App\Domain\StateRules\ProductStateRules;
use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class OfferQueryService
{
    /**
     * Published offers for public API (with published products eager loaded).
     * Uses OfferStateRules::defaultScopeForApi() and ProductStateRules::defaultScopeForApi().
     *
     * @return LengthAwarePaginator<int, Offer>
     */
    public function listPublished(int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        $offerState = OfferStateRules::defaultScopeForApi();
        $productState = ProductStateRules::defaultScopeForApi();

        return Offer::query()
            ->where('state', $offerState)
            ->latest()
            ->with(['products' => fn ($q) => $q->where('state', $productState)])
            ->paginate($perPage);
    }

    /**
     * Filtered offers for backoffice dashboard (by user, state, name, slug).
     *
     * @return LengthAwarePaginator<int, Offer>
     */
    public function listForDashboard(
        int $userId,
        ?string $state = null,
        ?string $name = null,
        ?string $slug = null,
        int $perPage = Pagination::DefaultPerPage->value
    ): LengthAwarePaginator {
        return $this->buildDashboardQuery($userId, $state, $name, $slug)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return EloquentBuilder<Offer>
     */
    private function buildDashboardQuery(int $userId, ?string $state, ?string $name, ?string $slug): EloquentBuilder
    {
        $query = Offer::query()->where('user_id', $userId);

        if ($state !== null && $state !== '') {
            $query->ofState($state);
        }

        if ($name !== null && $name !== '') {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($slug !== null && $slug !== '') {
            $query->where('slug', 'like', "%{$slug}%");
        }

        return $query->latest();
    }
}
