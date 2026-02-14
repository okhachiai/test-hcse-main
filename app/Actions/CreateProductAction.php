<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\StoreProductRequest;
use App\Models\Offer;
use App\Repositories\ProductRepository;
use App\Services\ImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class CreateProductAction
{
    public function __construct(
        private ProductRepository $productRepository,
        private ImageStorage $imageStorage,
        private Redirector $redirector
    ) {}

    public function execute(StoreProductRequest $request, Offer $offer): RedirectResponse
    {
        $data = [
            'name' => $request->validated('name'),
            'sku' => $request->validated('sku'),
            'image' => $this->imageStorage->store($request->file('image'), 'products'),
            'price' => $request->validated('price'),
            'state' => $request->validated('state'),
        ];

        $this->productRepository->create($offer, $data);

        return $this->redirector
            ->route('offers.products.index', $offer)
            ->with('status', 'Produit créé avec succès.');
    }
}
