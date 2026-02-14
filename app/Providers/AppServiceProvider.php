<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Contracts\OfferRepositoryInterface;
use App\Application\Contracts\ProductRepositoryInterface;
use App\Infrastructure\Repositories\OfferRepository;
use App\Infrastructure\Repositories\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;
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
    }
}
