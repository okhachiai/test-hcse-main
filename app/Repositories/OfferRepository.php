<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OfferRepository
{
    public function getPublishedPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Offer::ofState('published')
            ->with(['products' => fn ($q) => $q->where('state', 'published')])
            ->paginate($perPage);
    }

    /**
     * @return LengthAwarePaginator<Offer>
     */
    public function getFiltered(?string $state = null, ?string $name = null, ?string $slug = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Offer::query();

        if ($state !== null && $state !== '') {
            $query->ofState($state);
        }

        if ($name !== null && $name !== '') {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($slug !== null && $slug !== '') {
            $query->where('slug', 'like', "%{$slug}%");
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
