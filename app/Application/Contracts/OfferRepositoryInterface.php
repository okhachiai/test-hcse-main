<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Models\Offer;

interface OfferRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Offer;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Offer $offer, array $data): bool;

    public function delete(int $id): bool;

    public function findOrFailWithProducts(int $id): Offer;
}
