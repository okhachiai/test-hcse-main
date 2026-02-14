<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Offer;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class DeleteProductAction
{
    public function __construct(
        private ProductRepository $productRepository,
        private Redirector $redirector
    ) {}

    public function execute(Offer $offer, Product $product): RedirectResponse
    {
        $this->productRepository->delete($product);

        return $this->redirector
            ->route('offers.products.index', $offer)
            ->with('status', 'Produit supprimé avec succès.');
    }
}
