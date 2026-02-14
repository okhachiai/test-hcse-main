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

    public function getForOfferPaginated(Offer $offer, int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return $offer->products()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(Offer $offer, array $data): Product
    {
        $product = new Product($data);
        $product->offer_id = $offer->id;
        $product->save();

        return $product;
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
