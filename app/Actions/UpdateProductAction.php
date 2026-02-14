<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\UpdateProductRequest;
use App\Models\Offer;
use App\Services\ImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class UpdateProductAction
{
    public function __construct(
        private ImageStorage $imageStorage,
        private Redirector $redirector
    ) {}

    public function execute(UpdateProductRequest $request, int $offerId, int $productId): RedirectResponse
    {
        $offer = Offer::findOrFail($offerId);
        $product = $offer->products()->findOrFail($productId);

        $data = [
            'name' => $request->validated('name'),
            'sku' => $request->validated('sku'),
            'price' => $request->validated('price'),
            'state' => $request->validated('state'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->replace($product->image, $request->file('image'), 'products');
        }

        $product->update($data);

        return $this->redirector
            ->route('offers.products.index', $offer->id)
            ->with('status', 'Produit mis à jour avec succès.');
    }
}
