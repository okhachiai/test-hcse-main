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
    public function index(Offer $offer): View
    {
        $this->authorize('view', $offer);

        $products = $offer->products()->latest()->get();

        return view('products.index', ['offer' => $offer, 'products' => $products]);
    }

    public function create(Offer $offer): View
    {
        $this->authorize('update', $offer);

        $product = new Product;

        return view('products.create', ['offer' => $offer, 'product' => $product]);
    }

    public function store(StoreProductRequest $request, CreateProductAction $createProductAction, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        return $createProductAction->execute($request, $offer);
    }

    public function edit(Offer $offer, Product $product): View
    {
        $this->authorize('update', $offer);

        return view('products.edit', ['offer' => $offer, 'product' => $product]);
    }

    public function update(UpdateProductRequest $request, UpdateProductAction $updateProductAction, Offer $offer, Product $product): RedirectResponse
    {
        $this->authorize('update', $offer);

        return $updateProductAction->execute($request, $offer, $product);
    }

    public function destroy(Offer $offer, Product $product): RedirectResponse
    {
        $this->authorize('update', $offer);

        $product->delete();

        return redirect()
            ->route('offers.products.index', $offer)
            ->with('status', 'Produit supprimé avec succès.');
    }
}
