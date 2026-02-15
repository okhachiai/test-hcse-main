<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\OfferRepositoryInterface;
use App\Models\Offer;

class OfferRepository implements OfferRepositoryInterface
{
    public function findOrFailWithProducts(int $id): Offer
    {
        return Offer::with('products')->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Offer
    {
        return Offer::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Offer $offer, array $data): bool
    {
        return $offer->update($data);
    }

    public function delete(int $id): bool
    {
        return Offer::where('id', $id)->delete() > 0;
    }
}
