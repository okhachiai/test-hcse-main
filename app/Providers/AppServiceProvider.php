<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Contracts\OfferRepositoryInterface;
use App\Application\Contracts\ProductRepositoryInterface;
use App\Infrastructure\Repositories\OfferRepository;
use App\Infrastructure\Repositories\ProductRepository;
use App\Models\Offer;
use App\Models\Product;
use App\Observers\OfferObserver;
use App\Observers\ProductObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->bind(OfferRepositoryInterface::class, OfferRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        RateLimiter::for('api', fn () => Limit::perMinute(60)->by(request()->user()?->id ?: request()->ip()));

        Offer::observe(OfferObserver::class);
        Product::observe(ProductObserver::class);
    }
}
