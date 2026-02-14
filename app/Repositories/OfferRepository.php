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
}
