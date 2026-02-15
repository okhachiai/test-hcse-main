<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    private const string CACHE_VERSION_KEY = 'api:offers:version';

    public function created(Product $product): void
    {
        Cache::increment(self::CACHE_VERSION_KEY);
    }

    public function updated(Product $product): void
    {
        Cache::increment(self::CACHE_VERSION_KEY);
    }

    public function deleted(Product $product): void
    {
        Cache::increment(self::CACHE_VERSION_KEY);
    }
}
