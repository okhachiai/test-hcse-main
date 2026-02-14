<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateOfferAction;
use App\Actions\DeleteOfferAction;
use App\Actions\EditOfferAction;
use App\Actions\ShowOfferAction;
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

    public function edit(EditOfferAction $editOfferAction, string $offerId): View
    {
        $offer = $editOfferAction->execute((int) $offerId);
        $this->authorize('update', $offer);

        return view('offers.edit', ['offer' => $offer]);
    }

    public function update(UpdateOfferRequest $request, UpdateOfferAction $updateOfferAction, string $offerId): RedirectResponse
    {
        $offer = Offer::findOrFail((int) $offerId);
        $this->authorize('update', $offer);

        return $updateOfferAction->execute($request, (int) $offerId);
    }

    public function destroy(DeleteOfferAction $deleteOfferAction, string $offerId): RedirectResponse
    {
        $offer = Offer::findOrFail((int) $offerId);
        $this->authorize('delete', $offer);

        return $deleteOfferAction->execute((int) $offerId);
    }

    public function show(ShowOfferAction $showOfferAction, string $offerId): View
    {
        $offer = $showOfferAction->execute((int) $offerId);
        $this->authorize('view', $offer);

        return view('offers.show', ['offer' => $offer]);
    }
}
