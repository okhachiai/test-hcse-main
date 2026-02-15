<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Product;
use App\Repositories\ProductRepository;

readonly class DeleteProductAction
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    public function execute(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
