<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Pagination;
use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class OfferRepository
{
    public function findOrFailWithProducts(int $id): Offer
    {
        return Offer::with('products')->findOrFail($id);
    }

    public function create(array $data): Offer
    {
        return Offer::create($data);
    }

    public function update(Offer $offer, array $data): bool
    {
        return $offer->update($data);
    }

    public function delete(int $id): bool
    {
        return Offer::where('id', $id)->delete() > 0;
    }

    public function getPublishedPaginated(int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return Offer::published()
            ->with(['products' => fn ($q) => $q->published()])
            ->paginate($perPage);
    }

    public function getFiltered(?string $state = null, ?string $name = null, ?string $slug = null, ?int $userId = null, int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return $this->buildFilteredQuery($state, $name, $slug, $userId)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return Builder<Offer>
     */
    private function buildFilteredQuery(?string $state = null, ?string $name = null, ?string $slug = null, ?int $userId = null): Builder
    {
        $query = Offer::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        if ($state !== null && $state !== '') {
            $query->ofState($state);
        }

        if ($name !== null && $name !== '') {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($slug !== null && $slug !== '') {
            $query->where('slug', 'like', "%{$slug}%");
        }

        return $query;
    }
}
