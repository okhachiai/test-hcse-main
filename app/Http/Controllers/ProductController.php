<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateProductAction;
use App\Actions\UpdateProductAction;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(string $offerId): View
    {
        $offer = Offer::findOrFail($offerId);
        $this->authorize('view', $offer);

        $products = $offer->products()->latest()->get();

        return view('products.index', ['offer' => $offer, 'products' => $products]);
    }

    public function create(string $offerId): View
    {
        $offer = Offer::findOrFail($offerId);
        $this->authorize('update', $offer);

        $product = new Product;

        return view('products.create', ['offer' => $offer, 'product' => $product]);
    }

    public function store(StoreProductRequest $request, CreateProductAction $createProductAction, string $offerId): RedirectResponse
    {
        $offer = Offer::findOrFail($offerId);
        $this->authorize('update', $offer);

        return $createProductAction->execute($request, (int) $offerId);
    }

    public function edit(string $offerId, string $productId): View
    {
        $offer = Offer::findOrFail($offerId);
        $this->authorize('update', $offer);

        $product = $offer->products()->findOrFail($productId);

        return view('products.edit', ['offer' => $offer, 'product' => $product]);
    }

    public function update(UpdateProductRequest $request, UpdateProductAction $updateProductAction, string $offerId, string $productId): RedirectResponse
    {
        $offer = Offer::findOrFail($offerId);
        $this->authorize('update', $offer);

        return $updateProductAction->execute($request, (int) $offerId, (int) $productId);
    }

    public function destroy(string $offerId, string $productId): RedirectResponse
    {
        $offer = Offer::findOrFail($offerId);
        $this->authorize('update', $offer);

        $product = $offer->products()->findOrFail($productId);
        $product->delete();

        return redirect()
            ->route('offers.products.index', $offer->id)
            ->with('status', 'Produit supprimé avec succès.');
    }
}
