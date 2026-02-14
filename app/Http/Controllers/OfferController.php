<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateOfferAction;
use App\Actions\DeleteOfferAction;
use App\Actions\ListProductsAction;
use App\Actions\UpdateOfferAction;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function create(): View
    {
        return view('offers.create');
    }

    public function store(StoreOfferRequest $request, CreateOfferAction $createOfferAction): RedirectResponse
    {
        $createOfferAction->execute($request);

        return Redirect::route('dashboard');
    }

    public function edit(Offer $offer): View
    {
        $this->authorize('manage', $offer);

        return view('offers.edit', ['offer' => $offer]);
    }

    public function update(UpdateOfferRequest $request, UpdateOfferAction $updateOfferAction, Offer $offer): RedirectResponse
    {
        $this->authorize('manage', $offer);

        $updateOfferAction->execute($request, $offer);

        return Redirect::route('dashboard');
    }

    public function destroy(DeleteOfferAction $deleteOfferAction, Offer $offer): RedirectResponse
    {
        $this->authorize('manage', $offer);

        $deleteOfferAction->execute($offer);

        return Redirect::route('dashboard');
    }

    public function show(ListProductsAction $listProductsAction, Offer $offer): View
    {
        $this->authorize('manage', $offer);

        return view('offers.show', [
            'offer' => $offer,
            'products' => $listProductsAction->execute($offer),
        ]);
    }
}
