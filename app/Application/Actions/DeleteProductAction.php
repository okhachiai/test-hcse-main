<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Contracts\ProductRepositoryInterface;
use App\Models\Product;

readonly class DeleteProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
