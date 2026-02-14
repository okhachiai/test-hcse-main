<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Contracts\OfferRepositoryInterface;
use App\Http\Requests\UpdateOfferRequest;
use App\Infrastructure\Services\ImageStorage;
use App\Models\Offer;

readonly class UpdateOfferAction
{
    public function __construct(
        private OfferRepositoryInterface $offerRepository,
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
