<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateOfferAction;
use App\Actions\DeleteOfferAction;
use App\Actions\UpdateOfferAction;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function create(): View
    {
        return view('offers.create');
    }

    public function store(StoreOfferRequest $request, CreateOfferAction $createOfferAction): RedirectResponse
    {
        return $createOfferAction->execute($request);
    }

    public function edit(Offer $offer): View
    {
        $this->authorize('update', $offer);

        return view('offers.edit', ['offer' => $offer]);
    }

    public function update(UpdateOfferRequest $request, UpdateOfferAction $updateOfferAction, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        return $updateOfferAction->execute($request, $offer);
    }

    public function destroy(DeleteOfferAction $deleteOfferAction, Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        return $deleteOfferAction->execute($offer);
    }

    public function show(Offer $offer): View
    {
        $this->authorize('view', $offer);
        $offer->load('products');

        return view('offers.show', ['offer' => $offer]);
    }
}
