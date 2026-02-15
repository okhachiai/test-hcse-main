<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class DashboardData
{
    /**
     * @param  LengthAwarePaginator<int, Offer>  $offers
     * @param  array<string, string>  $filterParams
     * @param  array<string, string>  $offerStates
     */
    public function __construct(
        public LengthAwarePaginator $offers,
        public array $filterParams,
        public ?string $activeState,
        public array $offerStates,
    ) {}
}
