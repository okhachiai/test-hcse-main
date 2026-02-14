<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Pagination;
use App\Models\Offer;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class ListProductsAction
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function execute(Offer $offer, int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return $this->productRepository->getForOfferPaginated($offer, $perPage);
    }
}
