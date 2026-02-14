<?php

declare(strict_types=1);

namespace App\Actions;

use App\Repositories\OfferRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class DeleteOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository,
        private Redirector $redirector
    ) {}

    public function execute(int $offerId): RedirectResponse
    {
        $this->offerRepository->delete($offerId);

        return $this->redirector->route('dashboard');
    }
}
