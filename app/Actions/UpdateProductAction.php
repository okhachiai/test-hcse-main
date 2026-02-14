<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Offer;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class UpdateProductAction
{
    public function __construct(
        private ProductRepository $productRepository,
        private ImageStorage $imageStorage,
        private Redirector $redirector
    ) {}

    public function execute(UpdateProductRequest $request, Offer $offer, Product $product): RedirectResponse
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

        return $this->redirector
            ->route('offers.products.index', $offer)
            ->with('status', 'Produit mis à jour avec succès.');
    }
}
