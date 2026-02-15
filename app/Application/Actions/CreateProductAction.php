<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Contracts\ProductRepositoryInterface;
use App\Http\Requests\StoreProductRequest;
use App\Infrastructure\Services\ImageStorage;
use App\Models\Offer;

readonly class CreateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ImageStorage $imageStorage
    ) {}

    public function execute(StoreProductRequest $request, Offer $offer): void
    {
        $data = [
            'name' => $request->validated('name'),
            'sku' => $request->validated('sku'),
            'image' => $this->imageStorage->store($request->file('image'), 'products'),
            'price' => $request->validated('price'),
            'state' => $request->validated('state'),
        ];

        $this->productRepository->create($offer, $data);
    }
}
