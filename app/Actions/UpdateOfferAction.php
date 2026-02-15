<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use App\Repositories\OfferRepository;
use App\Services\ImageStorage;

readonly class UpdateOfferAction
{
    public function __construct(
        private OfferRepository $offerRepository,
        private ImageStorage $imageStorage
    ) {}

    public function execute(UpdateOfferRequest $request, Offer $offer): void
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
    }
}
