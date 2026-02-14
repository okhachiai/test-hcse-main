<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateProductAction;
use App\Actions\DeleteProductAction;
use App\Actions\ListProductsAction;
use App\Actions\UpdateProductAction;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
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

        return $createProductAction->execute($request, $offer);
    }

    public function edit(Offer $offer, Product $product): View
    {
        $this->authorize('manage', $offer);

        return view('products.edit', ['offer' => $offer, 'product' => $product]);
    }

    public function update(UpdateProductRequest $request, UpdateProductAction $updateProductAction, Offer $offer, Product $product): RedirectResponse
    {
        $this->authorize('manage', $offer);

        return $updateProductAction->execute($request, $offer, $product);
    }

    public function destroy(DeleteProductAction $deleteProductAction, Offer $offer, Product $product): RedirectResponse
    {
        $this->authorize('manage', $offer);

        return $deleteProductAction->execute($offer, $product);
    }
}
