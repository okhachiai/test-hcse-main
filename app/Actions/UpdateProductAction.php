<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Offer;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ImageStorage;

readonly class UpdateProductAction
{
    public function __construct(
        private ProductRepository $productRepository,
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
