<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Contracts\ProductRepositoryInterface;
use App\Http\Requests\UpdateProductRequest;
use App\Infrastructure\Services\ImageStorage;
use App\Models\Offer;
use App\Models\Product;

readonly class UpdateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ImageStorage $imageStorage
    ) {}

    public function execute(UpdateProductRequest $request, Offer $offer, Product $product): void
    {
        $data = [
            'name' => $request->validated('name'),
            'sku' => $request->validated('sku'),
            'price' => $request->validated('price'),
            'state' => $request->validated('state'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->replace($product->image, $request->file('image'), 'products');
        }

        $this->productRepository->update($product, $data);
    }
}
