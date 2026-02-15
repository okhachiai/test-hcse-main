<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\Enums\Pagination;
use App\Http\Resources\OfferResource;
use App\Infrastructure\QueryServices\OfferQueryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ListPublishedOffersAction
{
    private const string CACHE_VERSION_KEY = 'api:offers:version';

    private const int CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly OfferQueryService $offerQueryService
    ) {}

    public function execute(int $page = 1, int $perPage = Pagination::DefaultPerPage->value): AnonymousResourceCollection
    {
        $raw = Cache::get(self::CACHE_VERSION_KEY, 0);
        $version = is_int($raw) ? $raw : 0;
        $key = "api:offers:v{$version}:page:{$page}:per_page:{$perPage}";

        $offers = Cache::remember($key, self::CACHE_TTL_SECONDS, fn () => $this->offerQueryService->listPublished($page, $perPage));

        return OfferResource::collection($offers);
    }
}
