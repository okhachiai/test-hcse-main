<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Contracts\ProductRepositoryInterface;
use App\Domain\Enums\Pagination;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class ListProductsAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function execute(Offer $offer, int $perPage = Pagination::DefaultPerPage->value): LengthAwarePaginator
    {
        return $this->productRepository->getForOfferPaginated($offer, $perPage);
    }
}
