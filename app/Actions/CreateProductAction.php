<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\StoreProductRequest;
use App\Models\Offer;
use App\Models\Product;
use App\Services\ImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class CreateProductAction
{
    public function __construct(
        private ImageStorage $imageStorage,
        private Redirector $redirector
    ) {}

    public function execute(StoreProductRequest $request, int $offerId): RedirectResponse
    {
        $offer = Offer::findOrFail($offerId);

        $data = [
            'name' => $request->validated('name'),
            'sku' => $request->validated('sku'),
            'image' => $this->imageStorage->store($request->file('image'), 'products'),
            'price' => $request->validated('price'),
            'state' => $request->validated('state'),
        ];

        $product = new Product($data);
        $product->offer_id = $offer->id;
        $product->save();

        return $this->redirector
            ->route('offers.products.index', $offer->id)
            ->with('status', 'Produit créé avec succès.');
    }
}
