<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Pagination;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * @return Collection<int, Product>
     */
    public function getForOffer(Offer $offer): Collection
    {
        $collection = $offer->products()->latest()->get();

        /** @var Collection<int, Product> $collection */
        return $collection;
    }

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function getForOfferPaginated(Offer $offer, int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return $offer->products()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Offer $offer, array $data): Product
    {
        $product = new Product($data);
        $key = $offer->getKey();
        $product->offer_id = max(0, is_int($key) ? $key : (is_string($key) ? (int) $key : 0));
        $product->save();

        return $product;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
