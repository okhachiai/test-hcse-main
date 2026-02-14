<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Offer;
use App\Repositories\OfferRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class DeleteOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository,
        private Redirector $redirector
    ) {}

    public function execute(Offer $offer): RedirectResponse
    {
        $this->offerRepository->delete($offer->id);

        return $this->redirector->route('dashboard');
    }
}
