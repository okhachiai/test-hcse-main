<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Offer;
use Illuminate\Support\Facades\Cache;

class OfferObserver
{
    private const string CACHE_VERSION_KEY = 'api:offers:version';

    public function created(Offer $offer): void
    {
        $this->invalidateApiOffersCache();
    }

    public function updated(Offer $offer): void
    {
        $this->invalidateApiOffersCache();
    }

    public function deleted(Offer $offer): void
    {
        $this->invalidateApiOffersCache();
    }

    private function invalidateApiOffersCache(): void
    {
        Cache::increment(self::CACHE_VERSION_KEY);
    }
}
