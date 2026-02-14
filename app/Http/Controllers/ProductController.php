<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Actions\CreateProductAction;
use App\Application\Actions\DeleteProductAction;
use App\Application\Actions\ListProductsAction;
use App\Application\Actions\UpdateProductAction;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(ListProductsAction $listProductsAction, Offer $offer): View
    {
        $this->authorize('manage', $offer);

        return view('products.index', [
            'offer' => $offer,
            'products' => $listProductsAction->execute($offer),
        ]);
    }

    public function create(Offer $offer): View
    {
        $this->authorize('manage', $offer);

        return view('products.create', [
            'offer' => $offer,
            'product' => new Product,
        ]);
    }

    public function store(StoreProductRequest $request, CreateProductAction $createProductAction, Offer $offer): RedirectResponse
    {
        $this->authorize('manage', $offer);

        $createProductAction->execute($request, $offer);

        return Redirect::route('offers.products.index', $offer)->with('status', 'Produit créé avec succès.');
    }

    public function edit(Offer $offer, Product $product): View
    {
        $this->authorize('manage', $product);

        return view('products.edit', ['offer' => $offer, 'product' => $product]);
    }

    public function update(UpdateProductRequest $request, UpdateProductAction $updateProductAction, Offer $offer, Product $product): RedirectResponse
    {
        $this->authorize('manage', $product);

        $updateProductAction->execute($request, $offer, $product);

        return Redirect::route('offers.products.index', $offer)->with('status', 'Produit mis à jour avec succès.');
    }

    public function destroy(DeleteProductAction $deleteProductAction, Offer $offer, Product $product): RedirectResponse
    {
        $this->authorize('manage', $product);

        $deleteProductAction->execute($product);

        return Redirect::route('offers.products.index', $offer)->with('status', 'Produit supprimé avec succès.');
    }
}
