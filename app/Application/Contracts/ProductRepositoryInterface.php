<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Offer $offer, array $data): Product;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): bool;

    public function delete(Product $product): bool;

    /**
     * @return Collection<int, Product>
     */
    public function getForOffer(Offer $offer): Collection;

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function getForOfferPaginated(Offer $offer, int $perPage): LengthAwarePaginator;
}
