<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use App\Repositories\OfferRepository;
use App\Services\ImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

readonly class UpdateOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository,
        private ImageStorage $imageStorage,
        private Redirector $redirector
    ) {}

    public function execute(UpdateOfferRequest $request, Offer $offer): RedirectResponse
    {
        $data = [
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug'),
            'description' => $request->validated('description'),
            'state' => $request->validated('state'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageStorage->replace($offer->image, $request->file('image'), 'offers');
        }

        $this->offerRepository->update($offer, $data);

        return $this->redirector->route('dashboard');
    }
}
